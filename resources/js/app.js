import './bootstrap';
import './alpine.js';

import '../lineone/js/app.js';
import '../lineone/js/libs/components.js';

import '../lineone/js/libs/forms.js';
import '../lineone/js/pages/forms-upload.js';

import './notifications/notification-polling.js';

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
    initDocumentUpload
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
}
window.addEventListener("app:mounted", onLoad, { once: true });