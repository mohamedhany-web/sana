<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use Illuminate\Http\Request;

class ClassroomRecordingController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->get('status', 'all');
        if (!in_array($status, ['all', 'live', 'scheduled', 'ended'], true)) {
            $status = 'all';
        }

        $hasRecording = (string) $request->get('has_recording', 'all');
        if (!in_array($hasRecording, ['all', 'yes', 'no'], true)) {
            $hasRecording = 'all';
        }

        $search = trim((string) $request->get('search', ''));

        $query = ClassroomMeeting::query()->with(['user'])->latest();

        if ($status === 'live') {
            $query->whereNotNull('started_at')->whereNull('ended_at');
        } elseif ($status === 'scheduled') {
            $query->whereNull('started_at');
        } elseif ($status === 'ended') {
            $query->whereNotNull('ended_at');
        }

        if ($hasRecording === 'yes') {
            $query->where('recording_disk', 'live_recordings_r2')->whereNotNull('recording_path');
        } elseif ($hasRecording === 'no') {
            $query->where(function ($q) {
                $q->whereNull('recording_path')->orWhere('recording_disk', '!=', 'live_recordings_r2');
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('room_name', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        $meetings = $query->paginate(20)->withQueryString();

        return view('admin.classroom-recordings.index', compact('meetings', 'status', 'hasRecording', 'search'));
    }
}
