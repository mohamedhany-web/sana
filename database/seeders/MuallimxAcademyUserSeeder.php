<?php

namespace Database\Seeders;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MuallimxAcademyUserSeeder extends Seeder
{
    /**
     * بيانات مستخدمي منصة Muallimx — تأهيل المعلمين للعمل أونلاين
     */
    public function run(): void
    {
        if (!Schema::hasTable('users')) {
            $this->command->warn('⚠️  جدول users غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        $password = Hash::make('password123');

        // ─── مدير المنصة (Super Admin) ───
        $this->upsertUser('admin@Muallimx.com', '0500000000', [
            'name' => 'مدير المنصة',
            'password' => $password,
            'role' => 'super_admin',
            'is_active' => true,
            'bio' => 'مدير المنصة — مسؤول عن إدارة أكاديمية تأهيل المعلمين للعمل أونلاين.',
        ]);

        // ─── مدير أكاديمي (Super Admin ثاني — صلاحيات إدارية)
        $this->upsertUser('academy@Muallimx.com', '0500000001', [
            'name' => 'سارة المديرة الأكاديمية',
            'password' => $password,
            'role' => 'super_admin',
            'is_active' => true,
            'bio' => 'مديرة أكاديمية — متابعة البرامج التدريبية والشهادات والتوظيف.',
        ]);

        // ─── مدربون (Instructors) ───
        $instructors = [
            [
                'email' => 'instructor1@Muallimx.com',
                'phone' => '0500000010',
                'name' => 'د. أحمد الشمري',
                'bio' => 'مدرب معتمد في التدريس أونلاين — خبرة 12 سنة. متخصص في تصميم الحصص التفاعلية واستخدام أدوات التعلم الرقمي.',
            ],
            [
                'email' => 'instructor2@Muallimx.com',
                'phone' => '0500000011',
                'name' => 'نورة العتيبي',
                'bio' => 'معلمة لغة عربية أونلاين — تدريب المعلمين على تقديم حصص افتراضية احترافية وبناء البروفايل المهني.',
            ],
            [
                'email' => 'instructor3@Muallimx.com',
                'phone' => '0500000012',
                'name' => 'محمد المنصوري',
                'bio' => 'خبير في أدوات الذكاء الاصطناعي للمعلمين — ورش عملية على تحضير الدروس والأنشطة باستخدام AI.',
            ],
            [
                'email' => 'instructor4@Muallimx.com',
                'phone' => '0500000013',
                'name' => 'هدى الكويتية',
                'bio' => 'مدربة في التسويق للمعلمين والعمل بالدولار — مسارات تعلم للوصول لفرص عمل دولية.',
            ],
        ];

        foreach ($instructors as $data) {
            $user = $this->upsertUser($data['email'], $data['phone'], [
                'name' => $data['name'],
                'password' => $password,
                'role' => 'instructor',
                'is_active' => true,
                'bio' => $data['bio'],
            ]);

            // إنشاء ملف تعريفي للمدرب إذا كان الجدول موجوداً
            if (Schema::hasTable('instructor_profiles') && !$user->instructorProfile) {
                InstructorProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'headline' => $data['bio'],
                        'bio' => $data['bio'],
                        'experience' => 'خبرة في التدريب وتأهيل المعلمين',
                        'skills' => 'التدريس أونلاين، أدوات رقمية، تصميم الدروس',
                        'status' => InstructorProfile::STATUS_APPROVED,
                        'submitted_at' => now(),
                        'reviewed_at' => now(),
                    ]
                );
            }
        }

        // ─── طلاب / معلمون متدربون (Students) ───
        $students = [
            ['email' => 'student1@Muallimx.com', 'phone' => '0500000020', 'name' => 'فاطمة الزهراء'],
            ['email' => 'student2@Muallimx.com', 'phone' => '0500000021', 'name' => 'عمر الطالب'],
            ['email' => 'student3@Muallimx.com', 'phone' => '0500000022', 'name' => 'مريم المعلمة المتدربة'],
            ['email' => 'student4@Muallimx.com', 'phone' => '0500000023', 'name' => 'خالد السعيد'],
            ['email' => 'student5@Muallimx.com', 'phone' => '0500000024', 'name' => 'لينا أحمد'],
            ['email' => 'student6@Muallimx.com', 'phone' => '0500000025', 'name' => 'يوسف المعلم'],
        ];

        foreach ($students as $data) {
            $this->upsertUser($data['email'], $data['phone'], [
                'name' => $data['name'],
                'password' => $password,
                'role' => 'student',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ تم إنشاء مستخدمي أكاديمية Muallimx بنجاح.');
        $this->command->newLine();
        $this->command->info('📋 بيانات الدخول (كلمة المرور لجميع الحسابات: password123)');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👨‍💼 مدير المنصة:     admin@Muallimx.com     — 0500000000');
        $this->command->info('👩‍💼 مديرة أكاديمية: academy@Muallimx.com   — 0500000001');
        $this->command->info('👨‍🏫 مدربون:          instructor1@Muallimx.com … instructor4@Muallimx.com');
        $this->command->info('👩‍🎓 طلاب:            student1@Muallimx.com … student6@Muallimx.com');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * تحديث أو إنشاء مستخدم بالبريد أو الهاتف (يتجنب تعارض unique على phone).
     */
    private function upsertUser(string $email, string $phone, array $attributes): User
    {
        $user = User::query()
            ->where('email', $email)
            ->orWhere('phone', $phone)
            ->first();

        $payload = array_merge($attributes, [
            'email' => $email,
            'phone' => $phone,
        ]);

        if ($user) {
            $user->update($payload);

            return $user->fresh();
        }

        return User::create($payload);
    }
}
