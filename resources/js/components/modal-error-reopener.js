// /**
//  * modal-error-reopener.js
//  * Fungsi ini otomatis membuka modal yang memiliki atribut data-open-on-error="true"
//  * setelah redirect karena kegagalan validasi.
//  */
//
// export function initModalErrorReopener() {
//     console.log('[Modal Reopener] Inisialisasi dimulai...');
//
//     // Jalankan segera jika DOM sudah ready
//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', openErrorModals);
//     } else {
//         openErrorModals();
//     }
//
//     // Backup: Jalankan lagi setelah delay untuk memastikan library modal sudah loaded
//     setTimeout(openErrorModals, 200);
// }
//
// function openErrorModals() {
//     const errorModals = document.querySelectorAll('[data-open-on-error="true"]');
//
//     console.log('[Modal Reopener] Mencari modal dengan error...', errorModals.length, 'ditemukan');
//
//     if (errorModals.length === 0) {
//         return;
//     }
//
//     errorModals.forEach(modalElement => {
//         console.log('[Modal Reopener] Membuka modal:', modalElement.id);
//
//         // Gunakan timeout untuk memastikan semua library sudah ready
//         setTimeout(() => {
//             openModalProgrammatically(modalElement);
//         }, 150);
//     });
// }
//
// function openModalProgrammatically(modalElement) {
//     console.log('[Modal Reopener] Memproses modal:', modalElement.id);
//
//     // Method 1: Coba trigger button/trigger yang membuka modal ini
//     const triggerId = modalElement.id.replace('-modal', '');
//     const triggerButton = document.querySelector(`[data-target="#${modalElement.id}"]`);
//
//     if (triggerButton) {
//         console.log('[Modal Reopener] Trigger button ditemukan, mengklik...');
//         triggerButton.click();
//
//         // Fokus ke input setelah modal terbuka
//         setTimeout(() => focusFirstInput(modalElement), 300);
//         return;
//     }
//
//     console.log('[Modal Reopener] Trigger button tidak ditemukan, membuka manual...');
//
//     // Method 2: Buka manual dengan manipulasi DOM
//     // Tampilkan modal container
//     modalElement.style.display = 'flex';
//     modalElement.classList.add('is-open');
//     modalElement.classList.remove('opacity-0', 'pointer-events-none', 'invisible', 'hidden');
//
//     // Trigger transition dengan delay kecil
//     requestAnimationFrame(() => {
//         requestAnimationFrame(() => {
//             // Tampilkan overlay
//             const overlay = modalElement.querySelector('.modal-overlay');
//             if (overlay) {
//                 overlay.style.opacity = '1';
//                 overlay.classList.remove('opacity-0');
//             }
//
//             // Tampilkan content dengan animasi
//             const modalContent = modalElement.querySelector('.modal-content');
//             if (modalContent) {
//                 modalContent.classList.remove('opacity-0', 'invisible', 'hidden');
//                 modalContent.style.transform = 'scale(1)';
//                 modalContent.style.opacity = '1';
//             }
//         });
//     });
//
//     // Prevent body scroll
//     document.body.classList.add('is-modal-open');
//     document.body.style.overflow = 'hidden';
//
//     // Setup close handlers
//     setupCloseHandlers(modalElement);
//
//     // Fokus ke input
//     setTimeout(() => focusFirstInput(modalElement), 300);
//
//     console.log('[Modal Reopener] Modal berhasil dibuka secara manual');
// }
//
// function focusFirstInput(modalElement) {
//     // Cari input dengan error terlebih dahulu
//     const errorInput = modalElement.querySelector(
//         '.text-error, .border-error, [aria-invalid="true"], .is-invalid, input:invalid'
//     );
//
//     const targetInput = errorInput || modalElement.querySelector(
//         'input:not([type="hidden"]):not([readonly]):not([disabled]), select:not([disabled]), textarea:not([disabled])'
//     );
//
//     if (targetInput) {
//         console.log('[Modal Reopener] Fokus ke input:', targetInput.name || targetInput.id);
//         targetInput.focus();
//
//         // Scroll ke input jika perlu
//         targetInput.scrollIntoView({
//             behavior: 'smooth',
//             block: 'center',
//             inline: 'nearest'
//         });
//
//         // Jika text input, select text
//         if (targetInput.type === 'text' || targetInput.tagName === 'TEXTAREA') {
//             targetInput.select();
//         }
//     }
// }
//
// function setupCloseHandlers(modalElement) {
//     // Handler untuk tombol close (hanya setup sekali)
//     if (modalElement.dataset.closeHandlerSetup) return;
//     modalElement.dataset.closeHandlerSetup = 'true';
//
//     const closeButtons = modalElement.querySelectorAll('[data-close-modal]');
//     closeButtons.forEach(btn => {
//         btn.addEventListener('click', (e) => {
//             e.preventDefault();
//             closeModal(modalElement);
//         });
//     });
//
//     // Handler untuk click di overlay
//     const overlay = modalElement.querySelector('.modal-overlay');
//     if (overlay) {
//         overlay.addEventListener('click', (e) => {
//             if (e.target === overlay) {
//                 closeModal(modalElement);
//             }
//         });
//     }
//
//     // Handler untuk ESC key
//     const escHandler = (e) => {
//         if (e.key === 'Escape' && modalElement.classList.contains('is-open')) {
//             closeModal(modalElement);
//         }
//     };
//     document.addEventListener('keydown', escHandler);
//
//     // Simpan reference untuk cleanup
//     modalElement._escHandler = escHandler;
// }
//
// function closeModal(modalElement) {
//     console.log('[Modal Reopener] Menutup modal:', modalElement.id);
//
//     modalElement.classList.remove('is-open');
//     modalElement.classList.add('opacity-0', 'pointer-events-none');
//
//     // Animate out
//     const modalContent = modalElement.querySelector('.modal-content');
//     if (modalContent) {
//         modalContent.style.transform = 'scale(0.95)';
//         modalContent.style.opacity = '0';
//     }
//
//     setTimeout(() => {
//         modalElement.style.display = 'none';
//     }, 300);
//
//     // Kembalikan body scroll jika tidak ada modal lain yang terbuka
//     const openModals = document.querySelectorAll('.modal.is-open');
//     if (openModals.length === 0) {
//         document.body.classList.remove('is-modal-open');
//         document.body.style.overflow = '';
//     }
//
//     // Cleanup ESC handler
//     if (modalElement._escHandler) {
//         document.removeEventListener('keydown', modalElement._escHandler);
//         delete modalElement._escHandler;
//     }
// }
/**
 * modal-error-reopener.js
 * Otomatis membuka modal dengan data-open-on-error="true"
 */

