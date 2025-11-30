const initRoleIndex = () => {
    console.log('[Role Index] Initialized');

    // Setup Popper untuk action menu (3 dots)
    setupActionMenuPoppers();
};

/**
 * Setup Popper untuk dropdown action menu
 */
function setupActionMenuPoppers() {
    const dropdownConfig = {
        placement: "bottom",
        modifiers: [{ name: "offset", options: { offset: [0, 4] } }],
    };

    const menus = document.querySelectorAll(".role-action-menu");

    menus.forEach((menu) => {
        // Skip jika tidak ada ID
        if (!menu.id) {
            console.warn('[Role Index] Menu tanpa ID ditemukan, skip...');
            return;
        }

        const id = "#" + menu.id;

        try {
            new Popper(id, ".popper-ref", ".popper-root", dropdownConfig);
        } catch (error) {
            console.error('[Role Index] Error setup popper untuk', id, error);
        }
    });

    console.log('[Role Index] Setup popper untuk', menus.length, 'action menu');
}

// =============================================
// INITIALIZE
// =============================================

if (window.addEventListener) {
    window.addEventListener("app:mounted", initRoleIndex, { once: true });

    // Fallback
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRoleIndex);
    } else {
        initRoleIndex();
    }
}