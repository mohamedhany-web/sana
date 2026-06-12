<?php

return [
  /*
   * جميع المرفقات (فيديو، CV، شهادات…) تُرفع إلى Cloudflare R2
   * عبر filesystems.tutor_application_disk (الافتراضي: r2).
   */
  'storage_disk' => config('filesystems.tutor_application_disk', 'r2'),

  'video_max_mb' => (int) env('TUTOR_APPLY_VIDEO_MAX_MB', 150),
  'document_max_mb' => (int) env('TUTOR_APPLY_DOC_MAX_MB', 15),

  'specializations' => [
      'sciences' => 'علوم',
      'math' => 'رياضيات',
      'arabic' => 'لغة عربية',
      'english' => 'لغة إنجليزية',
      'qiyas_tahsili' => 'قدرات / تحصيلي',
      'elementary_foundation' => 'تأسيس ابتدائي',
      'teacher_training' => 'تدريب معلمين',
      'other' => 'أخرى',
  ],

  'curricula' => [
      'saudi' => 'المنهج السعودي',
      'american' => 'American Curriculum',
      'british_cambridge' => 'British / Cambridge',
      'ib_pyp_myp' => 'IB / PYP / MYP',
  ],

  'stages' => [
      'kg_early' => 'KG / Early Years',
      'primary' => 'Primary',
      'middle' => 'Middle School',
      'high' => 'High School',
  ],

  'lesson_formats' => [
      'one_to_one' => 'حصص فردية (One-to-One)',
      'small_group' => 'مجموعات صغيرة حتى ٥ طلاب',
      'recorded_courses' => 'كورسات مسجلة',
      'exam_reviews' => 'مراجعات اختبارات',
      'teacher_training' => 'تدريب معلمين',
      'remedial' => 'تأسيس وعلاج ضعف',
  ],

  'tech_skills' => [
      'zoom' => 'Zoom',
      'teams' => 'Microsoft Teams',
      'google_meet' => 'Google Meet',
      'powerpoint' => 'PowerPoint',
      'canva' => 'Canva',
      'whiteboard_tablet' => 'Whiteboard / Pen Tablet',
      'kahoot_quizizz' => 'Kahoot / Quizizz',
      'video_recording' => 'تسجيل فيديو تعليمي',
  ],

  'weekdays' => [
      0 => 'الأحد',
      1 => 'الإثنين',
      2 => 'الثلاثاء',
      3 => 'الأربعاء',
      4 => 'الخميس',
      5 => 'الجمعة',
      6 => 'السبت',
  ],

  'commitments' => [
      'official_channels_only' => 'ألتزم بعدم التواصل مع الطلاب خارج القنوات الرسمية.',
      'punctuality' => 'ألتزم بالحضور في المواعيد المحددة.',
      'no_private_lessons' => 'ألتزم بعدم تقديم حصص خاصة لطلاب سنا خارج الأكاديمية.',
      'data_privacy' => 'ألتزم بالحفاظ على سرية بيانات الطلاب وأولياء الأمور.',
      'recording_consent' => 'أوافق على تسجيل بعض الحصص لأغراض الجودة والمتابعة عند الحاجة.',
      'quality_review' => 'أوافق على مراجعة أدائي من إدارة الجودة في الأكاديمية.',
  ],

  'evaluation_criteria' => [
      'clarity' => 'وضوح الشرح',
      'time_management' => 'إدارة وقت فيديو ٥ دقائق',
      'language_style' => 'اللغة والأسلوب التربوي',
      'media_use' => 'استخدام الوسائل التعليمية',
      'curriculum_fit' => 'الخبرة المناسبة للمناهج المطلوبة',
      'professionalism' => 'الاحترافية والالتزام',
  ],

  'evaluation_decisions' => [
      'preliminary_accept' => 'قبول مبدئي',
      'second_interview' => 'مقابلة ثانية',
      'needs_training' => 'يحتاج تدريب',
      'reject' => 'رفض',
  ],
];
