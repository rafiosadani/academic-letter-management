// Konstanta yang dibutuhkan (didefinisikan ulang di sini karena merupakan modul terpisah)
const SIDEBAR_OPEN_CLASS = "is-sidebar-open";
const SIDEBAR_TOGGLE_BTN_CLASS = "sidebar-toggle";
const SIDEBAR_CLOSE_BTN_CLASS = "sidebar-close";

function saveSidebarState(body) {
    // Beri waktu sebentar (50ms) untuk memastikan DOM sudah diperbarui
    setTimeout(() => {
        // Simpan status baru ke Local Storage (true jika class SIDEBAR_OPEN_CLASS ada)
        const newStatus = body.classList.contains(SIDEBAR_OPEN_CLASS) ? 'true' : 'false';
        localStorage.setItem('app-panel-open', newStatus);
    }, 50);
}

export function initSidebarState() {
    const storageKey = 'app-panel-open';
    const body = document.body;

    // status awal local storage
    let isOpen = localStorage.getItem(storageKey);

    if (isOpen === null) {
        // apakah blade memberikan status default
        if (body.classList.contains('js-panel-default-open')) {
            isOpen = 'true';
        } else {
            isOpen = 'false';
        }
        // hapus kelas hook blade ketika sudah digunakan
        body.classList.remove('js-panel-default-open');
    }

    if (isOpen === 'true') {
        body.classList.add(SIDEBAR_OPEN_CLASS);
    } else {
        body.classList.remove(SIDEBAR_OPEN_CLASS);
    }

    const toggleButton = document.querySelector(`.${SIDEBAR_TOGGLE_BTN_CLASS}`);
    if (toggleButton) {
        toggleButton.addEventListener('click', () => saveSidebarState(body));
    }

    const closeButtons = document.querySelectorAll(`.${SIDEBAR_CLOSE_BTN_CLASS}`);
    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => saveSidebarState(body));
    });
}