export function initModalErrorReopener() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', openErrorModals);
    } else {
        openErrorModals();
    }

    setTimeout(openErrorModals, 200);
}

function openErrorModals() {
    const errorModals = document.querySelectorAll('[data-open-on-error="true"]');

    // Filter modal yang punya ID valid
    const validModals = Array.from(errorModals).filter(modal => modal.id && modal.id.trim() !== '');

    if (validModals.length === 0) return;

    validModals.forEach(modalElement => {
        setTimeout(() => {
            openModalProgrammatically(modalElement);
        }, 150);
    });
}

function openModalProgrammatically(modalElement) {
    const triggerButton = document.querySelector(`[data-target="#${modalElement.id}"]`);

    if (triggerButton) {
        triggerButton.click();
        setTimeout(() => focusFirstInput(modalElement), 300);
        return;
    }

    modalElement.style.display = 'flex';
    modalElement.classList.add('is-open');
    modalElement.classList.remove('opacity-0', 'pointer-events-none', 'invisible', 'hidden');

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            const overlay = modalElement.querySelector('.modal-overlay');
            if (overlay) {
                overlay.style.opacity = '1';
                overlay.classList.remove('opacity-0');
            }

            const modalContent = modalElement.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.remove('opacity-0', 'invisible', 'hidden');
                modalContent.style.transform = 'scale(1)';
                modalContent.style.opacity = '1';
            }
        });
    });

    document.body.classList.add('is-modal-open');
    document.body.style.overflow = 'hidden';

    setTimeout(() => focusFirstInput(modalElement), 300);
}

function focusFirstInput(modalElement) {
    const errorInput = modalElement.querySelector('.border-error, [aria-invalid="true"]');
    const targetInput = errorInput || modalElement.querySelector('input:not([type="hidden"]):not([readonly]), select, textarea');

    if (targetInput) {
        targetInput.focus();
        targetInput.scrollIntoView({ behavior: 'smooth', block: 'center' });

        if (targetInput.type === 'text' || targetInput.tagName === 'TEXTAREA') {
            targetInput.select();
        }
    }
}