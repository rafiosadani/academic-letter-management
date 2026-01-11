/**
 * modal-form-reset.js
 * Auto reset form ketika modal ditutup
 */
export function initModalFormReset() {
    setupModalCloseHandlers();
}

function setupModalCloseHandlers() {
    const modals = document.querySelectorAll('.modal:has(form)');

    modals.forEach(modal => {
        // Skip jika sudah di-setup atau modal tidak punya ID
        if (modal.dataset.resetHandlerSetup || !modal.id) return;
        modal.dataset.resetHandlerSetup = 'true';

        // Tombol close (X)
        const closeButtons = modal.querySelectorAll('[data-close-modal]');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                setTimeout(() => resetModalForm(modal), 300);
            }, { once: false }); // Biarkan multiple clicks
        });

        // Overlay click
        const overlay = modal.querySelector('.modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    setTimeout(() => resetModalForm(modal), 300);
                }
            });
        }

        // ESC key
        const escHandler = (e) => {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) {
                setTimeout(() => resetModalForm(modal), 300);
            }
        };
        document.addEventListener('keydown', escHandler);
        modal._escHandler = escHandler;
    });
}

function resetModalForm(modal) {
    if (modal.classList.contains('is-open')) return;

    const form = modal.querySelector('form');
    if (!form) return;

    // Reset form HTML5
    form.reset();

    // Hapus validation errors
    modal.querySelectorAll('.text-error, .text-tiny-plus.text-error, .text-tiny.text-error, span.text-error').forEach(el => el.remove());

    modal.querySelectorAll('.border-error').forEach(input => {
        input.classList.remove('border-error');
        if (input.classList.contains('form-input')) {
            input.classList.add('border-slate-300');
        }
    });

    modal.querySelectorAll('.alert.border-error, .alert.text-error').forEach(alert => alert.remove());

    // Reset inputs
    modal.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], input[type="password"], input[type="tel"], input[type="url"], input[type="search"], textarea').forEach(input => {
        input.value = '';
    });

    // Reset checkboxes (kecuali yang punya data-keep-state)
    modal.querySelectorAll('input[type="checkbox"]:not([data-keep-state])').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.indeterminate = false;
    });

    modal.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });

    modal.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });

    // Trigger custom event
    modal.dispatchEvent(new CustomEvent('modal:reset', {
        bubbles: true,
        detail: { modal, form }
    }));
}

export function resetModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        resetModalForm(modal);
    }
}