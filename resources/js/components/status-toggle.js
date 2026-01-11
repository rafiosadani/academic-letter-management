/**
 * Status Toggle Handler - Compatible dengan Lineone Template
 * File: resources/js/components/status-toggle.js
 *
 * Support data-target dengan atau tanpa # (auto detect)
 */

export function initStatusToggle(container = document) {
    const checkboxes = container.querySelectorAll('.status-toggle[data-toggle="modal"]');

    if (checkboxes.length === 0) {
        return;
    }

    checkboxes.forEach(checkbox => {
        // Skip jika sudah di-setup
        if (checkbox.dataset.statusToggleInitialized === 'true') {
            return;
        }

        setupStatusToggle(checkbox);
        checkbox.dataset.statusToggleInitialized = 'true';
    });
}

function setupStatusToggle(checkbox) {
    // FIX: Support data-target dengan atau tanpa #
    const targetSelector = checkbox.dataset.target;
    const modalId = getModalId(targetSelector);

    const originalState = checkbox.dataset.originalState === '1';

    // Verify modal exists
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('[Status Toggle] ❌ Modal not found:', modalId);
        return;
    }

    // Store current state
    checkbox.dataset.currentState = originalState ? '1' : '0';

    // Handle checkbox change
    checkbox.addEventListener('change', function(e) {
        const newState = this.checked;

        // Store temporary state
        this.dataset.tempState = newState ? '1' : '0';

        // Update hidden input value in form
        updateFormStatusValue(this, newState, modalId);
    });

    // Setup modal cancel handlers
    setupModalCancelHandlers(checkbox, modalId);
}

/**
 * Extract modal ID dari target (support dengan/tanpa #)
 */
function getModalId(targetSelector) {
    if (!targetSelector) return null;

    // Remove # jika ada
    return targetSelector.replace(/^#/, '');
}

function setupModalCancelHandlers(checkbox, modalId) {
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.error('[Status Toggle] Modal not found:', modalId);
        return;
    }

    // Get cancel elements
    const cancelButtons = modal.querySelectorAll('[data-close-modal]');
    const overlay = modal.querySelector('.modal-overlay');

    // Create cancel handler (bound to this checkbox)
    const handleCancel = () => {
        // Check if modal is actually visible
        const isVisible = modal.classList.contains('show');

        if (isVisible) {
            revertCheckbox(checkbox, modalId);
        }
    };

    // Remove old listeners jika ada (prevent duplicate)
    if (checkbox._cancelHandlers) {
        const oldHandlers = checkbox._cancelHandlers;

        // Remove from buttons
        if (oldHandlers.buttons) {
            oldHandlers.buttons.forEach((btn, handler) => {
                btn.removeEventListener('click', handler);
            });
        }

        // Remove from overlay
        if (oldHandlers.overlay) {
            oldHandlers.overlay.element.removeEventListener('click', oldHandlers.overlay.handler);
        }
    }

    // Store handler references untuk cleanup
    const buttonHandlers = new Map();

    // Attach to cancel buttons
    cancelButtons.forEach(button => {
        button.addEventListener('click', handleCancel);
        buttonHandlers.set(button, handleCancel);
    });

    // Attach to overlay
    let overlayHandler = null;
    if (overlay) {
        overlayHandler = { element: overlay, handler: handleCancel };
        overlay.addEventListener('click', handleCancel);
    }

    // Store all handlers
    checkbox._cancelHandlers = {
        buttons: buttonHandlers,
        overlay: overlayHandler
    };

    // ESC key handler yang specific untuk modal ini
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            const isVisible = modal.classList.contains('show');

            if (isVisible) {
                revertCheckbox(checkbox, modalId);
            }
        }
    };

    // Remove old ESC handler jika ada
    if (checkbox._escHandler) {
        document.removeEventListener('keydown', checkbox._escHandler);
    }

    // Store ESC handler reference
    checkbox._escHandler = handleEscape;
    document.addEventListener('keydown', handleEscape);
}

function revertCheckbox(checkbox, modalId) {
    const currentState = checkbox.dataset.currentState === '1';

    // Revert checkbox ke state sebelumnya
    checkbox.checked = currentState;

    // Revert form hidden input juga
    updateFormStatusValue(checkbox, currentState, modalId);

    // Update label text jika ada
    updateStatusLabel(checkbox, currentState);
}

function updateFormStatusValue(checkbox, newState, modalId) {
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.error('[Status Toggle] Modal not found for form update:', modalId);
        return;
    }

    // Find form - check attribute form di button submit
    const confirmButton = modal.querySelector('button[type="submit"]');
    const formId = confirmButton?.getAttribute('form');

    const form = formId ? document.getElementById(formId) : modal.querySelector('form');

    if (!form) {
        console.error('[Status Toggle] ❌ Form not found. FormID:', formId);
        // console.log('[Status Toggle] Modal HTML:', modal.innerHTML.substring(0, 200)); // Log ini dihilangkan
        return;
    }

    // Update hidden input value
    const statusInput = form.querySelector('input[name="status"]');
    if (statusInput) {
        statusInput.value = newState ? '1' : '0';
    } else {
        console.error('[Status Toggle] ❌ Status input not found in form');
        // console.log('[Status Toggle] Form HTML:', form.innerHTML); // Log ini dihilangkan
    }
}

function updateStatusLabel(checkbox, isActive) {
    // Find label text element
    const label = checkbox.closest('label')?.querySelector('.text-tiny');

    if (!label) {
        console.warn('[Status Toggle] Label not found'); // Console.warn dipertahankan
        return;
    }

    // Update text
    label.textContent = isActive ? 'Aktif' : 'Nonaktif';

    // Update color
    label.classList.remove('text-success', 'text-warning');
    label.classList.add(isActive ? 'text-success' : 'text-warning');
}

/**
 * Reinitialize untuk dynamic content
 */
export function reinitStatusToggle(container = document) {
    // Clear all initialized flags
    const checkboxes = container.querySelectorAll('.status-toggle[data-toggle="modal"]');
    checkboxes.forEach(cb => {
        delete cb.dataset.statusToggleInitialized;

        // Cleanup old handlers
        if (cb._cancelHandlers) {
            const handlers = cb._cancelHandlers;

            // Remove button handlers
            if (handlers.buttons) {
                handlers.buttons.forEach((handler, btn) => {
                    btn.removeEventListener('click', handler);
                });
            }

            // Remove overlay handler
            if (handlers.overlay) {
                handlers.overlay.element.removeEventListener('click', handlers.overlay.handler);
            }

            delete cb._cancelHandlers;
        }

        // Remove ESC handler
        if (cb._escHandler) {
            document.removeEventListener('keydown', cb._escHandler);
            delete cb._escHandler;
        }
    });

    initStatusToggle(container);
}