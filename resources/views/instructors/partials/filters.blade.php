@php
    $desktop = $desktop ?? true;
    $formId = $desktop ? 'inst-filters-desktop' : 'inst-filters-mobile';
@endphp
<aside class="{{ $desktop ? 'sana-cat-sidebar sana-inst-sidebar' : 'sana-inst-sidebar sana-inst-sidebar--mobile' }}">
    <form method="GET" action="{{ route('public.instructors.index') }}" id="{{ $formId }}" class="sana-inst-filters-form">
        @foreach($queryBase as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        @if(filled(request('q', request('search'))))
            <input type="hidden" name="q" value="{{ request('q', request('search')) }}">
        @endif

        <h3 class="sana-cat-sidebar__title">
            <i class="fas fa-sliders"></i>
            {{ __('public.instructors_filters_title') }}
        </h3>

        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_availability') }}</span>
            <label class="sana-cat-filter-check">
                <input type="radio" name="availability" value="" @checked(! request('availability'))> <span>{{ __('public.filter_all') }}</span>
            </label>
            @if(($filterOptions['bookable_count'] ?? 0) > 0)
            <label class="sana-cat-filter-check">
                <input type="radio" name="availability" value="bookable" @checked(request('availability') === 'bookable')>
                <span>{{ __('public.instructor_stat_bookable') }} ({{ $filterOptions['bookable_count'] }})</span>
            </label>
            @endif
            @if(($filterOptions['courses_count'] ?? 0) > 0)
            <label class="sana-cat-filter-check">
                <input type="radio" name="availability" value="courses" @checked(request('availability') === 'courses')>
                <span>{{ __('public.instructors_filter_has_courses') }} ({{ $filterOptions['courses_count'] }})</span>
            </label>
            @endif
            @if(($filterOptions['bookable_count'] ?? 0) > 0 && ($filterOptions['courses_count'] ?? 0) > 0)
            <label class="sana-cat-filter-check">
                <input type="radio" name="availability" value="bookable_courses" @checked(request('availability') === 'bookable_courses')>
                <span>{{ __('public.instructors_filter_bookable_and_courses') }}</span>
            </label>
            @endif
        </div>

        @if(!empty($filterOptions['subjects']))
        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.filter_subject') }}</span>
            <select name="subject_id" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                @foreach($filterOptions['subjects'] as $subject)
                    <option value="{{ $subject['id'] }}" @selected((int) request('subject_id') === (int) $subject['id'])>
                        {{ $subject['name'] }} ({{ $subject['count'] }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        @if(!empty($filterOptions['years']))
        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.filter_grade') }}</span>
            <select name="academic_year_id" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                @foreach($filterOptions['years'] as $year)
                    <option value="{{ $year['id'] }}" @selected((int) request('academic_year_id') === (int) $year['id'])>
                        {{ $year['name'] }} ({{ $year['count'] }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        @if(!empty($filterOptions['specializations']))
        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_specialization') }}</span>
            <select name="specialization" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                @foreach($filterOptions['specializations'] as $row)
                    <option value="{{ $row['key'] }}" @selected(request('specialization') === $row['key'])>
                        {{ $row['label'] }} ({{ $row['count'] }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        @if(!empty($filterOptions['curricula']))
        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_curriculum') }}</span>
            <select name="curriculum" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                @foreach($filterOptions['curricula'] as $row)
                    <option value="{{ $row['key'] }}" @selected(request('curriculum') === $row['key'])>
                        {{ $row['label'] }} ({{ $row['count'] }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        @if(!empty($filterOptions['stages']))
        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_stage') }}</span>
            <select name="stage" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                @foreach($filterOptions['stages'] as $row)
                    <option value="{{ $row['key'] }}" @selected(request('stage') === $row['key'])>
                        {{ $row['label'] }} ({{ $row['count'] }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        @if(!empty($filterOptions['session_types']))
        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_session_type') }}</span>
            <select name="session_type" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                @foreach($filterOptions['session_types'] as $row)
                    <option value="{{ $row['key'] }}" @selected(request('session_type') === $row['key'])>
                        {{ $row['label'] }} ({{ $row['count'] }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_experience') }}</span>
            <select name="experience_min" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                <option value="1" @selected((string) request('experience_min') === '1')>{{ __('public.instructors_filter_exp_1') }}</option>
                <option value="3" @selected((string) request('experience_min') === '3')>{{ __('public.instructors_filter_exp_3') }}</option>
                <option value="5" @selected((string) request('experience_min') === '5')>{{ __('public.instructors_filter_exp_5') }}</option>
                <option value="10" @selected((string) request('experience_min') === '10')>{{ __('public.instructors_filter_exp_10') }}</option>
            </select>
        </div>

        @if(($filterOptions['video_count'] ?? 0) > 0)
        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_video') }}</span>
            <select name="has_video" class="sana-inst-filter-select">
                <option value="">{{ __('public.filter_all') }}</option>
                <option value="yes" @selected(request('has_video') === 'yes')>{{ __('public.instructors_filter_has_video') }} ({{ $filterOptions['video_count'] }})</option>
                <option value="no" @selected(request('has_video') === 'no')>{{ __('public.instructors_filter_no_video') }}</option>
            </select>
        </div>
        @endif

        <div class="sana-cat-filter-group">
            <span class="sana-cat-filter-group__label">{{ __('public.instructors_filter_sort') }}</span>
            <select name="sort" class="sana-inst-filter-select">
                <option value="" @selected(! request('sort'))>{{ __('public.instructors_sort_recommended') }}</option>
                <option value="bookable_first" @selected(request('sort') === 'bookable_first')>{{ __('public.instructors_sort_bookable') }}</option>
                <option value="experience_desc" @selected(request('sort') === 'experience_desc')>{{ __('public.instructors_sort_experience') }}</option>
                <option value="courses_desc" @selected(request('sort') === 'courses_desc')>{{ __('public.instructors_sort_courses') }}</option>
                <option value="name" @selected(request('sort') === 'name')>{{ __('public.instructors_sort_name') }}</option>
            </select>
        </div>

        <div class="sana-inst-filters-actions">
            <button type="submit" class="sana-btn sana-btn--purple sana-btn--sm" style="width:100%">
                <i class="fas fa-check"></i> {{ __('public.instructors_apply_filters') }}
            </button>
            @if($hasActiveFilters)
                <a href="{{ route('public.instructors.index', $queryBase) }}" class="sana-cat-reset">
                    <i class="fas fa-undo"></i> {{ __('public.filter_reset') }}
                </a>
            @endif
        </div>
    </form>
</aside>
