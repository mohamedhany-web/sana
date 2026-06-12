@php
    $app = $application->application_data ?? [];
    $personal = $app['personal'] ?? [];
    $qual = $app['qualification'] ?? [];
    $teaching = $app['teaching'] ?? [];
    $screening = $app['screening'] ?? [];
    $video = $app['video'] ?? [];
    $docs = $app['documents'] ?? [];
    $declaration = $app['declaration'] ?? [];
    $commitments = $app['commitments'] ?? [];
    $eval = $application->application_evaluation ?? [];
    $opts = config('tutor_application');
    $fileUrl = fn (?string $path) => $path ? \App\Services\TutorApplicationStorage::publicUrl($path) : null;
    $platformSubjects = \App\Models\AcademicSubject::whereIn('id', $application->tutor_subject_ids ?? [])->pluck('name');
    $platformYears = \App\Models\AcademicYear::whereIn('id', $application->tutor_academic_year_ids ?? [])->pluck('name');
    $user = $application->user;
    $videoFileUrl = $fileUrl($video['file_path'] ?? null);
    $isYoutube = ! empty($video['link']) && preg_match('/youtube\.com|youtu\.be/i', (string) $video['link']);
@endphp

<section class="rounded-3xl bg-white border border-violet-200 shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-violet-100 bg-gradient-to-l from-violet-50 to-white flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 m-0">
                <i class="fas fa-file-lines text-violet-600"></i>
                نموذج طلب التوظيف — بيانات داخلية
            </h3>
            <p class="text-xs text-slate-500 mt-1 m-0">كل ما أدخله المتقدم في النموذج العام — للمراجعة والتقييم من الإدارة فقط.</p>
        </div>
        @if(!empty($app['form_version']))
            <span class="text-[10px] font-bold uppercase tracking-wide text-violet-600 bg-violet-100 px-2 py-1 rounded-lg">{{ $app['form_version'] }}</span>
        @endif
    </div>

    @if($app === [])
        <div class="p-6 text-sm text-amber-800 bg-amber-50">
            طلب قديم بُني قبل النموذج الموسّع — تظهر أعلاه بيانات الملف الأساسية فقط.
        </div>
    @else
    <div class="p-6 sm:p-8 space-y-8 text-sm">

        {{-- ١ البيانات الشخصية --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">١. البيانات الشخصية</h4>
            <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-2">
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">الاسم</dt><dd class="font-semibold text-slate-900">{{ $user?->name ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">البريد</dt><dd class="font-semibold text-slate-900" dir="ltr">{{ $user?->email ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">الجوال</dt><dd class="font-semibold text-slate-900" dir="ltr">{{ $user?->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">الجنسية</dt><dd class="font-semibold">{{ $personal['nationality'] ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">الدولة / المدينة</dt><dd class="font-semibold">{{ $personal['country_city'] ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">LinkedIn</dt><dd class="font-semibold">
                    @if(!empty($personal['linkedin_url']))
                        <a href="{{ $personal['linkedin_url'] }}" target="_blank" rel="noopener" class="text-sky-600">فتح الرابط</a>
                    @else — @endif
                </dd></div>
            </dl>
        </div>

        {{-- ٢ المؤهل --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">٢. المؤهل والخبرة</h4>
            <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-2 mb-3">
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">المؤهل</dt><dd>{{ $qual['degree_qualification'] ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">التخصص</dt><dd>{{ $qual['specialization'] ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">سنوات الخبرة</dt><dd>{{ $application->tutor_years_experience ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-b border-slate-50 pb-2"><dt class="text-slate-500">آخر جهة عمل</dt><dd>{{ $qual['last_workplace'] ?? '—' }}</dd></div>
            </dl>
            @if(!empty($qual['grades_taught']))
                <p class="m-0 mb-2"><span class="text-slate-500">المراحل التي درّسها:</span> {{ $qual['grades_taught'] }}</p>
            @endif
            @if(!empty($qual['curricula_experience_text']))
                <p class="m-0"><span class="text-slate-500">خبرة المناهج:</span> {{ $qual['curricula_experience_text'] }}</p>
            @endif
            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-xs text-slate-500 m-0 mb-1">العنوان المختصر</p>
                <p class="font-bold m-0">{{ $application->headline }}</p>
                <p class="text-xs text-slate-500 m-0 mt-2 mb-1">النبذة</p>
                <p class="whitespace-pre-wrap m-0 text-slate-700">{{ $application->bio }}</p>
            </div>
        </div>

        {{-- ٣–٥ تدريس --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">٣–٥. التخصصات والمناهج ونوع الحصص</h4>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-slate-500 m-0 mb-1">التخصصات المطلوبة</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($teaching['specializations'] ?? [] as $k)
                            <span class="px-2 py-0.5 rounded-full bg-violet-50 text-violet-800 text-xs font-semibold">{{ $opts['specializations'][$k] ?? $k }}</span>
                        @empty <span class="text-slate-400">—</span> @endforelse
                        @if(!empty($teaching['specializations_other']))
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs">أخرى: {{ $teaching['specializations_other'] }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 m-0 mb-1">المناهج</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($teaching['curricula'] ?? [] as $k)
                            <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-800 text-xs font-semibold">{{ $opts['curricula'][$k] ?? $k }}</span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 m-0 mb-1">المراحل</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($teaching['stages'] ?? [] as $k)
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold">{{ $opts['stages'][$k] ?? $k }}</span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 m-0 mb-1">نوع الحصص المناسبة</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($teaching['lesson_formats'] ?? [] as $k)
                            <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold">{{ $opts['lesson_formats'][$k] ?? $k }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-slate-500 m-0 mb-1">مواد المنصة</p>
                        <p class="m-0">{{ $platformSubjects->isNotEmpty() ? $platformSubjects->join('، ') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 m-0 mb-1">مسارات المنصة</p>
                        <p class="m-0">{{ $platformYears->isNotEmpty() ? $platformYears->join('، ') : '—' }}</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-slate-500 m-0 mb-1">أنماط الحجز على المنصة</p>
                        <ul class="m-0 list-disc list-inside text-slate-700">
                            @foreach($application->tutor_matching_modes ?? [] as $mode)
                                <li>{{ __('tutor.matching_'.$mode) }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 m-0 mb-1">أنواع الحصص (منصة)</p>
                        <ul class="m-0 list-disc list-inside text-slate-700">
                            @foreach($application->tutor_session_types ?? [] as $type)
                                <li>{{ __('tutor.session_'.$type) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- ٦ توفر --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">٦. التوفر الأسبوعي (توقيت السعودية)</h4>
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50"><tr><th class="p-2 text-right">اليوم</th><th class="p-2 text-right">الفترات</th><th class="p-2 text-right">ملاحظات</th></tr></thead>
                    <tbody>
                    @foreach($app['weekly_availability'] ?? [] as $row)
                        <tr class="border-t border-slate-50">
                            <td class="p-2 font-semibold">{{ $row['label'] ?? '' }}</td>
                            <td class="p-2">{{ $row['periods'] ?: '—' }}</td>
                            <td class="p-2 text-slate-500">{{ $row['notes'] ?? '' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ٧ مهارات --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-2">٧. المهارات التقنية</h4>
            <p class="m-0">{{ collect($app['tech_skills'] ?? [])->map(fn ($k) => $opts['tech_skills'][$k] ?? $k)->join(' • ') ?: '—' }}</p>
        </div>

        {{-- ٨ فيديو --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">٨. فيديو الشرح التجريبي</h4>
            <div class="rounded-xl border border-slate-200 p-4 space-y-3">
                <p class="m-0"><strong>الموضوع:</strong> {{ $video['topic_title'] ?? '—' }}</p>
                <p class="m-0"><strong>الصف / المرحلة:</strong> {{ $video['grade_level'] ?? '—' }}</p>
                @if(!empty($video['link']))
                    <a href="{{ $video['link'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sky-600 font-bold">
                        <i class="fas fa-external-link-alt"></i> فتح رابط الفيديو
                    </a>
                    @if($isYoutube)
                        @php
                            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video['link'], $yt);
                            $ytId = $yt[1] ?? null;
                        @endphp
                        @if($ytId)
                        <div class="aspect-video max-w-xl rounded-xl overflow-hidden bg-black">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $ytId }}" title="فيديو المتقدم" allowfullscreen loading="lazy"></iframe>
                        </div>
                        @endif
                    @endif
                @elseif($videoFileUrl)
                    <a href="{{ $videoFileUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">
                        <i class="fas fa-play"></i> تشغيل / تحميل الفيديو المرفوع
                    </a>
                    <video controls class="w-full max-w-xl rounded-xl bg-black mt-2" preload="metadata">
                        <source src="{{ $videoFileUrl }}">
                    </video>
                @else
                    <p class="text-amber-700 m-0">لم يُرفق فيديو.</p>
                @endif
            </div>
        </div>

        {{-- ٩ مستندات --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">٩. المستندات المطلوبة</h4>
            <div class="flex flex-wrap gap-2">
                @foreach(['cv' => 'السيرة الذاتية', 'degree_photo' => 'صورة المؤهل', 'id_photo' => 'الهوية / الإقامة', 'experience_certs' => 'شهادات خبرة', 'training_certs' => 'شهادات تدريب', 'portfolio_file' => 'نماذج أعمال'] as $key => $label)
                    @if($url = $fileUrl($docs[$key] ?? null))
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 font-semibold text-slate-700">
                            <i class="fas fa-file-download text-violet-500"></i> {{ $label }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-dashed border-slate-200 text-slate-400 text-xs">{{ $label }} — غير مرفق</span>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ١٠ فرز --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">١٠. أسئلة تقييم مبدئية</h4>
            <div class="space-y-4">
                @foreach([
                    'why_sana' => 'لماذا ترغب في العمل مع أكاديمية سنا؟',
                    'weak_student_approach' => 'كيف تتعامل مع طالب ضعيف في الأساسيات؟',
                    'online_interactivity' => 'كيف تجعل الحصة الأونلاين تفاعلية؟',
                    'teaching_tools' => 'ما الأدوات التي تستخدمها في الشرح؟',
                    'expected_rate' => 'متوسط المقابل المتوقع للحصة أو الساعة',
                    'available_start_date' => 'متى يمكنك البدء؟',
                ] as $k => $lbl)
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                    <p class="text-xs font-bold text-slate-500 m-0 mb-1">{{ $lbl }}</p>
                    <p class="whitespace-pre-wrap m-0 text-slate-800">{{ $screening[$k] ?? '—' }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ١١ التزام --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">١١. أسئلة الالتزام (سياسات سارية بعد القبول)</h4>
            <ul class="space-y-2 m-0 p-0 list-none">
                @foreach($opts['commitments'] ?? [] as $key => $text)
                <li class="flex items-start gap-2">
                    @if(!empty($commitments[$key]))
                        <i class="fas fa-circle-check text-emerald-600 mt-0.5"></i>
                    @else
                        <i class="fas fa-circle-xmark text-rose-500 mt-0.5"></i>
                    @endif
                    <span class="{{ !empty($commitments[$key]) ? 'text-slate-800' : 'text-rose-700' }}">{{ $text }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- ١٢ إقرار --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-violet-700 mb-3">١٢. الإقرار</h4>
            <dl class="grid sm:grid-cols-3 gap-3 text-sm">
                <div class="p-3 rounded-xl bg-slate-50"><dt class="text-xs text-slate-500">الاسم</dt><dd class="font-bold m-0">{{ $declaration['name'] ?? '—' }}</dd></div>
                <div class="p-3 rounded-xl bg-slate-50"><dt class="text-xs text-slate-500">التوقيع</dt><dd class="font-bold m-0">{{ $declaration['signature'] ?? '—' }}</dd></div>
                <div class="p-3 rounded-xl bg-slate-50"><dt class="text-xs text-slate-500">التاريخ</dt><dd class="font-bold m-0">{{ $declaration['date'] ?? '—' }}</dd></div>
            </dl>
        </div>

        @if(!empty($eval['scores']) || !empty($eval['decision']))
        <div class="border-t border-slate-100 pt-6">
            <h4 class="text-xs font-black uppercase tracking-widest text-indigo-700 mb-2">ملخص تقييم الفريق (محفوظ)</h4>
            @if(!empty($eval['decision']))
                <p class="m-0 mb-1"><strong>القرار:</strong> {{ $opts['evaluation_decisions'][$eval['decision']] ?? $eval['decision'] }}</p>
            @endif
            @if(!empty($eval['reviewer_name']))
                <p class="text-xs text-slate-500 m-0">{{ $eval['reviewer_name'] }} — {{ isset($eval['reviewed_at']) ? \Carbon\Carbon::parse($eval['reviewed_at'])->timezone(config('app.timezone'))->format('Y-m-d H:i') : '' }}</p>
            @endif
            @if(!empty($eval['notes']))
                <p class="text-sm mt-2 whitespace-pre-wrap m-0">{{ $eval['notes'] }}</p>
            @endif
        </div>
        @endif
    </div>
    @endif
</section>
