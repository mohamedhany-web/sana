<?php $subForJs = $subscription ?? null; ?>
<script>
    function subscriptionAdminForm(initialRole) {
        var PLAN_FEATURES = <?php echo json_encode($planFeatures, 15, 512) ?>;
        var PLAN_META = <?php echo json_encode($planApplyMeta, 15, 512) ?>;
        var STUDENT_PLAN_META = <?php echo json_encode($studentPlanApplyMeta ?? [], 15, 512) ?>;
        var MANUAL_DEFAULT_FEATURES = <?php echo json_encode($manualDefaultFeatures, 15, 512) ?>;
        var MANUAL_TEMPLATE_LIMITS = <?php echo json_encode($manualTemplateLimits, 15, 512) ?>;
        var DEFAULT_TUTOR_HOURS = <?php echo json_encode((int) ($defaultTutorHours ?? 0), 15, 512) ?>;
        var DEFAULT_STUDENT_PLAN_KEY = <?php echo json_encode($defaultStudentPlanKey ?? '', 15, 512) ?>;

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
            selectedPlan: <?php echo json_encode(old('teacher_plan_key', $subForJs?->teacher_plan_key ?? ''), 512) ?>,
            studentPackageMode: <?php echo json_encode($initialStudentPackageMode ?? 'template', 15, 512) ?>,
            subscriberRole: initialRole || '',
            form: {
                subscription_type: <?php echo json_encode(old('subscription_type', $subForJs?->subscription_type ?? 'monthly'), 512) ?>,
                plan_name: <?php echo json_encode(old('plan_name', $subForJs?->plan_name ?? ''), 512) ?>,
                price: <?php echo json_encode(old('price', $subForJs?->price ?? ''), 512) ?>,
                billing_cycle: <?php echo json_encode(old('billing_cycle', $subForJs?->billing_cycle ?? 'monthly'), 512) ?>,
            },
            limits: <?php echo json_encode($initialLimits, 15, 512) ?>,
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
                var initStudentKey = <?php echo json_encode($initialStudentPlanKey ?? '', 15, 512) ?>;
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\subscriptions\_subscription-form-script.blade.php ENDPATH**/ ?>