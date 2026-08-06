<div class="sana-cat-filter-group">
    <span class="sana-cat-filter-group__label">{{ __('public.filter_category') }}</span>
    <button type="button" class="sana-cat-filter-opt" :class="selectedCategoryId === '' && 'is-active'" @click="selectedCategoryId = ''">
        <span>{{ __('public.all_course_categories') }}</span>
        <span class="count" x-text="courses.length"></span>
    </button>
    <template x-for="cat in categories" :key="cat.id">
        <button type="button" class="sana-cat-filter-opt"
                :class="selectedCategoryId === String(cat.id) && 'is-active'"
                @click="selectedCategoryId = String(cat.id)">
            <span x-text="cat.name"></span>
            <span class="count" x-text="countForCategory(cat.id)"></span>
        </button>
    </template>
</div>

<div class="sana-cat-filter-group" x-show="years.length > 0">
    <span class="sana-cat-filter-group__label">{{ __('public.filter_grade') }}</span>
    <button type="button" class="sana-cat-filter-opt" :class="selectedYearId === '' && 'is-active'" @click="selectedYearId = ''">
        <span>{{ __('public.filter_all') }}</span>
    </button>
    <template x-for="y in years" :key="y.id">
        <button type="button" class="sana-cat-filter-opt"
                :class="selectedYearId === String(y.id) && 'is-active'"
                @click="selectedYearId = String(y.id)">
            <span x-text="y.name"></span>
        </button>
    </template>
</div>

<div class="sana-cat-filter-group" x-show="subjects.length > 0">
    <span class="sana-cat-filter-group__label">{{ __('public.filter_subject') }}</span>
    <button type="button" class="sana-cat-filter-opt" :class="selectedSubjectId === '' && 'is-active'" @click="selectedSubjectId = ''">
        <span>{{ __('public.filter_all') }}</span>
    </button>
    <template x-for="s in subjects" :key="s.id">
        <button type="button" class="sana-cat-filter-opt"
                :class="selectedSubjectId === String(s.id) && 'is-active'"
                @click="selectedSubjectId = String(s.id)">
            <span x-text="s.name"></span>
        </button>
    </template>
</div>

<div class="sana-cat-filter-group">
    <span class="sana-cat-filter-group__label">{{ __('public.filter_level') }}</span>
    <button type="button" class="sana-cat-filter-opt" :class="selectedLevel === '' && 'is-active'" @click="selectedLevel = ''"><span>{{ __('public.filter_all') }}</span></button>
    <button type="button" class="sana-cat-filter-opt" :class="selectedLevel === 'beginner' && 'is-active'" @click="selectedLevel = 'beginner'"><span>{{ __('public.level_beginner') }}</span></button>
    <button type="button" class="sana-cat-filter-opt" :class="selectedLevel === 'intermediate' && 'is-active'" @click="selectedLevel = 'intermediate'"><span>{{ __('public.level_intermediate') }}</span></button>
    <button type="button" class="sana-cat-filter-opt" :class="selectedLevel === 'advanced' && 'is-active'" @click="selectedLevel = 'advanced'"><span>{{ __('public.level_advanced') }}</span></button>
</div>

<div class="sana-cat-filter-group" x-show="instructors.length > 0">
    <span class="sana-cat-filter-group__label">{{ __('public.filter_instructor') }}</span>
    <button type="button" class="sana-cat-filter-opt" :class="selectedInstructorId === '' && 'is-active'" @click="selectedInstructorId = ''"><span>{{ __('public.filter_all') }}</span></button>
    <template x-for="inst in instructors" :key="inst.id">
        <button type="button" class="sana-cat-filter-opt"
                :class="selectedInstructorId === String(inst.id) && 'is-active'"
                @click="selectedInstructorId = String(inst.id)">
            <span x-text="inst.name"></span>
        </button>
    </template>
</div>

<div class="sana-cat-filter-group">
    <span class="sana-cat-filter-group__label">{{ __('public.filter_price') }}</span>
    <label class="sana-cat-filter-check"><input type="radio" name="priceFilter" value="all" x-model="priceFilter"> <span>{{ __('public.filter_all') }}</span></label>
    <label class="sana-cat-filter-check"><input type="radio" name="priceFilter" value="free" x-model="priceFilter"> <span>{{ __('public.free_price') }}</span></label>
    <label class="sana-cat-filter-check"><input type="radio" name="priceFilter" value="paid" x-model="priceFilter"> <span>{{ __('public.filter_paid') }}</span></label>
</div>

<div class="sana-cat-filter-group">
    <span class="sana-cat-filter-group__label">{{ __('public.filter_duration') }}</span>
    <label class="sana-cat-filter-check"><input type="radio" name="durationFilter" value="all" x-model="durationFilter"> <span>{{ __('public.filter_duration_any') }}</span></label>
    <label class="sana-cat-filter-check"><input type="radio" name="durationFilter" value="short" x-model="durationFilter"> <span>{{ __('public.filter_duration_short') }}</span></label>
    <label class="sana-cat-filter-check"><input type="radio" name="durationFilter" value="medium" x-model="durationFilter"> <span>{{ __('public.filter_duration_medium') }}</span></label>
    <label class="sana-cat-filter-check"><input type="radio" name="durationFilter" value="long" x-model="durationFilter"> <span>{{ __('public.filter_duration_long') }}</span></label>
</div>

<button type="button" class="sana-cat-reset" @click="resetFilters()">
    <i class="fas fa-rotate-left"></i> {{ __('public.filter_reset') }}
</button>
