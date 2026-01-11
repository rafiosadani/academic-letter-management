/**
 * Mobile Sidebar Controller
 * Auto-close dan overlay handler untuk mobile devices
 *
 * @module mobileSidebar
 */

const SIDEBAR_OPEN_CLASS = 'is-sidebar-open';

/**
 * Check if current viewport is mobile
 * @returns {boolean}
 */
const isMobile = () => window.innerWidth < 1280;

/**
 * Close sidebar (menggunakan sistem yang sudah ada)
 */
function closeSidebar() {
    if (!isMobile()) return;

    const body = document.body;
    const wasOpen = body.classList.contains(SIDEBAR_OPEN_CLASS);

    if (wasOpen) {
        body.classList.remove(SIDEBAR_OPEN_CLASS);

        // Save state ke localStorage
        setTimeout(() => {
            localStorage.setItem('app-panel-open', 'false');
        }, 50);
    }
}

/**
 * Setup auto-close setelah klik menu
 */
function setupAutoClose() {
    // Get all navigation links
    const mainLinks = document.querySelectorAll('.main-sidebar a[href]');
    const panelLinks = document.querySelectorAll('.sidebar-panel a[href][data-nav-link="panel"]');

    /**
     * Handle link click event
     * @param {Event} e - Click event
     */
    function handleLinkClick(e) {
        const href = this.getAttribute('href');

        // Only auto-close untuk navigasi valid
        if (href && href !== '#' && !href.startsWith('javascript:')) {
            if (isMobile()) {
                // Delay untuk visual feedback
                setTimeout(() => {
                    closeSidebar();
                }, 150);
            }
        }
    }

    // Attach event listeners
    mainLinks.forEach(link => link.addEventListener('click', handleLinkClick));
    panelLinks.forEach(link => link.addEventListener('click', handleLinkClick));
}

/**
 * Setup overlay click handler
 */
function setupOverlay() {
    const overlay = document.querySelector('.sidebar-overlay');

    if (overlay) {
        overlay.addEventListener('click', function() {
            if (isMobile()) {
                closeSidebar();
            }
        });
    }
}

/**
 * Setup ESC key handler
 */
function setupEscKey() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile()) {
            const body = document.body;
            if (body.classList.contains(SIDEBAR_OPEN_CLASS)) {
                closeSidebar();
            }
        }
    });
}

/**
 * Setup window resize handler
 */
function setupResize() {
    let resizeTimer;

    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // Auto-close saat resize ke mobile
            if (isMobile()) {
                closeSidebar();
            }
        }, 250);
    });
}

/**
 * Di mobile, jangan save state "open" ke localStorage
 * Agar sidebar selalu mulai dari hidden
 */
function preventMobilePersistence() {
    if (isMobile()) {
        // Override toggle button behavior di mobile
        const toggleBtn = document.querySelector('.sidebar-toggle');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                // Jangan save state di mobile, biarkan sidebar auto-close
                if (isMobile()) {
                    setTimeout(() => {
                        localStorage.setItem('app-panel-open', 'false');
                    }, 300);
                }
            });
        }
    }
}

/**
 * Initialize mobile sidebar controller
 * Call this function on app mount
 */
export function initMobileSidebar() {
    // Di mobile, force close sidebar saat load
    if (isMobile()) {
        const body = document.body;
        body.classList.remove(SIDEBAR_OPEN_CLASS);
        localStorage.setItem('app-panel-open', 'false');
    }

    // Setup semua handler
    setupAutoClose();
    setupOverlay();
    setupEscKey();
    setupResize();
    preventMobilePersistence();
}