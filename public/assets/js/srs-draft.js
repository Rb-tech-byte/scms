/**
 * Retains non-sensitive partial form entries locally for 15 minutes (SRS Integrity #26).
 */
(function () {
    const TTL_MS = 15 * 60 * 1000;
    const storageKey = 'scms_draft:' + window.location.pathname;

    function shouldPersistField(el) {
        if (!el.name || el.disabled) return false;
        if (el.name === '_token' || el.name === '_method') return false;
        const type = (el.type || '').toLowerCase();
        return type !== 'password' && type !== 'file' && type !== 'hidden' && type !== 'submit' && type !== 'button';
    }

    function readDraft() {
        try {
            const raw = localStorage.getItem(storageKey);
            if (!raw) return null;
            const parsed = JSON.parse(raw);
            if (!parsed || (Date.now() - parsed.savedAt) > TTL_MS) {
                localStorage.removeItem(storageKey);
                return null;
            }
            return parsed.fields || null;
        } catch (e) {
            return null;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const forms = Array.from(document.querySelectorAll('form[method="POST"], form[method="post"]'))
            .filter(f => !f.action.includes('/logout') && !f.action.includes('/login'));

        if (forms.length === 0) return;

        const form = forms[0];
        const saved = readDraft();

        if (saved && Object.keys(saved).length > 0) {
            const banner = document.createElement('div');
            banner.className = 'alert alert-info py-2 px-3 d-flex justify-content-between align-items-center mb-3';
            banner.innerHTML = '<span>Unsaved draft data recovered (retained up to 15 minutes).</span>' +
                '<div><button type="button" class="btn btn-sm btn-primary me-1" data-restore-draft>Restore</button>' +
                '<button type="button" class="btn btn-sm btn-outline-secondary" data-discard-draft>Discard</button></div>';

            form.parentNode.insertBefore(banner, form);

            banner.querySelector('[data-restore-draft]').addEventListener('click', function () {
                Object.keys(saved).forEach(function (name) {
                    const field = form.querySelector('[name="' + CSS.escape(name) + '"]');
                    if (field && shouldPersistField(field)) {
                        field.value = saved[name];
                    }
                });
                banner.remove();
            });

            banner.querySelector('[data-discard-draft]').addEventListener('click', function () {
                localStorage.removeItem(storageKey);
                banner.remove();
            });
        }

        form.addEventListener('input', function () {
            const fields = {};
            Array.from(form.elements).forEach(function (el) {
                if (shouldPersistField(el) && el.value !== '') {
                    fields[el.name] = el.value;
                }
            });
            try {
                localStorage.setItem(storageKey, JSON.stringify({ savedAt: Date.now(), fields: fields }));
            } catch (e) {}
        });

        form.addEventListener('submit', function () {
            localStorage.removeItem(storageKey);
        });
    });
})();

