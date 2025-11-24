import './bootstrap';
import './alpine.js';

import '../lineone/js/app.js'
import '../lineone/js/libs/components.js'

import { initProfileDropdown, initAllModals, initSessionHandlers } from './components';
const onLoad = () => {
    initProfileDropdown();
    initAllModals();
    initSessionHandlers();
}
window.addEventListener("app:mounted", onLoad, { once: true });
