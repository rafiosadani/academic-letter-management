import './bootstrap';
import './alpine.js';

import '../lineone/js/app.js';
import '../lineone/js/libs/components.js';

import '../lineone/js/libs/forms.js';
// import '../lineone/js/pages/forms-upload.js';

import './notifications/notification-polling.js';

import './pages/roles/role-form.js';

import '../lineone/js/pages/pages-error-404-3.js';

import {
    initProfileDropdown,
    initAllModals,
    initSessionHandlers,
    initRealtimeClock,
    initSidebarState,
    initUniversalPopperFix,
    initModalErrorReopener,
    initModalFormReset,
    initFormComponents,
    initStatusToggle,
    initDocumentUpload,
    initMobileSidebar
} from './components';

const onLoad = () => {
    initProfileDropdown();
    initAllModals();
    initSessionHandlers();
    initRealtimeClock();
    initSidebarState();
    initUniversalPopperFix();
    initModalErrorReopener();
    initModalFormReset();
    initFormComponents();
    initStatusToggle();
    initDocumentUpload();
    initMobileSidebar();
}
window.addEventListener("app:mounted", onLoad, { once: true });