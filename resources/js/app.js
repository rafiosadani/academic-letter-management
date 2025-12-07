import './bootstrap';
import './alpine.js';

import '../lineone/js/app.js';
import '../lineone/js/libs/components.js';

import './notifications/notification-polling.js';

import { initProfileDropdown, initAllModals, initSessionHandlers, initRealtimeClock, initSidebarState, initUniversalPopperFix, initModalErrorReopener, initModalFormReset, initFormComponents, initStatusToggle } from './components';

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
}
window.addEventListener("app:mounted", onLoad, { once: true });
