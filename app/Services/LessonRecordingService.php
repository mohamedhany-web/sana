<?php

namespace App\Services;

use Agence104\LiveKit\EgressServiceClient;
use App\Models\ClassroomMeeting;
use App\Models\LessonSessionRecording;
use App\Services\LiveKit\LiveKitRoomService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livekit\EncodedFileOutput;
use Livekit\EncodedFileType;
use Livekit\S3Upload;
use Throwable;

/**
 * تسجيل إجباري لحصة المعلم/الطالب عبر LiveKit Egress → Cloudflare R2.
 * كل ميتينج له مسار وegress مستقل حتى يعمل عشرات المعلمين معاً بدون تعارض.
 */
class LessonRecordingService
{
    public function startForMeeting(ClassroomMeeting $meeting): ?LessonSessionRecording
    {
        if (! LessonMeetingAccess::isLessonMeeting($meeting) || $meeting->ended_at) {
            return null;
        }

        if (! Schema::hasTable('lesson_session_recordings')) {
            return null;
        }

        $lock = Cache::lock('lesson-rec-start-'.$meeting->id, 20);
        if (! $lock->get()) {
            return LessonSessionRecording::query()
                ->where('classroom_meeting_id', $meeting->id)
                ->latest('id')
                ->first();
        }

        try {
            $active = LessonSessionRecording::query()
                ->where('classroom_meeting_id', $meeting->id)
                ->whereIn('status', [
                    LessonSessionRecording::STATUS_STARTING,
                    LessonSessionRecording::STATUS_RECORDING,
                    LessonSessionRecording::STATUS_UPLOADING,
                    LessonSessionRecording::STATUS_READY,
                ])
                ->latest('id')
                ->first();
            if ($active) {
                return $active;
            }

            $failed = LessonSessionRecording::query()
                ->where('classroom_meeting_id', $meeting->id)
                ->where('status', LessonSessionRecording::STATUS_FAILED)
                ->latest('id')
                ->first();
            if ($failed && $failed->updated_at && $failed->updated_at->gt(now()->subSeconds(45))) {
                return $failed;
            }

            $booking = LessonMeetingAccess::bookingsFor($meeting)->first();
            $path = $failed?->file_path ?: $this->uniqueObjectPath($meeting, $booking?->id);

            $row = $failed ?: LessonSessionRecording::create([
                'classroom_meeting_id' => $meeting->id,
                'lesson_booking_id' => $booking?->id,
                'student_id' => $booking?->student_id,
                'instructor_id' => $booking?->instructor_id ?? $meeting->user_id,
                'status' => LessonSessionRecording::STATUS_STARTING,
                'disk' => 'live_recordings_r2',
                'file_path' => $path,
                'mime_type' => 'video/mp4',
                'started_at' => now(),
            ]);

            if ($failed) {
                $row->update([
                    'status' => LessonSessionRecording::STATUS_STARTING,
                    'file_path' => $path,
                    'error_message' => null,
                    'started_at' => $row->started_at ?? now(),
                ]);
            }

            try {
                $egressId = $this->startEgress($meeting, $path);
                $row->update([
                    'egress_id' => $egressId,
                    'status' => LessonSessionRecording::STATUS_RECORDING,
                ]);
                if (Schema::hasColumn('classroom_meetings', 'recording_egress_id')) {
                    $meeting->update(['recording_egress_id' => $egressId]);
                }
            } catch (Throwable $e) {
                Log::error('Lesson recording egress failed to start', [
                    'meeting_id' => $meeting->id,
                    'message' => $e->getMessage(),
                ]);
                $row->update([
                    'status' => LessonSessionRecording::STATUS_FAILED,
                    'error_message' => mb_substr($e->getMessage(), 0, 2000),
                ]);
            }

            return $row->fresh();
        } finally {
            $lock->release();
        }
    }

