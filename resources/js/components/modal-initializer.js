export function initAllModals() {
    // Memeriksa apakah Class Modal dari lineone sudah tersedia
    if (typeof Modal === 'undefined') {
        console.warn("Modal class (window.Modal) not found. Modal initialization skipped.");
        return;
    }

    const modalElements = document.querySelectorAll('.modal');

    modalElements.forEach(modalEl => {
        if (modalEl.id) {
            new Modal(`#${modalEl.id}`);
        }
    });
}