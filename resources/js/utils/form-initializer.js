/**
 * form-initializer.js
 * Initialize semua form components (Tom Select, Quill, Filepond, Flatpickr)
 */

export function initFormComponents() {
    console.log('[Form Init] Starting initialization...');

    initTomSelect();
    initQuillEditors();
    initFilepond();
    initFlatpickr();

    console.log('[Form Init] ✓ All components initialized');
}

/**
 * Initialize Tom Select (untuk select, tags, dll)
 */
function initTomSelect() {
    // ✅ Cek apakah Tom Select library sudah tersedia
    if (typeof Tom === 'undefined') {
        console.warn('[Tom Select] Library not loaded yet, skipping...');
        return;
    }

    // 1. Regular Select dengan Tom Select
    document.querySelectorAll('select[data-tom-select="true"]').forEach(select => {
        // ✅ Skip jika sudah di-init
        if (select.tomselect) {
            console.log('[Tom Select] Already initialized:', select.name);
            return;
        }

        const searchable = select.dataset.tomSearchable !== 'false'; // Default true
        const creatable = select.dataset.tomCreatable === 'true';

        const config = {
            create: creatable,
            sortField: searchable ? { field: 'text', direction: 'asc' } : null,
            plugins: select.hasAttribute('multiple') ? ['remove_button'] : [],
            allowEmptyOption: true,
            onInitialize: function() {
                console.log('[Tom Select] ✓ Initialized:', select.name);
            }
        };

        try {
            new Tom(select, config);
        } catch (error) {
            console.error('[Tom Select] Error initializing:', select.name, error);
        }
    });

    // 2. Tags Input
    document.querySelectorAll('input[data-tom-tags="true"]').forEach(input => {
        if (input.tomselect) return;

        const suggestions = input.dataset.tomSuggestions
            ? JSON.parse(input.dataset.tomSuggestions)
            : [];

        const config = {
            create: true,
            createOnBlur: true,
            delimiter: ',',
            persist: false,
            options: suggestions.map(item => ({ value: item, text: item })),
        };

        try {
            new Tom(input, config);
            console.log('[Tom Tags] ✓ Initialized:', input.name);
        } catch (error) {
            console.error('[Tom Tags] Error:', input.name, error);
        }
    });
}

/**
 * Initialize Quill Rich Text Editor
 */
function initQuillEditors() {
    if (typeof Quill === 'undefined') {
        console.warn('[Quill] Library not loaded, skipping...');
        return;
    }

    document.querySelectorAll('[data-quill-editor]').forEach(editor => {
        if (editor._quill) return;

        const fieldName = editor.dataset.quillEditor;
        const toolbar = editor.dataset.quillToolbar || 'full';
        const textarea = document.getElementById(fieldName);

        if (!textarea) {
            console.warn('[Quill] Textarea not found for:', fieldName);
            return;
        }

        const toolbarConfigs = {
            full: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ],
            basic: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ],
            minimal: [
                ['bold', 'italic'],
                ['link']
            ]
        };

        const config = {
            modules: {
                toolbar: toolbarConfigs[toolbar] || toolbarConfigs.full,
            },
            placeholder: textarea.placeholder || 'Tulis konten di sini...',
            theme: 'snow',
        };

        try {
            editor._quill = new Quill(editor, config);

            if (textarea.value) {
                editor._quill.root.innerHTML = textarea.value;
            }

            editor._quill.on('text-change', () => {
                textarea.value = editor._quill.root.innerHTML;
            });

            console.log('[Quill] ✓ Initialized:', fieldName);
        } catch (error) {
            console.error('[Quill] Error:', fieldName, error);
        }
    });
}

/**
 * Initialize Filepond
 */
function initFilepond() {
    if (typeof FilePond === 'undefined') {
        console.warn('[Filepond] Library not loaded, skipping...');
        return;
    }

    document.querySelectorAll('input[data-filepond="true"]').forEach(input => {
        if (input._filepond) return;

        const maxFileSize = input.dataset.filepondMaxFileSize || '5MB';
        const maxFiles = input.dataset.filepondMaxFiles || null;
        const imagePreview = input.dataset.filepondImagePreview !== 'false';

        const config = {
            maxFileSize: maxFileSize,
            maxFiles: maxFiles ? parseInt(maxFiles) : null,
            allowMultiple: input.hasAttribute('multiple'),
            instantUpload: false,
            allowImagePreview: imagePreview,
            labelIdle: 'Drag & Drop file atau <span class="filepond--label-action">Browse</span>',
        };

        try {
            input._filepond = FilePond.create(input, config);
            console.log('[Filepond] ✓ Initialized:', input.name);
        } catch (error) {
            console.error('[Filepond] Error:', input.name, error);
        }
    });
}

/**
 * Initialize Flatpickr (Date/Time Picker)
 */
function initFlatpickr() {
    if (typeof flatpickr === 'undefined') {
        console.warn('[Flatpickr] Library not loaded, skipping...');
        return;
    }

    document.querySelectorAll('input[data-flatpickr="true"]').forEach(input => {
        if (input._flatpickr) return;

        const mode = input.dataset.flatpickrMode || 'single';
        const enableTime = input.dataset.flatpickrEnableTime === 'true';
        const dateFormat = input.dataset.flatpickrDateFormat || 'Y-m-d';

        const config = {
            mode: mode,
            enableTime: enableTime,
            dateFormat: enableTime ? `${dateFormat} H:i` : dateFormat,
            time_24hr: true,
        };

        try {
            input._flatpickr = flatpickr(input, config);
            console.log('[Flatpickr] ✓ Initialized:', input.name);
        } catch (error) {
            console.error('[Flatpickr] Error:', input.name, error);
        }
    });
}

// ✅ PENTING: Jalankan setelah library loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        // Tunggu sebentar untuk memastikan library loaded
        setTimeout(initFormComponents, 100);
    });
} else {
    setTimeout(initFormComponents, 100);
}

export default {
    init: initFormComponents,
    initTomSelect,
    initQuillEditors,
    initFilepond,
    initFlatpickr,
};