@php $photos = ['https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80']; @endphp
<section class="sana-section" id="instructors">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">تعرّف على <span class="hl">معلّمينا</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-teachers-m">
            @forelse(($homeInstructors ?? collect())->take(5) as $p)
            @php
                $name = $p->user->name ?? 'معلّم';
                $headline = $p->headline ?? 'خبير تعليم';
                $photo = $p->photo_path ? $p->photo_url : $photos[0];
            @endphp
            <article class="sana-teacher-m sana-reveal">
                <div class="sana-teacher-m__ring"><img src="{{ $photo }}" alt="{{ $name }}"></div>
                <h3>{{ $name }}</h3>
                <p class="role">{{ Str::limit($headline, 28) }}</p>
                <div class="stars">@for($s=0;$s<5;$s++)<i class="fas fa-star"></i>@endfor</div>
                @if(is_array($p->social_links ?? null))
                <div class="social">
                    @foreach(array_slice($p->social_links, 0, 3) as $net => $url)
                        @if(is_string($url) && $url)<a href="{{ $url }}" target="_blank" rel="noopener"><i class="fab fa-{{ $net === 'linkedin' ? 'linkedin-in' : $net }}"></i></a>@endif
                    @endforeach
                </div>
                @endif
            </article>
            @empty
            @foreach(['سارة محمد','أحمد علي','نور حسن','ليلى كريم','عمر سالم'] as $n)
            <article class="sana-teacher-m sana-reveal">
                <div class="sana-teacher-m__ring av">{{ mb_substr($n, 0, 1) }}</div>
                <h3>أ. {{ $n }}</h3>
                <p class="role">م educator متخصص</p>
                <div class="stars">@for($s=0;$s<5;$s++)<i class="fas fa-star"></i>@endfor</div>
            </article>
            @endforeach
            @endforelse
        </div>
    </div>
</section>
