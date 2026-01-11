/**
 * File: resources/js/components/form/file-preview.js
 * FINAL VERSION CLEANED: Implementasi penuh fitur, logika input.click(), dan tanpa console.log.
 */

const fileDataStore = new Map();

// Utilities

function generateFileKey(input) {
    return `${input.name}-${input.id}`;
}

function hidePreview(container) {
    if (!container) return;
    container.innerHTML = '';
    container.classList.add('hidden');
}

/**
 * Memperbarui teks dan ikon pada tombol file.
 */
function updateFileButtonText(input) {
    const buttonTextSpan = document.getElementById(input.id + '_btn_text');
    const btnLabel = document.getElementById(input.id + '_btn_label');
    const iconElement = document.getElementById(input.id + '_btn_icon');

    const defaultIconName = 'fa-cloud-arrow-up';
    const selectedIconName = 'fa-rotate-right';
    const defaultText = input.dataset.defaultText || 'Choose File';
    const changeText = input.dataset.changeText || 'Change File';

    if (!buttonTextSpan || !btnLabel || !iconElement) return;

    iconElement.classList.remove(defaultIconName, selectedIconName);

    if (input.files.length > 0) {
        const fileCount = input.files.length;

        buttonTextSpan.textContent =
            fileCount > 1 ? `Change ${fileCount} Files` : changeText;

        iconElement.classList.add(selectedIconName);
    } else {
        buttonTextSpan.textContent = defaultText;
        iconElement.classList.add(defaultIconName);
    }
}

/**
 * Create image preview element
 */
function createImagePreview(src, filename, fileIdentifier, input) {
    const wrapper = document.createElement('div');
    wrapper.className = 'relative group transition-all duration-200';

    const mode = input.dataset.previewMode || 'fit';
    const width = input.dataset.previewWidth;
    const maxWidth = input.dataset.previewMaxWidth;
    const aspect = input.dataset.previewAspect;

    let containerStyle = '';
    let imgClass =
        'rounded-lg object-cover border-2 border-slate-200 dark:border-navy-500 shadow-sm';

    if (mode === 'fixed' && width) {
        containerStyle += `width:${width}px;`;
        if (aspect) containerStyle += `aspect-ratio:${aspect};`;
        imgClass += ' w-full h-full';
    } else {
        if (maxWidth) containerStyle += `max-width:${maxWidth}px;`;
        imgClass += ' w-fit max-h-64';
    }

    wrapper.innerHTML = `
        <div style="${containerStyle}">
            <img
                src="${src}"
                alt="${filename}"
                title="${filename}"
                class="${imgClass}"
            />
        </div>

        <button
            type="button"
            data-file-id="${fileIdentifier}"
            onclick="event.preventDefault(); event.stopPropagation(); window.FilePreviewHandler.removePreview(this)"
            class="absolute -top-2 -right-2 size-7 rounded-full bg-error text-white shadow-lg
                   opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-error-focus"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    return wrapper;
}

/**
 * Buat notifikasi nama file (untuk non-image) yang dapat di-preview.
 */
function createFileNotification(file, fileIdentifier) {
    const tempUrl = URL.createObjectURL(file);
    const fileNotification = document.createElement('a');

    fileNotification.className =
        'flex items-center gap-2 p-2 bg-slate-100 dark:bg-navy-700 rounded-lg shadow-sm ' +
        'hover:bg-slate-200 dark:hover:bg-navy-600 transition-colors duration-200';

    fileNotification.href = tempUrl;
    fileNotification.target = '_blank';
    fileNotification.rel = 'noopener noreferrer';

    fileNotification.innerHTML = `
        <i class="fa-solid fa-file-pdf text-error"></i>
        <span class="text-sm truncate flex-1">${file.name}</span>
        <button
            type="button"
            data-file-id="${fileIdentifier}"
            onclick="event.preventDefault(); event.stopPropagation(); window.FilePreviewHandler.removePreview(this)"
            class="text-error hover:text-error-focus"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    return fileNotification;
}

