/**
 * universal-popper-fix.js
 * * Fungsi ini menginisialisasi Event Listener universal untuk menangani
 * konflik antara popper (dropdown menu) dan modal.
 * * Target: Tombol di dalam popper yang memiliki atribut data-action="popper-modal-trigger".
 * Aksi: Mensimulasikan klik pada tombol pemicu popper (.popper-ref) untuk
 * mereset status internalnya, memastikan menu bisa dibuka kembali setelah modal ditutup.
 */
export function initUniversalPopperFix() {
    // Memasang listener pada document untuk event delegation
    document.addEventListener('click', function(event) {
        // Cek apakah elemen yang diklik adalah tombol yang memicu modal dari dalam popper
        const targetElement = event.target.closest('[data-action="popper-modal-trigger"]');

        if (targetElement) {
            // Cari kontainer menu terdekat (misalnya elemen dengan class 'inline-flex')
            const menuContainer = targetElement.closest('.inline-flex');

            if (menuContainer) {
                // Cari tombol pemicu 3 titik (popper-ref)
                const popperRef = menuContainer.querySelector('.popper-ref');

                if (popperRef) {
                    // Simulasikan klik pada popper-ref untuk mereset status internalnya.
                    // Penundaan 10ms diperlukan agar event klik modal sempat terdaftar.
                    setTimeout(() => {
                        popperRef.click();
                    }, 10);

                    // Hapus atribut aria-expanded sebagai pembersihan tambahan
                    popperRef.removeAttribute('aria-expanded');
                }
            }
        }
    });

    console.log('Universal Popper Fix Initialized.');
}