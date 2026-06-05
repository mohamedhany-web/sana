<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('instructor_profiles')) {
            Schema::table('instructor_profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('instructor_profiles', 'offers_tutor_booking')) {
                    $table->boolean('offers_tutor_booking')->default(false)->after('status');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_matching_modes')) {
                    $table->json('tutor_matching_modes')->nullable()->after('offers_tutor_booking');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_session_types')) {
                    $table->json('tutor_session_types')->nullable()->after('tutor_matching_modes');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_subject_ids')) {
                    $table->json('tutor_subject_ids')->nullable()->after('tutor_session_types');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_academic_year_ids')) {
                    $table->json('tutor_academic_year_ids')->nullable()->after('tutor_subject_ids');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_years_experience')) {
                    $table->unsignedSmallInteger('tutor_years_experience')->nullable()->after('tutor_academic_year_ids');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_default_duration_minutes')) {
                    $table->unsignedSmallInteger('tutor_default_duration_minutes')->default(60)->after('tutor_years_experience');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_onboarding_completed_at')) {
                    $table->timestamp('tutor_onboarding_completed_at')->nullable()->after('tutor_default_duration_minutes');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_trial_completed_at')) {
                    $table->timestamp('tutor_trial_completed_at')->nullable()->after('tutor_onboarding_completed_at');
                }
                if (! Schema::hasColumn('instructor_profiles', 'tutor_activated_at')) {
                    $table->timestamp('tutor_activated_at')->nullable()->after('tutor_trial_completed_at');
                }
            });
        }

        if (! Schema::hasTable('student_learning_profiles')) {
            Schema::create('student_learning_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                $table->json('subject_ids')->nullable();
                $table->string('curriculum_label', 120)->nullable();
                $table->string('grade_stage', 80)->nullable();
                $table->string('matching_mode', 32)->default('pick_teacher');
                $table->string('preferred_session_type', 32)->default('one_to_one');
                $table->unsignedInteger('lesson_hours_quota')->default(0);
                $table->unsignedInteger('lesson_hours_used')->default(0);
                $table->text('assessment_notes')->nullable();
                $table->timestamp('assessed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tutor_availabilities')) {
            Schema::create('tutor_availabilities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week');
                $table->time('start_time');
                $table->time('end_time');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['instructor_id', 'day_of_week', 'is_active'], 'tutor_avail_inst_day_idx');
            });
        }

        if (! Schema::hasTable('lesson_bookings')) {
            Schema::create('lesson_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('code', 16)->unique();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('requested_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('academic_subject_id')->nullable()->constrained('academic_subjects')->nullOnDelete();
                $table->unsignedBigInteger('tutor_assisted_request_id')->nullable();
                $table->string('matching_mode', 32);
                $table->string('session_type', 32)->default('one_to_one');
                $table->string('status', 32)->default('pending');
                $table->boolean('is_trial')->default(false);
                $table->timestamp('scheduled_at')->nullable();
                $table->unsignedSmallInteger('duration_minutes')->default(60);
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
                $table->text('student_notes')->nullable();
                $table->text('instructor_notes')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->string('cancelled_by', 20)->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->unsignedInteger('billable_minutes')->default(0);
                $table->timestamp('co_presence_started_at')->nullable();
                $table->timestamp('co_presence_ended_at')->nullable();
                $table->timestamps();
                $table->index(['instructor_id', 'status', 'scheduled_at']);
                $table->index(['student_id', 'status', 'scheduled_at']);
            });
        }

        if (! Schema::hasTable('tutor_assisted_requests')) {
            Schema::create('tutor_assisted_requests', function (Blueprint $table) {
                $table->id();
                $table->string('code', 16)->unique();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('requested_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->json('subject_ids')->nullable();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                $table->string('preferred_session_type', 32)->default('one_to_one');
                $table->text('message')->nullable();
                $table->string('status', 32)->default('open');
                $table->foreignId('assigned_instructor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('lesson_booking_id')->nullable()->constrained('lesson_bookings')->nullOnDelete();
                $table->timestamp('assigned_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->timestamps();
                $table->index(['status', 'created_at']);
            });
        }

        if (Schema::hasTable('lesson_bookings') && Schema::hasTable('tutor_assisted_requests')) {
            Schema::table('lesson_bookings', function (Blueprint $table) {
                $table->foreign('tutor_assisted_request_id')
                    ->references('id')
                    ->on('tutor_assisted_requests')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasTable('lesson_booking_ratings')) {
            Schema::create('lesson_booking_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_booking_id')->constrained('lesson_bookings')->cascadeOnDelete();
                $table->foreignId('rater_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('rated_user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->timestamps();
                $table->unique(['lesson_booking_id', 'rater_id'], 'lbr_booking_rater_unique');
            });
        }

        if (! Schema::hasTable('tutor_work_logs')) {
            Schema::create('tutor_work_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->date('work_date');
                $table->unsignedInteger('minutes')->default(0);
                $table->string('source', 32)->default('manual');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['instructor_id', 'work_date', 'source'], 'tutor_work_log_unique');
            });
        }

        if (Schema::hasTable('lesson_bookings') && Schema::hasTable('classroom_meetings')) {
            Schema::table('lesson_bookings', function (Blueprint $table) {
                $table->foreign('classroom_meeting_id')
                    ->references('id')
                    ->on('classroom_meetings')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('classroom_meetings') && ! Schema::hasColumn('classroom_meetings', 'lesson_booking_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->foreignId('lesson_booking_id')->nullable()->after('user_id')->constrained('lesson_bookings')->nullOnDelete();
            });
        }

        if (Schema::hasTable('classroom_meeting_participants')) {
            Schema::table('classroom_meeting_participants', function (Blueprint $table) {
                if (! Schema::hasColumn('classroom_meeting_participants', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('classroom_meeting_id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('classroom_meeting_participants', 'participant_role')) {
                    $table->string('participant_role', 20)->nullable()->after('user_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('classroom_meeting_participants')) {
            Schema::table('classroom_meeting_participants', function (Blueprint $table) {
                if (Schema::hasColumn('classroom_meeting_participants', 'participant_role')) {
                    $table->dropColumn('participant_role');
                }
                if (Schema::hasColumn('classroom_meeting_participants', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }
            });
        }

        if (Schema::hasTable('classroom_meetings') && Schema::hasColumn('classroom_meetings', 'lesson_booking_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->dropConstrainedForeignId('lesson_booking_id');
            });
        }

        Schema::dropIfExists('tutor_work_logs');
        Schema::dropIfExists('lesson_booking_ratings');
        Schema::dropIfExists('lesson_bookings');
        Schema::dropIfExists('tutor_assisted_requests');
        Schema::dropIfExists('tutor_availabilities');
        Schema::dropIfExists('student_learning_profiles');

        if (Schema::hasTable('instructor_profiles')) {
            Schema::table('instructor_profiles', function (Blueprint $table) {
                $cols = [
                    'offers_tutor_booking', 'tutor_matching_modes', 'tutor_session_types',
                    'tutor_subject_ids', 'tutor_academic_year_ids', 'tutor_years_experience',
                    'tutor_default_duration_minutes', 'tutor_onboarding_completed_at',
                    'tutor_trial_completed_at', 'tutor_activated_at',
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('instructor_profiles', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
