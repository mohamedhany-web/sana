<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lesson_bookings') && ! Schema::hasColumn('lesson_bookings', 'billable_seconds')) {
            Schema::table('lesson_bookings', function (Blueprint $table) {
                $table->unsignedInteger('billable_seconds')->default(0)->after('billable_minutes');
            });
        }

        if (Schema::hasTable('classroom_meetings') && ! Schema::hasColumn('classroom_meetings', 'recording_egress_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->string('recording_egress_id', 120)->nullable()->after('recording_uploaded_at');
            });
        }

        if (! Schema::hasTable('lesson_session_recordings')) {
            Schema::create('lesson_session_recordings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('classroom_meeting_id')->index();
                $table->unsignedBigInteger('lesson_booking_id')->nullable()->index();
                $table->unsignedBigInteger('student_id')->nullable()->index();
                $table->unsignedBigInteger('instructor_id')->nullable()->index();
                $table->string('egress_id', 120)->nullable()->unique();
                $table->string('status', 32)->default('starting');
                $table->string('disk', 64)->nullable();
                $table->string('file_path', 500)->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->unsignedInteger('duration_seconds')->default(0);
                $table->string('mime_type', 80)->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamp('uploaded_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_session_recordings');

        if (Schema::hasTable('classroom_meetings') && Schema::hasColumn('classroom_meetings', 'recording_egress_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->dropColumn('recording_egress_id');
            });
        }

        if (Schema::hasTable('lesson_bookings') && Schema::hasColumn('lesson_bookings', 'billable_seconds')) {
            Schema::table('lesson_bookings', function (Blueprint $table) {
                $table->dropColumn('billable_seconds');
            });
        }
    }
};
