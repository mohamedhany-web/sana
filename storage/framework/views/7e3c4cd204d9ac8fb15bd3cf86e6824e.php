<style>
    .how-step-card {
        transition: transform .3s ease, border-color .3s, box-shadow .3s;
        cursor: default;
    }
    .how-step-card.is-active {
        transform: translateY(-6px);
        border-color: rgba(var(--edu-primary-rgb), .3) !important;
        box-shadow: 0 16px 36px -16px rgba(var(--edu-primary-rgb), .25);
    }
    .how-step-card.is-active .how-step-card__icon {
        transform: scale(1.08);
        background: var(--edu-primary) !important;
        color: #fff !important;
    }
    .how-step-card__icon {
        transition: transform .3s ease, background .3s, color .3s;
    }
    .edu-card.group:hover .edu-card-img-zoom,
    .edu-card:has(a:hover) img.hover\:scale-105 {
        transform: scale(1.05);
    }
</style>
<script>
function howSteps(count) {
    return {
        active: 0,
        timer: null,
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            this.timer = setInterval(() => {
                this.active = (this.active + 1) % count;
            }, 3500);
        },
        setActive(i) { this.active = i; },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        }
    };
}
</script>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/partials/home-interactions.blade.php ENDPATH**/ ?>