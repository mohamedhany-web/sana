@extends('layouts.app')
@section('title', 'طلب مساعدة')
@section('header', 'طلب مساعدة')
@include('partials.tutor-lesson-ui')
@section('content')
<div class="tl-page max-w-xl mx-auto py-2"><form method="post" action="{{ route('parent.tutor-lessons.assisted.store') }}" class="tl-card tl-form space-y-4">@csrf
<div><label>الابن/الابنة</label><select name="student_id" required>@foreach($children as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
<div><label>المواد</label><div class="flex flex-wrap gap-2">@foreach($subjects as $s)<label class="tl-chip"><input type="checkbox" name="subject_ids[]" value="{{ $s->id }}"> {{ $s->name }}</label>@endforeach</div></div>
<div><label>الرسالة</label><textarea name="message" rows="4" required></textarea></div>
<button class="tl-btn tl-btn-primary w-full">إرسال — سنتواصل عبر المنصة</button></form></div>
@endsection
