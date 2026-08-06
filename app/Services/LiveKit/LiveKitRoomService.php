<?php

namespace App\Services\LiveKit;

use App\Models\ClassroomMeeting;
use App\Models\CourseSection;
use App\Models\LiveSession;
use App\Support\PlatformBranding;
use InvalidArgumentException;

class LiveKitRoomService
{
    public function forMeeting(ClassroomMeeting $meeting): string
    {
        $name = trim((string) $meeting->room_name);
        if ($name === '') {
            $name = PlatformBranding::classroomRoomName((string) $meeting->code);
        }

        return $this->sanitize($name);
    }

    public function forLiveSession(LiveSession $session): string
    {
        $sectionId = (int) ($session->course_section_id ?? 0);
        if ($sectionId > 0) {
            return $this->forCourseSectionId($sectionId);
        }

        $name = trim((string) $session->room_name);
        if ($name === '') {
            $name = LiveSession::generateRoomName($session->title);
        }

        return $this->sanitize($name);
    }

    public function forCourseSection(CourseSection $section): string
    {
        return $this->forCourseSectionId((int) $section->id);
    }

    public function forCourseSectionId(int $sectionId): string
    {
        if ($sectionId < 1) {
            throw new InvalidArgumentException('Invalid course section id for LiveKit room.');
        }

        return $this->sanitize(PlatformBranding::roomPrefix().'-unit-'.$sectionId);
    }

    public function sanitize(string $roomName): string
    {
        $roomName = trim($roomName);
        $roomName = preg_replace('/\s+/', '-', $roomName) ?? $roomName;
        $roomName = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $roomName) ?? $roomName;

        if ($roomName === '') {
            throw new InvalidArgumentException('LiveKit room name cannot be empty.');
        }

        return $roomName;
    }
}
