<section class="sana-section" id="testimonials">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">ماذا يقول <span class="hl">طلابنا وأولياء الأمور؟</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-test-m">
            @foreach(($homeTestimonials ?? collect())->take(3) as $t)
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«{{ Str::limit(strip_tags($t->body ?? ''), 160) }}»</p>
                <div class="stars">@for($s=0;$s<5;$s++)<i class="fas fa-star"></i>@endfor</div>
                <div class="author">
                    @if($t->isImageType() && $t->publicImageUrl())
                        <img src="{{ $t->publicImageUrl() }}" alt="">
                    @else
                        <span class="av">{{ $t->author_name ? mb_substr($t->author_name, 0, 1) : '؟' }}</span>
                    @endif
                    <div><strong>{{ $t->author_name ?? 'عميل' }}</strong>@if($t->role_label)<small>{{ $t->role_label }}</small>@endif</div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
