/**
 * FilePond Handler
 * File: resources/js/components/filepond-handler.js
 */

import * as FilePond from 'filepond';
import 'filepond/dist/filepond.min.css';

// Import plugins
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
// import 'filepond-plugin-image-crop/dist/filepond-plugin-image-crop.css';

// Register plugins
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageResize,
    FilePondPluginImageTransform,
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType,
    FilePondPluginImageCrop
);

// Store instances
const filePondInstances = new Map();

/**
 * Initialize FilePond
 */
export function initFilePond(container = document) {
    const fileInputs = container.querySelectorAll('input[type="file"][data-filepond="true"]');

    if (fileInputs.length === 0) {
        return;
    }

    fileInputs.forEach(input => {
        // Skip if already initialized
        if (filePondInstances.has(input)) {
            return;
        }

        try {
            const instance = createFilePondInstance(input);
            filePondInstances.set(input, instance);
        } catch (error) {
            console.error('[FilePond] ✗ Failed to initialize:', input.name, error);
        }
    });
}

/**
 * Create FilePond instance dengan config dari data attributes
 */
function createFilePondInstance(input) {
    const config = {
        // Basic settings
        name: input.name,
        allowMultiple: input.hasAttribute('multiple'),
        required: input.hasAttribute('required'),

        // Labels (Bahasa Indonesia)
        labelIdle: 'Drag & Drop file atau <span class="filepond--label-action">Browse</span>',
        labelFileLoading: 'Memuat...',
        labelFileProcessing: 'Mengupload...',
        labelFileProcessingComplete: 'Upload selesai',
        labelFileProcessingAborted: 'Upload dibatalkan',
        labelTapToCancel: 'tap untuk batal',
        labelTapToRetry: 'tap untuk retry',
        labelTapToUndo: 'tap untuk undo',
        labelButtonRemoveItem: 'Hapus',
        labelButtonAbortItemLoad: 'Batal',
        labelButtonRetryItemLoad: 'Retry',
        labelButtonAbortItemProcessing: 'Batal',
        labelButtonUndoItemProcessing: 'Undo',
        labelButtonRetryItemProcessing: 'Retry',
        labelButtonProcessItem: 'Upload',

        // File validation
        acceptedFileTypes: getAcceptedTypes(input.accept),
        maxFileSize: parseFileSize(input.dataset.filepondMaxFileSize || '5MB'),
        maxFiles: input.dataset.filepondMaxFiles ? parseInt(input.dataset.filepondMaxFiles) : null,

        // Image settings
        allowImagePreview: input.dataset.filepondImagePreview !== 'false',
        imagePreviewHeight: 170,
        imageCropAspectRatio: '1:1',
        imageResizeTargetWidth: parseInt(input.dataset.filepondResizeWidth || 800),
        imageResizeTargetHeight: parseInt(input.dataset.filepondResizeHeight || 600),
        imageResizeMode: 'contain',
        imageResizeUpscale: false,

        // Styling
        stylePanelLayout: 'compact circle',
        styleLoadIndicatorPosition: 'center bottom',
        styleProgressIndicatorPosition: 'right bottom',
        styleButtonRemoveItemPosition: 'right bottom',
        styleButtonProcessItemPosition: 'right bottom',

        // Server settings (jika instant upload)
        instantUpload: input.dataset.filepondInstantUpload === 'true',

        // Callbacks
        onaddfile: (error, file) => {
            if (error) {
                console.error('[FilePond] Error adding file:', error);
                return;
            }
            console.log('[FilePond] File added:', file.filename);
        },

        onremovefile: (error, file) => {
            console.log('[FilePond] File removed:', file.filename);
        },

        onprocessfile: (error, file) => {
            if (error) {
                console.error('[FilePond] Error processing file:', error);
                return;
            }
        },
    };

    // Enable crop jika diminta
    if (input.dataset.filepondAllowCrop === 'true') {
        config.allowImageCrop = true;
        config.imageCropAspectRatio = '1:1';
    }

    // Enable resize jika diminta
    if (input.dataset.filepondImageResize === 'true') {
        config.allowImageResize = true;
        config.imageResizeMode = 'contain';
    }

    return FilePond.create(input, config);
}

/**
 * Parse file size string to bytes
 */
function parseFileSize(sizeStr) {
    const units = {
        'B': 1,
        'KB': 1024,
        'MB': 1024 * 1024,
        'GB': 1024 * 1024 * 1024,
    };

    const match = sizeStr.match(/^(\d+(?:\.\d+)?)\s*([KMGT]?B)$/i);
    if (!match) return 5 * 1024 * 1024; // Default 5MB

    const value = parseFloat(match[1]);
    const unit = match[2].toUpperCase();

    return value * (units[unit] || 1);
}

/**
 * Get accepted file types array
 */
function getAcceptedTypes(acceptAttr) {
    if (!acceptAttr) return null;

    // Convert MIME types or extensions
    return acceptAttr.split(',').map(type => type.trim());
}

/**
 * Destroy FilePond instance
 */
export function destroyFilePond(input) {
    const instance = filePondInstances.get(input);
    if (instance) {
        instance.destroy();
        filePondInstances.delete(input);
        console.log('[FilePond] Destroyed:', input.name);
    }
}

/**
 * Destroy all FilePond instances
 */
export function destroyAllFilePond() {
    filePondInstances.forEach((instance) => {
        instance.destroy();
    });
    filePondInstances.clear();
}

/**
 * Get FilePond instance
 */
export function getFilePondInstance(input) {
    return filePondInstances.get(input);
}

// Export untuk global access
window.FilePondHandler = {
    init: initFilePond,
    destroy: destroyFilePond,
    destroyAll: destroyAllFilePond,
    getInstance: getFilePondInstance,
};