/**
 * Handle file change event (Preview Gambar dan Notifikasi Nama File)
 */
function handleFileChange(event) {
    const input = event.target;
    const previewId = input.dataset.previewTarget;
    const files = input.files;

    updateFileButtonText(input);

    if (!previewId || !files || files.length === 0) {
        hidePreview(document.getElementById(previewId));
        return;
    }

    const previewContainer = document.getElementById(previewId);
    previewContainer.classList.remove('hidden');
    previewContainer.innerHTML = '';

    const fileKey = generateFileKey(input);
    const storedData = fileDataStore.get(fileKey) || { previewed: new Set() };

    if (!input.multiple) {
        storedData.previewed.clear();
    }

    const isImageFile = files[0].type.startsWith('image/');
    const isCentered = previewContainer.classList.contains('justify-center');
    const alignmentClass = isCentered ? 'justify-center' : 'justify-start';

    if (isImageFile) {
        const previewGrid = document.createElement('div');
        previewGrid.className = `flex flex-wrap ${alignmentClass} gap-3`;
        previewContainer.appendChild(previewGrid);

        Array.from(files).forEach(file => {
            const fileIdentifier = `${file.name}-${file.size}-${file.lastModified}`;
            if (storedData.previewed.has(fileIdentifier)) return;

            storedData.previewed.add(fileIdentifier);

            const reader = new FileReader();
            reader.onload = e => {
                const imageWrapper = createImagePreview(
                    e.target.result,
                    file.name,
                    fileIdentifier,
                    input
                );
                previewGrid.appendChild(imageWrapper);
            };
            reader.readAsDataURL(file);
        });
    } else {
        const fileList = document.createElement('div');
        fileList.className = `flex flex-col ${alignmentClass} gap-2`;
        previewContainer.appendChild(fileList);

        Array.from(files).forEach(file => {
            const fileIdentifier = `${file.name}-${file.size}-${file.lastModified}`;
            if (storedData.previewed.has(fileIdentifier)) return;

            storedData.previewed.add(fileIdentifier);
            fileList.appendChild(createFileNotification(file, fileIdentifier));
        });
    }

    fileDataStore.set(fileKey, storedData);
}

/**
 * Remove preview, hapus identitas file dari store, dan picu input click.
 */
export function removePreview(button) {
    const wrapper =
        button.closest('.relative') || button.closest('a');
    if (!wrapper) return;

    const previewContainer = button.closest('[id$="_preview"]');
    const input = document.querySelector(
        `input[data-preview-target="${previewContainer.id}"]`
    );

    const fileIdentifier = button.dataset.fileId;

    if (input) {
        const fileKey = generateFileKey(input);
        const storedData = fileDataStore.get(fileKey);
        if (storedData) {
            storedData.previewed.delete(fileIdentifier);
        }
    }

    /* 🔥 ANIMATION */
    wrapper.style.opacity = '0';
    wrapper.style.transform = 'scale(0.85)';
    wrapper.style.transition = 'all 0.2s ease-out';

    setTimeout(() => {
        wrapper.remove();

        const remaining =
            previewContainer.querySelectorAll('[data-file-id]').length;

        updateFileButtonText(input);

        if (remaining === 0) {
            hidePreview(previewContainer);
            if (input) {
                input.value = '';
                input.click(); // auto open input
            }
        }
    }, 200);
}

/**
 * Initialize file preview listeners
 */
export function initFilePreview(container = document) {
    const fileInputs = container.querySelectorAll(
        'input[type="file"][data-file-button="true"]'
    );

    fileInputs.forEach(input => {
        input.removeEventListener('change', handleFileChange);
        input.addEventListener('change', handleFileChange);
        updateFileButtonText(input);
    });
}

// Export untuk global access
window.FilePreviewHandler = {
    init: initFilePreview,
    removePreview: removePreview,
};
