/**
 * File: resources/js/components/form/password-toggle.js
 * Handle toggling visibility for password inputs using data attributes.
 */

/**
 * Mengubah tipe input (password/text) dan ikon.
 */
function togglePasswordVisibility(button) {
    // Cari wrapper terdekat. Kita menggunakan '.password-wrapper' dari Blade.
    const wrapper = button.closest('.password-wrapper');
    if (!wrapper) return;

    // Cari input di dalam wrapper (bisa type=password atau type=text)
    const input = wrapper.querySelector('input[type="password"], input[type="text"]');
    // Cari ikon di dalam tombol yang diklik
    const icon = button.querySelector('i.fa-eye, i.fa-eye-slash');

    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash'); // Mata tertutup
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye'); // Mata terbuka
    }
}

/**
 * Handle click event pada tombol toggle.
 */
function handleToggleClick(event) {
    event.preventDefault();
    // Menggunakan currentTarget karena listener dipasang pada tombol
    togglePasswordVisibility(event.currentTarget);
}

/**
 * Initialize password toggle listeners
 */
export function initPasswordToggle(container = document) {
    console.log('👁️ [Password Toggle] Initializing...');

    // Cari semua tombol dengan hook data-toggle-password
    const toggleButtons = container.querySelectorAll('button[data-toggle-password="true"]');

    if (toggleButtons.length === 0) {
        return;
    }

    toggleButtons.forEach(button => {
        // Hapus listener lama jika ada (untuk reinit)
        button.removeEventListener('click', handleToggleClick);
        // Tambah listener baru
        button.addEventListener('click', handleToggleClick);
    });

    console.log(`👁️ [Password Toggle] ✓ Initialized ${toggleButtons.length} toggle button(s)`);
}

// Export untuk global access
window.PasswordToggleHandler = {
    init: initPasswordToggle,
};