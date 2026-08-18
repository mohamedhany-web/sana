<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Webhook for LiveKit Egress (and legacy Jibri) after upload to R2.
 * Header: X-Webhook-Token
 */
class LiveRecordingWebhookController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $token = config('services.live_recordings_webhook.token');
        if (empty($token) || $request->header('X-Webhook-Token') !== $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // LiveKit egress style payload
        if ($request->filled('egress_id') || $request->input('provider') === 'livekit') {
            return $this->registerLiveKit($request);
        }

        $validated = $request->validate([
            'session_id' => 'required|exists:live_sessions,id',
            'file_path' => 'required|string|max:500',
            'title' => 'nullable|string|max:255',
            'duration_seconds' => 'nullable|integer|min:0',
            'file_size' => 'nullable|integer|min:0',
        ]);

        $session = LiveSession::find($validated['session_id']);
        $title = $validated['title'] ?? ('تسجيل '.$session->title);

        $rec = LiveRecording::create([
            'session_id' => $validated['session_id'],
            'title' => $title,
            'file_path' => $validated['file_path'],
            'storage_disk' => 'r2',
            'file_size' => $validated['file_size'] ?? 0,
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'status' => 'ready',
            'is_published' => false,
        ]);

        return response()->json([
            'success' => true,
            'recording_id' => $rec->id,
            'message' => 'تم تسجيل التسجيل بنجاح. يمكنك نشره من لوحة الإدارة.',
        ], 201);
    }

    protected function registerLiveKit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'nullable|exists:live_sessions,id',
            'room_name' => 'nullable|string|max:255',
            'file_path' => 'required|string|max:500',
            'title' => 'nullable|string|max:255',
            'duration_seconds' => 'nullable|integer|min:0',
            'file_size' => 'nullable|integer|min:0',
            'egress_id' => 'nullable|string|max:120',
        ]);

        $session = null;
        if (! empty($validated['session_id'])) {
            $session = LiveSession::find($validated['session_id']);
        } elseif (! empty($validated['room_name'])) {
            $session = LiveSession::where('room_name', $validated['room_name'])->latest('id')->first();
        }

        if (! $session) {
            $marked = app(\App\Services\LessonRecordingService::class)->markFromWebhook(
                $validated['egress_id'] ?? null,
                $validated['room_name'] ?? null,
                $validated['file_path'],
                (int) ($validated['file_size'] ?? 0),
                (int) ($validated['duration_seconds'] ?? 0)
            );
            if ($marked) {
                return response()->json([
                    'success' => true,
                    'provider' => 'livekit',
                    'lesson_recording_id' => $marked->id,
                    'egress_id' => $validated['egress_id'] ?? null,
                    'message' => 'Lesson recording registered.',
                ], 201);
            }

            return response()->json(['error' => 'Live session not found for recording'], 422);
        }

        $title = $validated['title'] ?? ('تسجيل LiveKit — '.$session->title);

        $rec = LiveRecording::create([
            'session_id' => $session->id,
            'title' => $title,
            'file_path' => $validated['file_path'],
            'storage_disk' => 'r2',
            'file_size' => $validated['file_size'] ?? 0,
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'status' => 'ready',
            'is_published' => false,
        ]);

        return response()->json([
            'success' => true,
            'provider' => 'livekit',
            'recording_id' => $rec->id,
            'egress_id' => $validated['egress_id'] ?? null,
            'message' => 'LiveKit recording registered.',
        ], 201);
    }
}
