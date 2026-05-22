(function () {
    var STORAGE_KEY = 'sana_saved_courses';

    function getCsrf() {
        var el = document.querySelector('meta[name="csrf-token"]');
        return el ? el.getAttribute('content') : '';
    }

    function readLocal() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            var parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed.map(Number).filter(Boolean) : [];
        } catch (e) {
            return [];
        }
    }

    function writeLocal(ids) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
        } catch (e) {}
    }

    function updateButton(btn, saved) {
        if (!btn) return;
        btn.setAttribute('data-saved', saved ? '1' : '0');
        btn.setAttribute('aria-pressed', saved ? 'true' : 'false');
        btn.classList.toggle('is-saved', saved);
        var icon = btn.querySelector('i');
        if (icon) {
            icon.classList.toggle('far', !saved);
            icon.classList.toggle('fas', saved);
        }
    }

    window.SanaCourseFavorites = {
        isAuthenticated: false,
        loginUrl: '/login',
        toggleUrlTemplate: '/saved-courses/__ID__/toggle',
        syncUrl: '/saved-courses/sync',
        savedIds: [],

        init: function (config) {
            config = config || {};
            this.isAuthenticated = !!config.authenticated;
            this.loginUrl = config.loginUrl || this.loginUrl;
            this.toggleUrlTemplate = config.toggleUrlTemplate || this.toggleUrlTemplate;
            this.syncUrl = config.syncUrl || this.syncUrl;
            this.savedIds = (config.savedIds || []).map(Number);

            if (!this.isAuthenticated) {
                var local = readLocal();
                this.savedIds = Array.from(new Set(this.savedIds.concat(local)));
            } else {
                this.syncFromLocal();
            }

            document.querySelectorAll('[data-course-fav]').forEach(function (btn) {
                var id = parseInt(btn.getAttribute('data-course-id'), 10);
                if (id) updateButton(btn, window.SanaCourseFavorites.isSaved(id));
            });
        },

        isSaved: function (id) {
            return this.savedIds.indexOf(Number(id)) !== -1;
        },

        syncFromLocal: function () {
            var local = readLocal();
            if (!local.length) return;
            var self = this;
            fetch(this.syncUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ ids: local }),
                credentials: 'same-origin',
            })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (data && data.ids) {
                        self.savedIds = data.ids.map(Number);
                        writeLocal([]);
                        document.querySelectorAll('[data-course-fav]').forEach(function (btn) {
                            var cid = parseInt(btn.getAttribute('data-course-id'), 10);
                            updateButton(btn, self.isSaved(cid));
                        });
                    }
                })
                .catch(function () {});
        },

        toggle: function (courseId, btn, onDone) {
            var id = Number(courseId);
            if (!id) return;

            if (!this.isAuthenticated) {
                var local = readLocal();
                var idx = local.indexOf(id);
                var saved;
                if (idx === -1) {
                    local.push(id);
                    saved = true;
                } else {
                    local.splice(idx, 1);
                    saved = false;
                }
                writeLocal(local);
                this.savedIds = local.slice();
                document.querySelectorAll('[data-course-fav][data-course-id="' + id + '"]').forEach(function (b) {
                    updateButton(b, saved);
                });
                this.toast(saved
                    ? 'تم الحفظ محلياً — سجّل دخولك لمزامنة قائمتك'
                    : 'تمت إزالة الكورس من المحفوظات');
                if (onDone) onDone({ saved: saved, ids: this.savedIds.slice() });
                return;
            }

            var url = this.toggleUrlTemplate.replace('__ID__', String(id));
            var self = this;
            btn.disabled = true;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then(function (r) {
                    if (r.status === 401) {
                        window.location.href = self.loginUrl + '?redirect=' + encodeURIComponent(window.location.href);
                        return null;
                    }
                    return r.json();
                })
                .then(function (data) {
                    btn.disabled = false;
                    if (!data) return;
                    self.savedIds = (data.ids || []).map(Number);
                    document.querySelectorAll('[data-course-fav][data-course-id="' + id + '"]').forEach(function (b) {
                        updateButton(b, !!data.saved);
                    });
                    self.toast(data.saved ? 'تم حفظ الكورس' : 'تمت إزالة الكورس من المحفوظات');
                    if (onDone) onDone(data);
                })
                .catch(function () {
                    btn.disabled = false;
                    self.toast('تعذّر تحديث المحفوظات، حاول مرة أخرى');
                });
        },

        handleClick: function (event) {
            event.preventDefault();
            event.stopPropagation();
            var btn = event.currentTarget;
            var id = parseInt(btn.getAttribute('data-course-id'), 10);
            this.toggle(id, btn);
        },

        toast: function (message) {
            var el = document.getElementById('edu-fav-toast');
            if (!el) {
                el = document.createElement('div');
                el.id = 'edu-fav-toast';
                el.setAttribute('role', 'status');
                el.className = 'edu-fav-toast';
                document.body.appendChild(el);
            }
            el.textContent = message;
            el.classList.add('is-visible');
            clearTimeout(el._hideTimer);
            el._hideTimer = setTimeout(function () {
                el.classList.remove('is-visible');
            }, 2800);
        },
    };
})();
