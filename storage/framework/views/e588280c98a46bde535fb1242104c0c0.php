<div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 sm:p-5">
    <h3 class="text-sm font-bold text-emerald-900 flex items-center gap-2 mb-3">
        <i class="fas fa-lightbulb"></i>
        كيف يعمل برنامج الإحالة؟
    </h3>
    <ol class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs text-emerald-900/90 list-none m-0 p-0">
        <li class="flex gap-2 items-start">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">1</span>
            <span>كل مستخدم له <strong>كود إحالة</strong> — يشاركه مع أصدقائه أو عبر رابط التسجيل.</span>
        </li>
        <li class="flex gap-2 items-start">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">2</span>
            <span>عند تسجيل شخص جديد بالكود يُطبَّق <strong>برنامج الإحالة الافتراضي</strong> (أو أحدث برنامج نشط).</span>
        </li>
        <li class="flex gap-2 items-start">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">3</span>
            <span>المحال (الصديق) يحصل على <strong>خصم</strong> عند أول شراء/اشتراك حسب قواعد البرنامج.</span>
        </li>
        <li class="flex gap-2 items-start">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">4</span>
            <span>المحيل يحصل على <strong>مكافأة</strong> (مبلغ أو نقاط) عند اكتمال الإحالة — تُتابع من صفحة «الإحالات».</span>
        </li>
    </ol>
    <p class="text-[11px] text-emerald-800/70 mt-3 mb-0">
        رابط التسجيل: <code class="bg-white/60 px-1 rounded"><?php echo e(url('/register')); ?>?ref=كود_المستخدم</code>
    </p>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\partials\referral-program-how-it-works.blade.php ENDPATH**/ ?>