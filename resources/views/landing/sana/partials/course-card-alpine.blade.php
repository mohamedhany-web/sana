{{--
    Premium course card — Alpine.js
    Usage: inside x-for="course in ..." on a page with sanaCoursesCatalog helpers
    Optional: add class sana-course-card--featured on the article for featured variant
--}}
<article class="sana-course-card"
         :class="course.is_featured && 'sana-course-card--featured'">
    <div class="sana-course-card__media">
        <a :href="courseUrl(course.id)" class="sana-course-card__img-link" tabindex="-1" aria-hidden="true">
            <img :src="cardImage(course)" :alt="course.title" loading="lazy">
        </a>
        <div class="sana-course-card__shine" aria-hidden="true"></div>
        <div class="sana-course-card__badges">
            <span class="sana-course-card__badge sana-course-card__badge--subject"
                  x-text="catLabel(course)" x-show="catLabel(course)"></span>
            <span class="sana-course-card__badge sana-course-card__badge--level"
                  x-text="course.level_label"></span>
            <span class="sana-course-card__badge sana-course-card__badge--featured"
                  x-show="course.is_featured" x-text="labels.featured"></span>
        </div>
        <button type="button"
                class="sana-course-card__fav"
                :class="isSaved(course.id) && 'is-saved'"
                :aria-label="isSaved(course.id) ? labels.unsave : labels.save"
                @click.prevent="toggleFavorite(course.id, $event)">
            <i class="fa-heart" :class="isSaved(course.id) ? 'fas' : 'far'"></i>
        </button>
    </div>

    <div class="sana-course-card__body">
        <h3 class="sana-course-card__title">
            <a :href="courseUrl(course.id)" x-text="course.title"></a>
        </h3>
        <p class="sana-course-card__desc"
           x-text="(course.description || 'دورة تعليمية تفاعلية مصمّمة لتجربة تعلّم ممتعة وفعّالة.').substring(0, 110)"></p>

        <div class="sana-course-card__instructor" x-show="course.instructor">
            <template x-if="course.instructor && course.instructor.avatar_url">
                <img class="sana-course-card__avatar" :src="course.instructor.avatar_url" alt="">
            </template>
            <template x-if="course.instructor && !course.instructor.avatar_url">
                <span class="sana-course-card__avatar sana-course-card__avatar--initial"
                      x-text="(course.instructor && course.instructor.name ? course.instructor.name : 'م').charAt(0)"></span>
            </template>
            <div class="sana-course-card__instructor-info">
                <span class="sana-course-card__instructor-label">المعلّم</span>
                <span class="sana-course-card__instructor-name" x-text="course.instructor ? course.instructor.name : 'معلّم'"></span>
            </div>
        </div>

        <div class="sana-course-card__stats">
            <span class="sana-course-card__stat sana-course-card__stat--rating">
                <i class="fas fa-star"></i>
                <strong x-text="course.rating"></strong>
            </span>
            <span class="sana-course-card__stat">
                <i class="fas fa-users"></i>
                <span x-text="formatCount(course.students_count)"></span>
            </span>
            <span class="sana-course-card__stat" x-show="course.duration_hours">
                <i class="far fa-clock"></i>
                <span x-text="course.duration_hours + ' ' + labels.hours"></span>
            </span>
            <span class="sana-course-card__stat" x-show="course.lectures_count">
                <i class="fas fa-layer-group"></i>
                <span x-text="course.lectures_count + ' ' + labels.lecture"></span>
            </span>
        </div>
    </div>

    <div class="sana-course-card__footer">
        <div class="sana-course-card__progress" x-show="course.is_enrolled">
            <div class="sana-course-card__progress-head">
                <span>تقدّمك في الدورة</span>
                <strong x-text="Math.round(course.enrollment_progress || 0) + '%'"></strong>
            </div>
            <div class="sana-course-card__progress-track">
                <span :style="'width:' + (course.enrollment_progress || 0) + '%'"></span>
            </div>
        </div>
        <div class="sana-course-card__actions">
            <div class="sana-course-card__price-wrap">
                <template x-if="priceHtml(course) === 'free'">
                    <span class="sana-course-card__price sana-course-card__price--free">
                        <i class="fas fa-gift"></i> <span x-text="labels.free"></span>
                    </span>
                </template>
                <template x-if="priceHtml(course) === 'whatsapp'">
                    <span class="sana-course-card__price sana-course-card__price--contact">
                        <i class="fab fa-whatsapp"></i> تواصل للسعر
                    </span>
                </template>
                <template x-if="priceHtml(course) === 'sale' || priceHtml(course) === 'normal'">
                    <span class="sana-course-card__price" x-html="priceLabel(course)"></span>
                </template>
            </div>
            <a :href="courseUrl(course.id)" class="sana-course-card__cta">
                <span x-text="course.is_enrolled ? 'تابع التعلّم' : 'ابدأ الآن'"></span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
</article>
