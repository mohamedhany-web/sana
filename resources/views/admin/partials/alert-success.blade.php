@if(session('success'))
    <div class="admin-alert admin-alert--success">
        <span class="admin-alert__icon"><i class="fas fa-check"></i></span>
        <p class="font-semibold">{{ session('success') }}</p>
    </div>
@endif
