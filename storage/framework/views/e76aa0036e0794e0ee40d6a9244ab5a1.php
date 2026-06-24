<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $wallets = $wallets ?? collect();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>دفع اشتراك الباقة - <?php echo e($plan['label'] ?? 'الباقة'); ?> - <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="#283593">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#020617' },
                    brand: { 50:'#FFF3E0',100:'#FFE0B2',200:'#FFCC80',300:'#FFB74D',400:'#FFA726',500:'#FB5607',600:'#E04D00',700:'#BF360C',800:'#8D2600',900:'#5D1A00' },
                    mx: { navy:'#283593', indigo:'#1F2A7A', orange:'#FB5607', rose:'#FFE5F7', gold:'#FFE569', soft:'#F7F8FF' }
                },
                fontFamily: {
                    heading: ['Cairo','Tajawal','IBM Plex Sans Arabic','sans-serif'],
                    body: ['Cairo','IBM Plex Sans Arabic','Tajawal','sans-serif'],
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{background:#fff;overflow-x:hidden}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width:768px){.container-1200{padding-inline:16px}}
        .card-hover{transition:all .4s cubic-bezier(.16,1,.3,1)}
        .card-hover:hover{transform:translateY(-6px);box-shadow:0 20px 40px -18px rgba(15,23,42,.35)}
        .btn-primary{position:relative;overflow:hidden;transition:all .3s cubic-bezier(.16,1,.3,1)}
        .btn-primary::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);transition:left .5s}
        .btn-primary:hover::before{left:100%}
        .btn-primary:hover{transform:translateY(-1px)}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{
            background:rgba(31,42,122,.92)!important;
            backdrop-filter:blur(12px)!important;
            -webkit-backdrop-filter:blur(12px)!important;
            border-bottom:1px solid rgba(255,255,255,.08)!important;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>.navbar-spacer{display:block}</style>

    <main class="flex-1">
        
        <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative"
                 style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
            <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>

            <div class="container-1200 relative z-10 text-center">
                <nav class="text-sm text-slate-500 mb-6 flex items-center justify-center gap-2 flex-wrap">
                    <a href="<?php echo e(url('/')); ?>" class="hover:text-mx-indigo transition-colors">الرئيسية</a>
                    <span>/</span>
                    <a href="<?php echo e(route('public.pricing')); ?>" class="hover:text-mx-indigo transition-colors">الأسعار والباقات</a>
                    <span>/</span>
                    <span class="text-mx-indigo font-semibold">دفع الاشتراك</span>
                </nav>
                <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full text-sm font-medium mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                    <span class="w-2 h-2 rounded-full bg-[#FB5607] animate-pulse"></span>
                    تحويل مبلغ الاشتراك ثم رفع إيصال الدفع
                </div>
                <h1 class="font-heading text-3xl sm:text-4xl md:text-5xl font-black leading-tight text-mx-indigo mb-4">
                    دفع اشتراك الباقة
                    <br>
                    <span style="color:#FB5607">
                        <?php echo e($plan['label'] ?? 'باقة المعلم'); ?>

                    </span>
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    قم بتحويل <strong class="text-mx-indigo"><?php echo e(number_format($plan['price'] ?? 0, 0)); ?> <?php echo e(__('public.currency')); ?></strong> إلى أحد الحسابات أدناه، ثم ارفع صورة إيصال الدفع ليتم مراجعته وتفعيل اشتراكك.
                </p>
            </div>
        </section>

        
        <section class="py-16 md:py-20 bg-white">
            <div class="container-1200">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6 sticky top-24 card-hover">
                            <h3 class="font-heading text-xl font-black text-slate-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-receipt text-[#FB5607]"></i>
                                ملخص الدفع
                            </h3>
                            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 mb-4">
                                <p class="text-sm font-semibold text-amber-800 mb-1">مبلغ الاشتراك المطلوب تحويله</p>
                                <p class="text-3xl font-black text-amber-900">
                                    <?php echo e(number_format($plan['price'] ?? 0, 0)); ?>

                                    <span class="text-lg font-bold text-amber-700"><?php echo e(__('public.currency')); ?></span>
                                </p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 mb-4">
                                <label class="block text-sm font-bold text-slate-700 mb-2">كوبون خصم الباقة (اختياري)</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="subscription_coupon_code" name="coupon_code" value="<?php echo e(old('coupon_code')); ?>"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm uppercase font-mono"
                                           placeholder="مثال: PRO20" dir="ltr" autocomplete="off">
                                </div>
                                <p class="text-xs text-slate-500 mt-2">إذا كان الكوبون صالحاً سيتم تطبيقه عند إرسال الطلب.</p>
                            </div>
                            <p class="text-sm text-slate-600 mb-2"><strong><?php echo e($plan['label'] ?? 'الباقة'); ?></strong> · <?php echo e($billingLabel); ?></p>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="lg:col-span-2 space-y-6">
                        <input type="hidden" id="sub_checkout_upgrade" value="<?php echo e(!empty($upgrade) ? '1' : '0'); ?>">
                        <input type="hidden" id="sub_checkout_from" value="<?php echo e($fromSubscriptionId ?? ''); ?>">

                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden card-hover">
                            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                                    <h3 class="font-heading text-lg font-black text-slate-900 flex items-center gap-2">
                                        <i class="fas fa-file-invoice text-[#FB5607]"></i>
                                    بعد التحويل ارفع إيصال الدفع
                                </h3>
                                <p class="text-xs text-slate-600 mt-1">سيظهر طلبك في لوحة الإدارة لمراجعة الدفع وتفعيل الاشتراك.</p>
                            </div>
                            <div class="p-6">

                                <form action="<?php echo e(route('public.subscription.checkout.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="plan" value="<?php echo e($planKey); ?>">
                                    <input type="hidden" name="coupon_code" id="subscription_coupon_code_hidden" value="<?php echo e(old('coupon_code')); ?>">

                                    <?php $__errorArgs = ['coupon_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">طريقة الدفع <span class="text-rose-500">*</span></label>
                                        <select name="payment_method" id="payment_method" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-[#283593] focus:border-[#283593]">
                                            <option value="bank_transfer" <?php echo e(old('payment_method', $wallets->count() > 0 ? '' : 'bank_transfer') === 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                                        </select>
                                    </div>


                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">صورة إيصال الدفع <span class="text-rose-500">*</span></label>
                                        <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" required
                                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-[#283593] focus:border-[#283593] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#FFE5F7] file:text-[#283593] file:font-semibold">
                                        <p class="text-xs text-slate-500 mt-1">صيغ مقبولة: jpeg, png, jpg — حجم أقصى 40 ميجابايت</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات (اختياري)</label>
                                        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-[#283593] focus:border-[#283593]" placeholder="أي ملاحظات إضافية..."><?php echo e(old('notes')); ?></textarea>
                                    </div>

                                    <button type="submit" class="btn-primary w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-[#FB5607] hover:bg-[#e84d00] text-white font-bold text-base shadow-lg">
                                        <i class="fas fa-paper-plane"></i>
                                        إرسال إيصال الدفع
                                    </button>
                                </form>
                                <p class="text-xs text-slate-500 mt-4 text-center">
                                    بعد الإرسال سيظهر طلبك في لوحة الإدارة. عند التحقق من الدفع سيتم تفعيل اشتراكك وتظهر أقسام الباقة في لوحتك.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="<?php echo e(route('public.pricing')); ?>" class="mt-8 inline-flex items-center gap-2 text-[#283593] hover:text-[#1f2a7a] font-semibold transition-colors">
                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i>
                    العودة إلى الأسعار والباقات
                </a>
            </div>
        </section>
    </main>

    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
    (function(){
        var method = document.getElementById('payment_method');
        var wrap = document.getElementById('wallet_id_wrap');
        var walletSelect = document.getElementById('wallet_id');
        if (method && wrap) {
            function toggle() {
                wrap.classList.toggle('hidden', method.value !== 'wallet');
                if (walletSelect) walletSelect.required = (method.value === 'wallet');
            }
            method.addEventListener('change', toggle);
            toggle();
        }
    })();
    </script>


    <script>
    (function() {
        var couponInput = document.getElementById('subscription_coupon_code');
        var hiddenCoupon = document.getElementById('subscription_coupon_code_hidden');
        if (!couponInput || !hiddenCoupon) return;
        function sync() { hiddenCoupon.value = (couponInput.value || '').trim(); }
        couponInput.addEventListener('input', sync);
        couponInput.addEventListener('change', sync);
        sync();
    })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\subscription-checkout.blade.php ENDPATH**/ ?>