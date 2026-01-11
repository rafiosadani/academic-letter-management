/**
 * Quill Editor Handler
 * File: resources/js/components/quill-handler.js
 */

import Quill from 'quill';
import 'quill/dist/quill.snow.css';

// Store instances
const quillInstances = new Map();

/**
 * Toolbar configurations
 */
const toolbarConfigs = {
    full: [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'font': [] }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'align': [] }],
        ['blockquote', 'code-block'],
        ['link', 'image', 'video'],
        ['clean']
    ],
    basic: [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['link'],
        ['clean']
    ],
    minimal: [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'bullet' }],
        ['link']
    ]
};

/**
 * Initialize Quill Editor
 */
export function initQuillEditor(container = document) {
    console.log('[Quill] Initializing...');

    const editors = container.querySelectorAll('[data-quill-editor]');

    if (editors.length === 0) {
        console.log('[Quill] No editors found');
        return;
    }

    editors.forEach(editorContainer => {
        const editorId = editorContainer.id;
        const fieldName = editorContainer.dataset.quillEditor;
        const toolbarType = editorContainer.dataset.quillToolbar || 'full';

        // Skip if already initialized
        if (quillInstances.has(editorId)) {
            console.log('[Quill] Already initialized:', fieldName);
            return;
        }

        try {
            const instance = createQuillInstance(editorContainer, fieldName, toolbarType);
            quillInstances.set(editorId, instance);
            console.log('[Quill] ✓ Initialized:', fieldName);
        } catch (error) {
            console.error('[Quill] ✗ Failed to initialize:', fieldName, error);
        }
    });

    console.log(`[Quill] ✓ Initialized ${editors.length} editor(s)`);
}

/**
 * Create Quill instance
 */
function createQuillInstance(container, fieldName, toolbarType) {
    const hiddenInput = document.getElementById(fieldName);

    if (!hiddenInput) {
        throw new Error(`Hidden textarea not found: ${fieldName}`);
    }

    // Get initial value
    const initialValue = hiddenInput.value;

    // Create Quill instance
    const quill = new Quill(container, {
        theme: 'snow',
        modules: {
            toolbar: toolbarConfigs[toolbarType] || toolbarConfigs.full,
            clipboard: {
                matchVisual: false
            }
        },
        placeholder: 'Tulis konten di sini...',
        bounds: container,
    });

    // Set initial content jika ada
    if (initialValue) {
        quill.root.innerHTML = initialValue;
    }

    // Sync content ke hidden textarea saat ada perubahan
    quill.on('text-change', () => {
        const html = quill.root.innerHTML;
        hiddenInput.value = html === '<p><br></p>' ? '' : html;

        // Trigger change event untuk validation
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    });

    // Handle form submit
    const form = hiddenInput.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            const html = quill.root.innerHTML;
            hiddenInput.value = html === '<p><br></p>' ? '' : html;
        });
    }

    // Custom styling untuk dark mode
    applyDarkModeStyles(container);

    return quill;
}

/**
 * Apply dark mode styles
 */
function applyDarkModeStyles(container) {
    const isDark = document.documentElement.classList.contains('dark');

    if (isDark) {
        container.classList.add('quill-dark');
    }

    // Listen for dark mode changes
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const isDark = document.documentElement.classList.contains('dark');
                container.classList.toggle('quill-dark', isDark);
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
}

/**
 * Get Quill instance
 */
export function getQuillInstance(editorId) {
    return quillInstances.get(editorId);
}

/**
 * Destroy Quill instance
 */
export function destroyQuillEditor(editorId) {
    const instance = quillInstances.get(editorId);
    if (instance) {
        // Quill doesn't have destroy method, but we can cleanup
        quillInstances.delete(editorId);
        console.log('[Quill] Destroyed:', editorId);
    }
}

/**
 * Destroy all Quill instances
 */
export function destroyAllQuill() {
    quillInstances.clear();
    console.log('[Quill] All instances destroyed');
}

/**
 * Get content dari Quill editor
 */
export function getQuillContent(editorId, format = 'html') {
    const instance = quillInstances.get(editorId);
    if (!instance) return null;

    if (format === 'text') {
        return instance.getText();
    } else if (format === 'delta') {
        return instance.getContents();
    }

    return instance.root.innerHTML;
}

/**
 * Set content ke Quill editor
 */
export function setQuillContent(editorId, content, format = 'html') {
    const instance = quillInstances.get(editorId);
    if (!instance) return;

    if (format === 'text') {
        instance.setText(content);
    } else if (format === 'delta') {
        instance.setContents(content);
    } else {
        instance.root.innerHTML = content;
    }
}

// Export untuk global access
window.QuillHandler = {
    init: initQuillEditor,
    destroy: destroyQuillEditor,
    destroyAll: destroyAllQuill,
    getInstance: getQuillInstance,
    getContent: getQuillContent,
    setContent: setQuillContent,
};