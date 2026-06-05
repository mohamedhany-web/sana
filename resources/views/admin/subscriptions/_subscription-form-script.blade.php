@php $subForJs = $subscription ?? null; @endphp
<script>
    function subscriptionAdminForm(initialRole) {
        var PLAN_FEATURES = @json($planFeatures);
        var PLAN_META = @json($planApplyMeta);
        var STUDENT_PLAN_META = @json($studentPlanApplyMeta ?? []);
        var MANUAL_DEFAULT_FEATURES = @json($manualDefaultFeatures);
        var MANUAL_TEMPLATE_LIMITS = @json($manualTemplateLimits);
        var DEFAULT_TUTOR_HOURS = @json((int) ($defaultTutorHours ?? 0));
        var DEFAULT_STUDENT_PLAN_KEY = @json($defaultStudentPlanKey ?? '');

        function syncSubscriptionFeatureCheckboxes(featureList) {
            var set = {};
            (featureList || []).forEach(function (f) { set[f] = true; });
            document.querySelectorAll('input[type=checkbox][data-sub-feature]').forEach(function (cb) {
                var fk = cb.getAttribute('data-sub-feature');
                cb.checked = !!set[fk];
            });
        }

        function computeEndDateFromStart(startYmd, billingCycle) {
            if (!startYmd) return '';
            var parts = startYmd.split('-');
            if (parts.length !== 3) return '';
            var d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            if (billingCycle === 'yearly') {
                d.setFullYear(d.getFullYear() + 1);
            } else if (billingCycle === 'quarterly') {
                d.setMonth(d.getMonth() + 3);
            } else {
                d.setMonth(d.getMonth() + 1);
            }
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + day;
        }

        function syncEndDateFromBilling(billingCycle) {
            var startEl = document.querySelector('input[name=start_date]');
            var endEl = document.querySelector('input[name=end_date]');
            if (!startEl || !endEl) return;
            var computed = computeEndDateFromStart(startEl.value, billingCycle);
            if (computed) endEl.value = computed;
        }

        return {
            selectedPlan: @json(old('teacher_plan_key', $subForJs?->teacher_plan_key ?? '')),
            studentPackageMode: @json($initialStudentPackageMode ?? 'template'),
            subscriberRole: initialRole || '',
            form: {
                subscription_type: @json(old('subscription_type', $subForJs?->subscription_type ?? 'monthly')),
                plan_name: @json(old('plan_name', $subForJs?->plan_name ?? '')),
                price: @json(old('price', $subForJs?->price ?? '')),
                billing_cycle: @json(old('billing_cycle', $subForJs?->billing_cycle ?? 'monthly')),
            },
            limits: @json($initialLimits),
            isStudent() {
                return this.subscriberRole === 'student';
            },
            isInstructor() {
                return this.subscriberRole === 'instructor' || this.subscriberRole === 'teacher';
            },
            onUserChange() {
                var sel = document.getElementById('mx-user-select');
                if (!sel || sel.selectedIndex < 0) {
                    this.subscriberRole = '';
                    return;
                }
                var opt = sel.options[sel.selectedIndex];
                this.subscriberRole = opt.getAttribute('data-role') || '';
                if (this.isStudent()) {
                    this.studentPackageMode = 'template';
                    if (DEFAULT_STUDENT_PLAN_KEY) {
                        this.applyStudentPlan(DEFAULT_STUDENT_PLAN_KEY);
                    } else {
                        this.selectedPlan = '';
                        this.limits.tutor_lesson_hours = DEFAULT_TUTOR_HOURS;
                        if (!this.form.plan_name) {
                            this.form.plan_name = 'باقة حصص مع المعلمين';
                        }
                    }
                    this.$nextTick(function () {
                        document.querySelectorAll('input[type=checkbox][data-sub-feature]').forEach(function (cb) {
                            cb.checked = false;
                        });
                    });
                } else if (this.isInstructor()) {
                    this.studentPackageMode = 'template';
                }
            },
            setStudentPackageMode(mode) {
                this.studentPackageMode = mode;
                if (mode === 'custom') {
                    this.selectedPlan = '';
                    if (!this.form.plan_name) {
                        this.form.plan_name = 'باقة مخصصة — حصص مع المعلم';
                    }
                    if (!this.limits.tutor_lesson_hours && DEFAULT_TUTOR_HOURS) {
                        this.limits.tutor_lesson_hours = DEFAULT_TUTOR_HOURS;
                    }
                } else if (DEFAULT_STUDENT_PLAN_KEY) {
                    this.applyStudentPlan(DEFAULT_STUDENT_PLAN_KEY);
                }
            },
            applyStudentPlan(key) {
                if (!this.isStudent() || !key || !STUDENT_PLAN_META[key]) return;
                this.studentPackageMode = 'template';
                this.selectedPlan = key;
                var m = STUDENT_PLAN_META[key];
                this.form.subscription_type = m.subscription_type || 'monthly';
                this.form.plan_name = m.plan_name || '';
                this.form.price = parseFloat(m.price) || 0;
                this.form.billing_cycle = m.billing_cycle || 'monthly';
                if (m.limits) {
                    Object.assign(this.limits, m.limits);
                }
                syncEndDateFromBilling(m.billing_cycle || 'monthly');
            },
            init() {
                var self = this;
                var initStudentKey = @json($initialStudentPlanKey ?? '');
                this.$nextTick(function () {
                    if (self.isStudent()) {
                        if (initStudentKey && STUDENT_PLAN_META[initStudentKey]) {
                            self.applyStudentPlan(initStudentKey);
                        } else if (self.studentPackageMode === 'custom') {
                            self.selectedPlan = '';
                        }
                    } else if (self.isInstructor() && self.selectedPlan) {
                        self.applyPlan({ target: { value: self.selectedPlan } });
                    } else if (self.isInstructor() && MANUAL_DEFAULT_FEATURES.length) {
                        syncSubscriptionFeatureCheckboxes(MANUAL_DEFAULT_FEATURES);
                    }
                });
            },
            applyPlan(event) {
                if (!this.isInstructor()) return;
                var key = event.target ? event.target.value : '';
                if (!key) {
                    this.$nextTick(function () {
                        syncSubscriptionFeatureCheckboxes(MANUAL_DEFAULT_FEATURES);
                    });
                    if (MANUAL_TEMPLATE_LIMITS) {
                        Object.assign(this.limits, MANUAL_TEMPLATE_LIMITS);
                    }
                    return;
                }
                if (!PLAN_FEATURES[key] || !PLAN_META[key]) return;
                var m = PLAN_META[key];
                this.form.subscription_type = m.subscription_type || 'monthly';
                this.form.plan_name = m.plan_name || '';
                this.form.price = parseFloat(m.price) || 0;
                this.form.billing_cycle = m.billing_cycle || 'monthly';
                if (m.limits) {
                    Object.assign(this.limits, m.limits);
                }
                this.$nextTick(function () {
                    syncSubscriptionFeatureCheckboxes(PLAN_FEATURES[key]);
                });
            },
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('mx-user-search');
        var selectEl = document.getElementById('mx-user-select');
        if (!searchInput || !selectEl) return;
        searchInput.addEventListener('input', function () {
            var term = (this.value || '').toLowerCase();
            Array.prototype.forEach.call(selectEl.options, function (opt, idx) {
                if (idx === 0) {
                    opt.hidden = false;
                    return;
                }
                var text = (opt.text || '').toLowerCase();
                opt.hidden = term && text.indexOf(term) === -1;
            });
        });
    });
</script>
