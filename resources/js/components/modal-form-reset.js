// /**
//  * modal-form-reset.js
//  * Auto reset form ketika modal ditutup
//  */
// export function initModalFormReset() {
//     setupModalCloseHandlers();
// }
//
// // Setup modal close handlers untuk reset form
// function setupModalCloseHandlers() {
//     // Cari semua modal yang memiliki form
//     const modals = document.querySelectorAll('.modal:has(form)');
//
//     modals.forEach(modal => {
//         // Skip jika sudah di-setup
//         if (modal.dataset.resetHandlerSetup) return;
//         modal.dataset.resetHandlerSetup = 'true';
//
//         // Tombol close (X)
//         const closeButtons = modal.querySelectorAll('[data-close-modal]');
//         closeButtons.forEach(btn => {
//             btn.addEventListener('click', () => {
//                 setTimeout(() => resetModalForm(modal), 300); // Delay untuk animasi close
//             });
//         });
//
//         // Overlay click
//         const overlay = modal.querySelector('.modal-overlay');
//         if (overlay) {
//             overlay.addEventListener('click', (e) => {
//                 if (e.target === overlay) {
//                     setTimeout(() => resetModalForm(modal), 300);
//                 }
//             });
//         }
//
//         // ESC key
//         const escHandler = (e) => {
//             if (e.key === 'Escape' && modal.classList.contains('is-open')) {
//                 setTimeout(() => resetModalForm(modal), 300);
//             }
//         };
//         document.addEventListener('keydown', escHandler);
//
//         // Simpan reference untuk cleanup
//         modal._escHandler = escHandler;
//     });
//
//     console.log('[Modal Form Reset] Setup complete untuk', modals.length, 'modal(s)');
// }
//
// // Fungsi untuk reset form di dalam modal
// function resetModalForm(modal) {
//     // Skip jika modal masih terbuka (belum selesai animasi close)
//     if (modal.classList.contains('is-open')) return;
//
//     const form = modal.querySelector('form');
//
//     if (!form) return;
//
//     console.log('[Modal Form Reset] Mereset form di modal:', modal.id);
//
//     // Reset form HTML5
//     form.reset();
//
//     // Hapus semua pesan error validation
//     modal.querySelectorAll('.text-error, .text-tiny.text-error').forEach(errorMsg => {
//         errorMsg.remove();
//     });
//
//     // Hapus border error dari input
//     modal.querySelectorAll('.border-error, input.border-error, select.border-error, textarea.border-error').forEach(input => {
//         input.classList.remove('border-error');
//         // Kembalikan class border default
//         if (input.classList.contains('form-input')) {
//             input.classList.add('border-slate-300');
//         }
//     });
//
//     // Hapus alert error box
//     modal.querySelectorAll('.alert.border-error, .alert.text-error').forEach(alert => {
//         alert.remove();
//     });
//
//     // Reset semua checkboxes ke state default (unchecked)
//     modal.querySelectorAll('input[type="checkbox"]:not([data-keep-state])').forEach(checkbox => {
//         checkbox.checked = false;
//         checkbox.indeterminate = false;
//     });
//
//     // Reset semua radio buttons
//     modal.querySelectorAll('input[type="radio"]').forEach(radio => {
//         radio.checked = false;
//     });
//
//     // Reset select ke option pertama
//     modal.querySelectorAll('select').forEach(select => {
//         select.selectedIndex = 0;
//     });
//
//     // Kosongkan input text, email, number, password, tel, url, date, time, datetime-local, dll
//     modal.querySelectorAll(`
//         input[type="text"],
//         input[type="email"],
//         input[type="number"],
//         input[type="password"],
//         input[type="tel"],
//         input[type="url"],
//         input[type="date"],
//         input[type="time"],
//         input[type="datetime-local"],
//         input[type="month"],
//         input[type="week"],
//         input[type="search"],
//         textarea
//     `).forEach(input => {
//         input.value = '';
//     });
//
//     // Reset file input
//     modal.querySelectorAll('input[type="file"]').forEach(input => {
//         input.value = '';
//     });
//
//     // Reset range/slider ke nilai default
//     modal.querySelectorAll('input[type="range"]').forEach(input => {
//         input.value = input.defaultValue || input.min || 0;
//     });
//
//     // Reset color picker ke default
//     modal.querySelectorAll('input[type="color"]').forEach(input => {
//         input.value = input.defaultValue || '#000000';
//     });
//
//     // Trigger custom event untuk reset tambahan jika diperlukan
//     modal.dispatchEvent(new CustomEvent('modal:reset', {
//         bubbles: true,
//         detail: { modal, form }
//     }));
// }
//
// // Export fungsi resetModalForm agar bisa dipanggil manual jika perlu
// export function resetModal(modalId) {
//     const modal = document.getElementById(modalId);
//     if (modal) {
//         resetModalForm(modal);
//     }
// }

/**
 * modal-form-reset.js
 * Auto reset form ketika modal ditutup
 */
export function initModalFormReset() {
    setupModalCloseHandlers();
    console.log('[Modal Form Reset] Initialized');
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

    console.log('[Modal Form Reset] Setup complete untuk', modals.length, 'modal(s)');
}

function resetModalForm(modal) {
    if (modal.classList.contains('is-open')) return;

    const form = modal.querySelector('form');
    if (!form) return;

    console.log('[Modal Form Reset] Mereset form di modal:', modal.id);

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

    console.log('[Modal Form Reset] ✓ Reset complete untuk:', modal.id);
}

export function resetModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        resetModalForm(modal);
    }
}