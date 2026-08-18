<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * الصفحة الرئيسية تحتاج جداول قاعدة البيانات؛ إعداد الاختبارات هنا sqlite في الذاكرة بدون migrate.
     */
    public function test_the_application_boots_and_core_routes_exist(): void
    {
        $this->assertNotEmpty(config('app.name'));
        $this->assertTrue(Route::has('home'));
        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('student.tutor-lessons.teachers'));
        $this->assertTrue(Route::has('student.play.activities'));
        $this->assertTrue(Route::has('student.play.challenges'));
        $this->assertTrue(Route::has('student.play.rewards'));
        $this->assertTrue(Route::has('classroom.join'));
        $this->assertTrue(Route::has('classroom.join.enter'));
        $this->assertTrue(Route::has('classroom.join.heartbeat'));
        $this->assertTrue(Route::has('classroom.join.leave'));
        $this->assertTrue(Route::has('api.livekit.webhook'));
        $this->assertTrue(Route::has('student.live-recordings.lesson'));
        $this->assertTrue(Route::has('instructor.classroom.heartbeat'));
        $this->assertTrue(Route::has('instructor.classroom.leave-presence'));
        $this->assertTrue(Route::has('admin.classroom.heartbeat'));
    }
}
