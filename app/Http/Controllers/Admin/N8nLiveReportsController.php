<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeetingReport;
use App\Models\LiveSessionReport;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class N8nLiveReportsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $instructorId = $request->input('instructor_id');
        $source = $request->input('source');

        $items = collect();

        if (! $source || $source === 'live_session') {
            $items = $items->concat($this->fetchLiveSessionReports($status, $instructorId));
        }

        if (! $source || $source === 'classroom_meeting') {
            $items = $items->concat($this->fetchClassroomMeetingReports($status, $instructorId));
        }

        $items = $items->sortByDesc('sort_at')->values();

        $perPage = 25;
        $page = max(1, (int) $request->input('page', 1));
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        $reports = new LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.n8n.live-session-reports.index', compact(
            'reports',
            'status',
            'instructorId',
            'source'
        ));
    }

    public function show(string $source, int $report)
    {
        $reportView = match ($source) {
            'live_session' => $this->mapLiveSessionReport(
                LiveSessionReport::with(['session', 'instructor', 'recording'])->findOrFail($report)
            ),
            'classroom_meeting' => $this->mapClassroomMeetingReport(
                ClassroomMeetingReport::with(['meeting', 'user'])->findOrFail($report)
            ),
            default => abort(404),
        };

        return view('admin.n8n.live-session-reports.show', compact('reportView', 'source'));
    }

    /**
     * @return Collection<int, object>
     */
    private function fetchLiveSessionReports(?string $status, ?string $instructorId): Collection
    {
        $query = LiveSessionReport::with(['session', 'instructor', 'recording'])->latest('updated_at');

        if ($status && in_array($status, ['pending', 'processing', 'completed', 'failed'], true)) {
            $query->where('status', $status);
        }

        if ($instructorId) {
            $query->where('instructor_id', (int) $instructorId);
        }

        return $query->get()->map(fn (LiveSessionReport $report) => $this->mapLiveSessionReport($report));
    }

    private function mapLiveSessionReport(LiveSessionReport $report): object
    {
        return (object) [
            'id' => $report->id,
            'source' => 'live_session',
            'source_label' => 'بث مباشر',
            'context_title' => $report->session?->title,
            'context_id' => $report->live_session_id,
            'user' => $report->instructor,
            'user_id' => $report->instructor_id,
            'status' => $report->status,
            'title' => $report->title,
            'summary' => $report->summary,
            'media_url' => $report->recording?->getUrl(),
            'audio_path' => $report->audio_path,
            'n8n_execution_id' => $report->n8n_execution_id,
            'sort_at' => $report->updated_at,
            'updated_at' => $report->updated_at,
            'created_at' => $report->created_at,
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function fetchClassroomMeetingReports(?string $status, ?string $instructorId): Collection
    {
        $query = ClassroomMeetingReport::with(['meeting', 'user'])->latest('updated_at');

        if ($status && in_array($status, ['pending', 'processing', 'completed', 'failed'], true)) {
            $query->where('status', $status);
        }

        if ($instructorId) {
            $query->where('user_id', (int) $instructorId);
        }

        return $query->get()->map(fn (ClassroomMeetingReport $report) => $this->mapClassroomMeetingReport($report));
    }

    private function mapClassroomMeetingReport(ClassroomMeetingReport $report): object
    {
        $meeting = $report->meeting;

        return (object) [
            'id' => $report->id,
            'source' => 'classroom_meeting',
            'source_label' => 'اجتماع Classroom',
            'context_title' => $meeting?->title,
            'context_id' => $report->classroom_meeting_id,
            'user' => $report->user,
            'user_id' => $report->user_id,
            'status' => $report->status,
            'title' => $report->title,
            'summary' => $report->summary,
            'media_url' => $meeting?->recording_audio_download_url ?? $meeting?->recording_download_url,
            'audio_path' => $report->audio_path,
            'n8n_execution_id' => $report->n8n_execution_id,
            'sort_at' => $report->updated_at,
            'updated_at' => $report->updated_at,
            'created_at' => $report->created_at,
        ];
    }
}
