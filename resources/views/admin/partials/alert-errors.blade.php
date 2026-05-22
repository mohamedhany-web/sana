@if($errors->any())
    <div class="admin-alert" style="background: #fef2f2; border-color: #fecaca; color: #991b1b;">
        <span class="admin-alert__icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-exclamation-circle"></i></span>
        <div>
            <p class="font-bold mb-1">يرجى تصحيح ما يلي:</p>
            <ul class="list-disc list-inside space-y-0.5 text-sm font-medium">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
