import './bootstrap';
import './alpine.js';

import '../lineone/js/app.js'
import '../lineone/js/libs/components.js'

import { initProfileDropdown, initAllModals} from './components';
const onLoad = () => {
    initProfileDropdown();
    initAllModals();
}
window.addEventListener("app:mounted", onLoad, { once: true });
