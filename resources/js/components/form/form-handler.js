/**
 * Form Handler - Main Entry Point
 * File: resources/js/components/form-handler.js
 *
 * Import semua form handlers dan inisialisasi
 */

import { initTomSelect } from './select-handler';
import { initFilePreview } from './file-preview';
import { initFilePond } from './filepond-handler';
import { initQuillEditor } from './quill-handler';
import { initFlatpickr } from './flatpickr-handler';
import { initPasswordToggle } from "./password-toggle.js";

/**
 * Initialize semua form components
 */
export function initFormComponents() {
    // Initialize semua handlers
    initTomSelect();
    initFilePreview();
    initFilePond();
    initQuillEditor();
    initFlatpickr();
    initPasswordToggle();
}

/**
 * Auto-initialize saat DOM ready
 */
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFormComponents);
    } else {
        initFormComponents();
    }
}

/**
 * Re-initialize untuk dynamic content (modal, ajax, etc)
 */
export function reinitFormComponents(container = document) {
    initTomSelect(container);
    initFilePreview(container);
    initFilePond(container);
    initQuillEditor(container);
    initFlatpickr(container);
    initPasswordToggle(container)
}

// Export untuk global access jika diperlukan
window.FormHandler = {
    init: initFormComponents,
    reinit: reinitFormComponents,
};