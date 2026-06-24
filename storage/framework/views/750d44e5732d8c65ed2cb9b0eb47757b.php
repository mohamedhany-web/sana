<?php $__env->startSection('title', __('student.wallet_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $depositCount = isset($transactions)
        ? $transactions->getCollection()->filter(fn ($t) => $t->type == 'deposit' || $t->type == 'إيداع')->count()
        : 0;
    $withdrawCount = isset($transactions)
        ? $transactions->getCollection()->filter(fn ($t) => ! ($t->type == 'deposit' || $t->type == 'إيداع'))->count()
        : 0;
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.wallet_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.transactions_log')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.my_courses_link')); ?>

            </a>
        </div>
    </header>

    <?php if(isset($wallet)): ?>
        <div class="sanua-wallet-balance">
            <div>
                <p class="sanua-wallet-balance__label"><?php echo e(__('student.current_balance')); ?></p>
                <p class="sanua-wallet-balance__amount">
                    <?php echo e(number_format($wallet->balance ?? 0, 2)); ?>

                    <span style="font-size:0.55em;font-weight:800;opacity:0.9"><?php echo e(__('public.currency')); ?></span>
                </p>
            </div>
            <span class="sanua-wallet-balance__icon" aria-hidden="true">
                <i class="fas fa-wallet"></i>
            </span>
        </div>

        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-wallet"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e(number_format($wallet->balance ?? 0, 0)); ?></strong>
                    <span><?php echo e(__('student.current_balance')); ?></span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-arrow-down"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($depositCount); ?></strong>
                    <span>إيداع (الصفحة)</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                    <i class="fas fa-arrow-up"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($withdrawCount); ?></strong>
                    <span>سحب (الصفحة)</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-receipt"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($transactions->total() ?? 0); ?></strong>
                    <span>إجمالي العمليات</span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="sanua-wallet-empty"><?php echo e(__('student.no_wallet_message')); ?></div>
    <?php endif; ?>

    <?php if(isset($transactions) && $transactions->count() > 0): ?>
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3><?php echo e(__('student.transactions_log')); ?></h3>
                </div>
                <div class="sanua-panel__body sanua-tx-list">
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isDeposit = $transaction->type == 'deposit' || $transaction->type == 'إيداع'; ?>
                        <div class="sanua-tx-row">
                            <div class="min-w-0">
                                <p class="sanua-tx-row__title"><?php echo e($transaction->description ?? __('student.transaction_default')); ?></p>
                                <p class="sanua-tx-row__date">
                                    <?php echo e($transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : '—'); ?>

                                </p>
                            </div>
                            <p class="sanua-tx-row__amount <?php echo e($isDeposit ? 'is-in' : 'is-out'); ?>">
                                <?php echo e($isDeposit ? '+' : '−'); ?><?php echo e(number_format($transaction->amount ?? 0, 2)); ?>

                                <?php echo e(__('public.currency')); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <?php if($transactions->hasPages()): ?>
                <div class="sanua-pagination">
                    <?php echo e($transactions->links()); ?>

                </div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h3><?php echo e(__('student.no_transactions')); ?></h3>
            <p>ستظهر عمليات المحفظة هنا عند إجرائها</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\wallet\index.blade.php ENDPATH**/ ?>