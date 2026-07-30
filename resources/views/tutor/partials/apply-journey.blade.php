{{-- يوضح مسار الانضمام: قبل التسجيل / بعد إنشاء الحساب --}}
@php
    $journeyPhase = $journeyPhase ?? 'register'; // register | complete
@endphp
<div class="ta-journey mb-6" role="list">
    <div class="ta-journey-item {{ $journeyPhase === 'register' ? 'is-current' : 'is-done' }}" role="listitem">
        <span class="ta-journey-num">1</span>
        <div class="ta-journey-body">
            <p class="ta-journey-title">قبل التسجيل — إنشاء الحساب</p>
            <p class="ta-journey-desc">الاسم، الجوال، البريد (يوزر الدخول)، وكلمة المرور فقط.</p>
        </div>
    </div>
    <div class="ta-journey-item {{ $journeyPhase === 'complete' ? 'is-current' : ($journeyPhase === 'register' ? 'is-next' : 'is-done') }}" role="listitem">
        <span class="ta-journey-num">2</span>
        <div class="ta-journey-body">
            <p class="ta-journey-title">بعد التسجيل — إكمال الملف</p>
            <p class="ta-journey-desc">المؤهل، التخصصات، الفيديو، المستندات، ثم الإرسال للإدارة.</p>
        </div>
    </div>
    <div class="ta-journey-item is-next" role="listitem">
        <span class="ta-journey-num">3</span>
        <div class="ta-journey-body">
            <p class="ta-journey-title">بعد الإرسال — مراجعة الإدارة</p>
            <p class="ta-journey-desc">يصل تأكيد على بريدك، ثم الموافقة لتفعيل التدريس.</p>
        </div>
    </div>
</div>
<style>
    .ta-journey { display: flex; flex-direction: column; gap: .65rem; }
    .ta-journey-item {
        display: flex; gap: .85rem; align-items: flex-start;
        padding: .85rem 1rem; border-radius: 1rem;
        border: 1.5px solid #e2e8f0; background: #f8fafc;
    }
    .ta-journey-item.is-current {
        border-color: rgba(var(--edu-primary-rgb), .35);
        background: var(--edu-primary-light);
        box-shadow: 0 8px 24px -16px rgba(var(--edu-primary-rgb), .45);
    }
    .ta-journey-item.is-done {
        border-color: #a7f3d0; background: #ecfdf5;
    }
    .ta-journey-item.is-next { opacity: .72; }
    .ta-journey-num {
        flex-shrink: 0; width: 1.75rem; height: 1.75rem; border-radius: 999px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 800; background: #fff; color: #64748b;
        border: 1.5px solid #e2e8f0;
    }
    .ta-journey-item.is-current .ta-journey-num {
        background: var(--edu-primary); color: #fff; border-color: transparent;
    }
    .ta-journey-item.is-done .ta-journey-num {
        background: #10b981; color: #fff; border-color: transparent;
    }
    .ta-journey-title { margin: 0; font-size: .88rem; font-weight: 800; color: #0f172a; }
    .ta-journey-desc { margin: .2rem 0 0; font-size: .78rem; color: #64748b; line-height: 1.55; }
</style>
