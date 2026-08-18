<?php

namespace Tests\Unit;

use App\Models\InstructorProfile;
use App\Models\StudentLearningProfile;
use Tests\TestCase;

class InstructorSessionTypesTest extends TestCase
{
    public function test_empty_or_invalid_types_default_to_one_to_one(): void
    {
        $this->assertSame(
            [StudentLearningProfile::SESSION_ONE_TO_ONE],
            InstructorProfile::normalizeSessionTypes(null)
        );
        $this->assertSame(
            [StudentLearningProfile::SESSION_ONE_TO_ONE],
            InstructorProfile::normalizeSessionTypes([])
        );
        $this->assertSame(
            [StudentLearningProfile::SESSION_ONE_TO_ONE],
            InstructorProfile::normalizeSessionTypes(['recorded_courses', 'exam_reviews'])
        );
    }

    public function test_nested_and_alias_values_are_normalized(): void
    {
        $this->assertSame(
            [StudentLearningProfile::SESSION_ONE_TO_ONE, StudentLearningProfile::SESSION_SMALL_GROUP],
            InstructorProfile::normalizeSessionTypes([['one_to_one'], 'small-group'])
        );
        $this->assertSame(
            [StudentLearningProfile::SESSION_ONE_TO_ONE],
            InstructorProfile::normalizeSessionTypes(['individual', 'recorded_courses'])
        );
    }

    public function test_profile_accepts_one_to_one_when_session_types_are_missing(): void
    {
        $profile = new InstructorProfile(['tutor_session_types' => []]);

        $this->assertTrue($profile->supportsSessionType('one_to_one'));
        $this->assertSame('one_to_one', $profile->resolveSessionType('small_group'));
    }

    public function test_preferred_group_falls_back_when_teacher_only_offers_one_to_one(): void
    {
        $profile = new InstructorProfile(['tutor_session_types' => ['one_to_one']]);

        $this->assertSame('one_to_one', $profile->resolveSessionType('small_group'));
        $this->assertFalse($profile->supportsSessionType('small_group'));
    }
}
