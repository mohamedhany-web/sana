<?php

return [
    'page_title' => 'Teacher Onboarding Policy',
    'page_subtitle' => 'For applicants joining the platform',
    'meta_description' => 'Teacher onboarding policy on the Sana platform — official channels, student data confidentiality, and content ownership. Read before submitting your application.',
    'hero_sub' => 'A document for teachers wishing to join — read it before completing the "Join as a Teacher" application. Agreement to these terms is part of the application form.',
    'applicant_notice' => 'This page is for the teacher onboarding process — not part of the general family and student documentation.',
    'intro' => 'This policy aims to govern the relationship between teachers and the Sana platform, protect the rights of students and parents, and maintain the confidentiality of platform data.',
    'intro_commit' => 'Every applicant wishing to join as a teacher is required to review this policy and agree to it as part of the application form.',

    'sections' => [
        [
            'id' => 'confidentiality',
            'icon' => 'user-secret',
            'title' => 'Information confidentiality',
            'body' => 'The teacher commits to maintaining the confidentiality of information they access while working with the platform, including but not limited to:',
            'items' => [
                'Student and parent data.',
                'Phone numbers, email addresses, or contact methods.',
                'Plan pricing and offers.',
                'Curricula, files, recordings, and teaching materials.',
                'Session management methods and the booking system.',
                'Data of other teachers or the administration team.',
            ],
            'footer' => 'This information may not be shared, copied, or used outside the platform.',
        ],
        [
            'id' => 'communication',
            'icon' => 'comments',
            'title' => 'Communication through official channels',
            'body' => 'The teacher commits not to communicate with any student or parent outside the platform\'s official channels, except with written approval from administration. The following is prohibited:',
            'items' => [
                'Sharing a personal phone number with a student or parent.',
                'Communicating via WhatsApp or personal accounts outside the platform.',
                'Agreeing to private sessions outside the platform.',
                'Redirecting a student to another platform or center.',
                'Requesting payment or booking outside the platform system.',
            ],
            'footer' => 'All educational or administrative communication must take place through the official channels designated by the platform.',
        ],
        [
            'id' => 'non_solicitation',
            'icon' => 'user-slash',
            'title' => 'Non-solicitation of students',
            'body' => 'Students and parents whom the teacher interacts with through the platform are clients of the platform. The teacher commits not to solicit them for direct dealings outside the platform — during employment or afterward as agreed upon at contracting.',
            'items' => [
                'Offering a lower price outside the platform.',
                'Persuading a parent to cancel their subscription.',
                'Offering private sessions to the same student after meeting them through the platform.',
                'Using student data for personal marketing.',
            ],
        ],
        [
            'id' => 'materials',
            'icon' => 'folder-closed',
            'title' => 'Ownership of educational materials',
            'body' => 'Materials prepared or used within the platform are considered the property of the platform unless otherwise agreed in writing, including presentations, tests, plans, and videos.',
            'footer' => 'These materials may not be reused or published outside the platform without written approval.',
        ],
        [
            'id' => 'data',
            'icon' => 'shield-halved',
            'title' => 'Protection of student data',
            'body' => 'The teacher commits to keeping student data confidential and using it only to deliver services within the platform. Retaining student data after employment ends or sharing it with any party is prohibited.',
            'footer' => 'If any data is accessed by mistake, administration must be notified immediately.',
        ],
        [
            'id' => 'conduct',
            'icon' => 'user-tie',
            'title' => 'Professional conduct',
            'body' => 'The teacher commits to the following:',
            'items' => [
                'Attending sessions on time.',
                'Using professional and respectful language.',
                'Not requesting payments directly from a student or parent.',
                'Following the designated curriculum plan.',
                'Not recording or publishing sessions except with official permission.',
            ],
        ],
        [
            'id' => 'brand',
            'icon' => 'certificate',
            'title' => 'Use of the platform name',
            'body' => 'The platform name or logo may not be used in personal advertising without written approval, and official representation may not be claimed outside the scope of assigned duties.',
        ],
        [
            'id' => 'penalties',
            'icon' => 'gavel',
            'title' => 'Penalties',
            'body' => 'In case of any violation, the platform may take actions including warning, temporary suspension, termination of cooperation, or appropriate legal action.',
            'wide' => true,
        ],
    ],

    'contract_extra_title' => 'Contract addendum and acknowledgment (after acceptance)',
    'contract_extra_sub' => 'Presented to accepted applicants upon contracting — not required for general visitors.',

    'annex_title' => 'Contract addendum — brief form',
    'annex_body' => 'The teacher commits to maintaining the confidentiality of information and educational materials, and not communicating with or contracting students they met through the platform outside official channels, unless written approval is obtained.',

    'ack_title' => 'Teacher acknowledgment',
    'ack_body' => 'I acknowledge that I have read the teacher onboarding policy and pledge to comply with its terms.',
    'ack_fields' => [
        'name' => 'Name',
        'id_number' => 'ID / residency number',
        'email' => 'Email',
        'phone' => 'Mobile number',
        'date' => 'Date',
        'signature' => 'Signature',
    ],

    'digital_title' => 'Digital consent upon application',
    'digital_sub' => 'When completing the "Join as a Teacher" form, your agreement covers:',
    'digital_items' => [
        'Teacher onboarding policy.',
        'No communication outside official channels.',
        'Rules for communicating with students.',
        'Confidentiality of internal materials and data.',
        'Data protection guidelines.',
    ],

    'refs_title' => 'Legal references',
    'refs' => [
        'Saudi Labor Law — regarding employment contracts and protection of trade secrets.',
        'Personal Data Protection Law (SDAIA) — regarding student and teacher data.',
    ],

    'cta_title' => 'Ready to join?',
    'cta_sub' => 'Read the policy, then complete your application — agreement to the terms is part of the form.',
    'cta_apply' => 'Apply as a teacher',
    'cta_contact' => 'Contact us',
];
