@extends('layouts.app')

@section('title', __('student.play.activities_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.play.activities_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.play.activities_sub') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('dashboard') }}" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-home"></i>
                {{ __('student.play.back_dashboard') }}
            </a>
        </div>
    </header>

    @if(empty($tiles))
        <div class="sanua-empty">
            <div class="sanua-empty__icon"><i class="fas fa-gamepad"></i></div>
            <h3>{{ __('student.play.activities_empty') }}</h3>
        </div>
    @else
        <div class="sanua-hub-grid">
            @foreach($tiles as $tile)
                <a href="{{ $tile['url'] }}" class="sanua-hub-tile">
                    <span class="sanua-hub-tile__emoji">{{ $tile['emoji'] }}</span>
                    <span class="sanua-hub-tile__label">{{ $tile['label'] }}</span>
                    <span class="sanua-hub-tile__hint">{{ $tile['hint'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
