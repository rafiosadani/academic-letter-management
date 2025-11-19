import './bootstrap';
import './alpine.js';

import '../lineone/js/app.js'
import '../lineone/js/libs/components.js'

import {initProfileDropdown} from './components';

document.addEventListener('DOMContentLoaded', () => {
    initProfileDropdown();
});
