import './bootstrap';
import './alpine.js';

import '../lineone/js/app.js'
import '../lineone/js/libs/components.js'

import { initProfileDropdown, initAllModals, initSessionHandlers, initRealtimeClock, initSidebarState, initUniversalPopperFix, initModalErrorReopener, initModalFormReset } from './components';
const onLoad = () => {
    initProfileDropdown();
    initAllModals();
    initSessionHandlers();
    initRealtimeClock();
    initSidebarState();
    initUniversalPopperFix();
    initModalErrorReopener();
    initModalFormReset();
}
window.addEventListener("app:mounted", onLoad, { once: true });
