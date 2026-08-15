<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'title',
        'message',
        'type',
        'action_url',
        'action_text',
        'priority',
        'target_type',
        'target_id',
        'audience',
        'is_read',
        'read_at',
        'expires_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * علاقة مع المستخدم المستقبل
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع المرسل
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * أنواع الإشعارات المتاحة
     */
    public static function getTypes()
    {
        return [
            'general' => 'عام',
            'course' => 'كورس',
            'exam' => 'امتحان',
            'assignment' => 'واجب',
            'grade' => 'درجة',
            'announcement' => 'إعلان',
            'reminder' => 'تذكير',
            'warning' => 'تحذير',
            'system' => 'نظام',
            'employee' => 'موظف',
            'instructor' => 'مدرب',
        ];
    }

    /**
     * مستويات الأولوية
     */
    public static function getPriorities()
    {
        return [
            'low' => 'منخفضة',
            'normal' => 'عادية',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
        ];
    }

    /**
     * شرائح الجمهور في مركز الإشعارات الموحّد
     */
    public static function getAudiences(): array
    {
        return [
            'student' => 'الطلاب',
            'instructor' => 'المدربون',
            'employee' => 'الموظفون',
        ];
    }

    /**
     * أهداف الإشعارات (طلاب + مدربون + موظفون)
     */
    public static function getTargetTypes()
    {
        return [
            'all_students' => 'جميع الطلاب',
            'course_students' => 'طلاب كورس معين',
            'year_students' => 'طلاب سنة دراسية',
            'subject_students' => 'طلاب مادة معينة',
            'individual' => 'طالب محدد',
            'all_instructors' => 'جميع المدربين',
            'individual_instructor' => 'مدربون محددون',
            'incomplete_instructors' => 'مدربون لم يكملوا بيانات الانضمام',
            'all_employees' => 'جميع الموظفين',
            'individual_employee' => 'موظف محدد',
        ];
    }

    public static function audienceForTargetType(string $targetType): string
    {
        return match ($targetType) {
            'all_instructors', 'individual_instructor', 'incomplete_instructors' => 'instructor',
            'all_employees', 'individual_employee' => 'employee',
            default => 'student',
        };
    }

    /**
     * بريد يمكن الرد عليه من المنصة (رسالة تواصل / بيانات محفوظة / بريد المستخدم المرتبط).
     *
     * @return array{email: string, name: ?string, source: string}|null
     */
    public function resolveReplyEmail(): ?array
    {
        $data = is_array($this->data) ? $this->data : [];

        if (! empty($data['reply_email']) && filter_var($data['reply_email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'email' => (string) $data['reply_email'],
                'name' => $data['reply_name'] ?? null,
                'source' => (string) ($data['source'] ?? 'notification'),
            ];
        }

        if (! empty($data['contact_message_id'])) {
            $contact = ContactMessage::find($data['contact_message_id']);
            if ($contact && filter_var($contact->email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'email' => (string) $contact->email,
                    'name' => $contact->name,
                    'source' => 'contact_page',
                ];
            }
        }

        if ($this->sender && filter_var($this->sender->email, FILTER_VALIDATE_EMAIL)) {
            return [
                'email' => (string) $this->sender->email,
                'name' => $this->sender->name,
                'source' => 'sender',
            ];
        }

        if ($this->user && filter_var($this->user->email, FILTER_VALIDATE_EMAIL) && (int) $this->user_id !== (int) auth()->id()) {
            return [
                'email' => (string) $this->user->email,
                'name' => $this->user->name,
                'source' => 'recipient',
            ];
        }

        return null;
    }

    public function emailReplies()
    {
        return $this->hasMany(NotificationEmailReply::class)->orderByDesc('created_at');
    }

    /**
     * scope للإشعارات غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * scope للإشعارات المقروءة
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * scope للإشعارات حسب النوع
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * scope للإشعارات حسب الأولوية
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * scope للإشعارات غير المنتهية الصلاحية
     */
    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * التحقق من انتهاء صلاحية الإشعار
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * الحصول على لون الأولوية
     */
    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'gray',
            'normal' => 'blue',
            'high' => 'yellow',
            'urgent' => 'red',
        ];

        return $colors[$this->priority] ?? 'blue';
    }

    /**
     * الحصول على أيقونة النوع
     */
    public function getTypeIconAttribute()
    {
        $icons = [
            'general' => 'fas fa-info-circle',
            'course' => 'fas fa-graduation-cap',
            'exam' => 'fas fa-clipboard-check',
            'assignment' => 'fas fa-tasks',
            'grade' => 'fas fa-star',
            'announcement' => 'fas fa-bullhorn',
            'reminder' => 'fas fa-bell',
            'warning' => 'fas fa-exclamation-triangle',
            'employee' => 'fas fa-user-tie',
            'instructor' => 'fas fa-chalkboard-teacher',
            'system' => 'fas fa-cog',
        ];

        return $icons[$this->type] ?? 'fas fa-info-circle';
    }

    /**
     * الحصول على لون النوع
     */
    public function getTypeColorAttribute()
    {
        $colors = [
            'general' => 'blue',
            'course' => 'green',
            'exam' => 'purple',
            'assignment' => 'orange',
            'grade' => 'yellow',
            'announcement' => 'red',
            'reminder' => 'blue',
            'warning' => 'red',
            'employee' => 'indigo',
            'instructor' => 'teal',
            'system' => 'gray',
        ];

        return $colors[$this->type] ?? 'blue';
    }

    /**
     * تحضير صف للإدراج الجماعي — insert() لا يمرّ عبر casts.
     */
    protected static function prepareInsertPayload(array $data): array
    {
        if (array_key_exists('data', $data) && is_array($data['data'])) {
            $data['data'] = json_encode($data['data'], JSON_UNESCAPED_UNICODE);
        }

        return $data;
    }

    /**
     * إرسال إشعار لمستخدم واحد
     */
    public static function sendToUser($userId, $data)
    {
        return self::create(array_merge($data, ['user_id' => $userId]));
    }

    /**
     * إرسال إشعار لمجموعة مستخدمين
     */
    public static function sendToUsers($userIds, $data)
    {
        $payload = self::prepareInsertPayload($data);
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = array_merge($payload, [
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (empty($notifications)) {
            return 0;
        }

        return self::insert($notifications);
    }

    /**
     * إرسال إشعار لجميع الطلاب
     */
    public static function sendToAllStudents($data)
    {
        $studentIds = User::where('role', 'student')->where('is_active', true)->pluck('id');
        return self::sendToUsers($studentIds, $data);
    }

    /**
     * إرسال إشعار لطلاب كورس معين
     */
    public static function sendToCourseStudents($courseId, $data)
    {
        $studentIds = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $courseId)
                                                        ->where('status', 'active')
                                                        ->pluck('user_id');
        return self::sendToUsers($studentIds, $data);
    }

    /**
     * إرسال إشعار لطلاب سنة دراسية
     */
    public static function sendToYearStudents($yearId, $data)
    {
        $courseIds = \App\Models\AdvancedCourse::where('academic_year_id', $yearId)->pluck('id');
        $studentIds = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                        ->where('status', 'active')
                                                        ->pluck('user_id')
                                                        ->unique();
        return self::sendToUsers($studentIds, $data);
    }

    /**
     * إرسال إشعار لطلاب مادة معينة
     */
    public static function sendToSubjectStudents($subjectId, $data)
    {
        $courseIds = \App\Models\AdvancedCourse::where('academic_subject_id', $subjectId)->pluck('id');
        $studentIds = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                        ->where('status', 'active')
                                                        ->pluck('user_id')
                                                        ->unique();
        return self::sendToUsers($studentIds, $data);
    }

    /**
     * إرسال إشعار لموظف معين
     */
    public static function sendToEmployee($employeeId, $data)
    {
        $user = User::find($employeeId);
        if (!$user || !$user->is_employee) {
            return 0;
        }

        $notification = self::create(array_merge($data, [
            'user_id' => $employeeId,
        ]));

        return $notification ? 1 : 0;
    }

    /**
     * إرسال إشعار لمجموعة موظفين
     */
    public static function sendToEmployees($employeeIds, $data)
    {
        $payload = self::prepareInsertPayload($data);
        $notifications = [];
        foreach ($employeeIds as $employeeId) {
            $notifications[] = array_merge($payload, [
                'user_id' => $employeeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (empty($notifications)) {
            return 0;
        }

        try {
            self::insert($notifications);
            return count($notifications);
        } catch (\Exception $e) {
            \Log::error('Error sending notifications to employees: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * إرسال إشعار لجميع الموظفين
     */
    public static function sendToAllEmployees($data)
    {
        $employeeIds = User::where('is_employee', true)
                          ->where('is_active', true)
                          ->pluck('id');
        return self::sendToEmployees($employeeIds, $data);
    }

    public static function sendToAllInstructors(array $data)
    {
        $ids = User::whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->pluck('id');

        return self::sendToUsers($ids, $data);
    }

    public static function sendToInstructor(int $instructorId, array $data): int
    {
        return self::sendToInstructors([$instructorId], $data);
    }

    /**
     * @param  list<int|string>  $instructorIds
     */
    public static function sendToInstructors(array $instructorIds, array $data): int
    {
        $ids = User::query()
            ->whereIn('id', $instructorIds)
            ->whereIn('role', ['instructor', 'teacher'])
            ->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        self::sendToUsers($ids, $data);

        return $ids->count();
    }
}