    public function stopForMeeting(ClassroomMeeting $meeting): void
    {
        if (! Schema::hasTable('lesson_session_recordings')) {
            return;
        }

        $rows = LessonSessionRecording::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->whereIn('status', [
                LessonSessionRecording::STATUS_STARTING,
                LessonSessionRecording::STATUS_RECORDING,
            ])
            ->get();

        foreach ($rows as $row) {
            if ($row->egress_id) {
                try {
                    $this->client()->stopEgress($row->egress_id);
                } catch (Throwable $e) {
                    Log::warning('Lesson recording egress stop failed', [
                        'meeting_id' => $meeting->id,
                        'egress_id' => $row->egress_id,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
            $row->update([
                'status' => LessonSessionRecording::STATUS_UPLOADING,
                'ended_at' => $row->ended_at ?? now(),
            ]);
        }
    }

    public function markReady(
        LessonSessionRecording $row,
        ?string $filePath = null,
        int $fileSize = 0,
        int $durationSeconds = 0
    ): void {
        $path = $filePath ?: $row->file_path;
        $row->update([
            'status' => LessonSessionRecording::STATUS_READY,
            'file_path' => $path,
            'file_size' => $fileSize > 0 ? $fileSize : $row->file_size,
            'duration_seconds' => $durationSeconds > 0 ? $durationSeconds : $row->duration_seconds,
            'ended_at' => $row->ended_at ?? now(),
            'uploaded_at' => now(),
            'error_message' => null,
        ]);

        $meeting = $row->meeting;
        if ($meeting && $path) {
            $meeting->update([
                'recording_disk' => 'live_recordings_r2',
                'recording_path' => $path,
                'recording_mime_type' => $row->mime_type ?: 'video/mp4',
                'recording_size' => $fileSize > 0 ? $fileSize : $meeting->recording_size,
                'recording_duration_seconds' => $durationSeconds > 0 ? $durationSeconds : $meeting->recording_duration_seconds,
                'recording_uploaded_at' => now(),
            ]);
        }
    }

    public function markFromWebhook(?string $egressId, ?string $roomName, ?string $filePath, int $fileSize = 0, int $duration = 0, ?string $error = null): ?LessonSessionRecording
    {
        if (! Schema::hasTable('lesson_session_recordings')) {
            return null;
        }

        $row = null;
        if ($egressId) {
            $row = LessonSessionRecording::query()->where('egress_id', $egressId)->first();
        }
        if (! $row && $roomName) {
            $meeting = ClassroomMeeting::query()->where('room_name', $roomName)->latest('id')->first();
            if ($meeting) {
                $row = LessonSessionRecording::query()
                    ->where('classroom_meeting_id', $meeting->id)
                    ->latest('id')
                    ->first();
            }
        }
        if (! $row) {
            return null;
        }

        if ($error || ($filePath === null && $fileSize <= 0)) {
            $row->update([
                'status' => LessonSessionRecording::STATUS_FAILED,
                'error_message' => mb_substr((string) ($error ?: 'LiveKit egress ended without a file'), 0, 2000),
                'ended_at' => $row->ended_at ?? now(),
            ]);

            return $row->fresh();
        }

        $this->markReady($row, $filePath, $fileSize, $duration);

        return $row->fresh();
    }

    /**
     * احتياطي: رفع تسجيل المتصفح إذا فشل Egress — لا يستبدل تسجيلاً جاهزاً من السيرفر.
     */
    public function attachBrowserUpload(
        ClassroomMeeting $meeting,
        string $path,
        int $fileSize = 0,
        int $durationSeconds = 0,
        ?string $mime = null
    ): ?LessonSessionRecording {
        if (! LessonMeetingAccess::isLessonMeeting($meeting) || ! Schema::hasTable('lesson_session_recordings')) {
            return null;
        }

        $existing = LessonSessionRecording::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->where('status', LessonSessionRecording::STATUS_READY)
            ->latest('id')
            ->first();
        if ($existing) {
            return $existing;
        }

        $booking = LessonMeetingAccess::bookingsFor($meeting)->first();
        $row = LessonSessionRecording::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->whereIn('status', [
                LessonSessionRecording::STATUS_STARTING,
                LessonSessionRecording::STATUS_RECORDING,
                LessonSessionRecording::STATUS_UPLOADING,
                LessonSessionRecording::STATUS_FAILED,
            ])
            ->latest('id')
            ->first();

        $payload = [
            'classroom_meeting_id' => $meeting->id,
            'lesson_booking_id' => $booking?->id,
            'student_id' => $booking?->student_id,
            'instructor_id' => $booking?->instructor_id ?? $meeting->user_id,
            'status' => LessonSessionRecording::STATUS_READY,
            'disk' => 'live_recordings_r2',
            'file_path' => $path,
            'file_size' => $fileSize,
            'duration_seconds' => $durationSeconds,
            'mime_type' => $mime ?: 'video/webm',
            'started_at' => $row?->started_at ?? $meeting->started_at ?? now(),
            'ended_at' => now(),
            'uploaded_at' => now(),
            'error_message' => null,
        ];

        if ($row) {
            $row->update($payload);
        } else {
            $row = LessonSessionRecording::create($payload);
        }

        return $row->fresh();
    }

    protected function startEgress(ClassroomMeeting $meeting, string $filepath): string
    {
        $s3 = new S3Upload([
            'access_key' => (string) config('filesystems.disks.live_recordings_r2.key'),
            'secret' => (string) config('filesystems.disks.live_recordings_r2.secret'),
            'region' => (string) (config('filesystems.disks.live_recordings_r2.region') ?: 'auto'),
            'endpoint' => (string) config('filesystems.disks.live_recordings_r2.endpoint'),
            'bucket' => (string) config('filesystems.disks.live_recordings_r2.bucket'),
            'force_path_style' => true,
        ]);

        $output = new EncodedFileOutput([
            'file_type' => EncodedFileType::MP4,
            'filepath' => $filepath,
            's3' => $s3,
        ]);

        $room = app(LiveKitRoomService::class)->forMeeting($meeting);
        $info = $this->client()->startRoomCompositeEgress($room, 'grid', $output);

        $egressId = method_exists($info, 'getEgressId') ? (string) $info->getEgressId() : '';
        if ($egressId === '') {
            throw new \RuntimeException('LiveKit لم يُرجع معرف egress.');
        }

        return $egressId;
    }

    protected function uniqueObjectPath(ClassroomMeeting $meeting, ?int $bookingId): string
    {
        return sprintf(
            'lesson-recordings/%s/m%s-i%s-b%s-%s.mp4',
            now()->format('Y/m'),
            $meeting->id,
            (int) $meeting->user_id,
            $bookingId ?: 0,
            bin2hex(random_bytes(8))
        );
    }

    protected function client(): EgressServiceClient
    {
        $host = (string) (config('livekit.public_url') ?: config('livekit.url'));
        $host = str_replace(['wss://', 'ws://'], ['https://', 'http://'], $host);
        $host = rtrim($host, '/');

        $key = (string) config('livekit.api_key');
        $secret = (string) config('livekit.api_secret');
        if ($key === '' || $secret === '' || $host === '') {
            throw new \RuntimeException('مفاتيح LiveKit غير مضبوطة لتسجيل الحصص.');
        }

        return new EgressServiceClient($host, $key, $secret);
    }
}
