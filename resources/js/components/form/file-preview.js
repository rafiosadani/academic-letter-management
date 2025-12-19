// /**
//  * File: resources/js/components/form/file-preview.js
//  * FINAL VERSION CLEANED: Implementasi penuh fitur, logika input.click(), dan tanpa console.log.
//  */
//
// const fileDataStore = new Map();
//
// function generateFileKey(input) {
//     return `${input.name}-${input.id}`;
// }
//
// function hidePreview(container) {
//     if (!container) return;
//     container.innerHTML = '';
//     container.classList.add('hidden');
// }
//
// /**
//  * Memperbarui teks dan ikon pada tombol file.
//  */
// function updateFileButtonText(input) {
//     const buttonTextSpan = document.getElementById(input.id + '_btn_text');
//     const btnLabel = document.getElementById(input.id + '_btn_label');
//     const iconElement = document.getElementById(input.id + '_btn_icon');
//
//     const defaultIconName = 'fa-cloud-arrow-up';
//     const selectedIconName = 'fa-rotate-right';
//     const defaultText = input.dataset.defaultText || 'Choose File';
//     const changeText = input.dataset.changeText || 'Change File';
//
//     if (!buttonTextSpan || !btnLabel || !iconElement) return;
//
//     iconElement.classList.remove(defaultIconName, selectedIconName);
//
//     const fileKey = generateFileKey(input);
//     const storedData = fileDataStore.get(fileKey);
//     const count = storedData ? storedData.previewed.size : 0;
//
//     if (count > 0) {
//         buttonTextSpan.textContent =
//             count > 1 ? `Change ${count} Files` : changeText;
//
//         btnLabel.title = `Change ${count} file`;
//         iconElement.classList.add(selectedIconName);
//     } else {
//         buttonTextSpan.textContent = defaultText;
//         btnLabel.title = '';
//         iconElement.classList.add(defaultIconName);
//     }
// }
//
// /**
//  * Create image preview element
//  */
// function createImagePreview(src, filename, fileIdentifier) {
//     const wrapper = document.createElement('div');
//     wrapper.className = 'relative group inline-block';
//
//     wrapper.innerHTML = `
//         <img
//             src="${src}"
//             alt="${filename}"
//             class="h-32 w-32 rounded-lg object-cover border-2 border-slate-200 dark:border-navy-500 shadow-sm"
//             title="${filename}"
//         />
//         <button
//             type="button"
//             onclick="event.preventDefault(); event.stopPropagation(); window.FilePreviewHandler.removePreview(this)"
//             class="absolute -top-2 -right-2 size-7 rounded-full bg-error text-white shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center text-sm hover:bg-error-focus"
//             title="Hapus preview"
//             data-file-id="${fileIdentifier}"
//         >
//             <i class="fa-solid fa-xmark"></i>
//         </button>
//     `;
//
//     return wrapper;
// }
//
// /**
//  * Buat notifikasi nama file (untuk non-image) yang dapat di-preview.
//  */
// function createFileNotification(file, fileIdentifier) {
//     const tempUrl = URL.createObjectURL(file);
//     const fileNotification = document.createElement('a');
//
//     fileNotification.className =
//         'flex items-center space-x-2 p-2 bg-slate-100 dark:bg-navy-700 rounded-lg shadow-sm w-fit max-w-full transition-colors duration-200 hover:bg-slate-200 dark:hover:bg-navy-600 cursor-pointer';
//
//     fileNotification.href = tempUrl;
//     fileNotification.target = '_blank';
//     fileNotification.rel = 'noopener noreferrer';
//     fileNotification.title = 'Klik untuk Preview File di Tab Baru';
//
//     fileNotification.innerHTML = `
//         <i class="fa-solid fa-file-pdf text-lg text-error"></i>
//         <span class="text-sm text-slate-700 dark:text-navy-100 truncate flex-grow">
//             ${file.name}
//         </span>
//         <button
//             type="button"
//             onclick="event.preventDefault(); event.stopPropagation(); window.FilePreviewHandler.removePreview(this)"
//             class="text-error hover:text-error-focus ml-auto p-1"
//             title="Hapus file"
//             data-file-id="${fileIdentifier}"
//         >
//             <i class="fa-solid fa-xmark"></i>
//         </button>
//     `;
//
//     return fileNotification;
// }
//
// /**
//  * Handle file change event (Preview Gambar dan Notifikasi Nama File)
//  */
// function handleFileChange(event) {
//     const input = event.target;
//     const previewId = input.dataset.previewTarget;
//     if (!previewId) return;
//
//     const previewContainer = document.getElementById(previewId);
//     if (!previewContainer) return;
//
//     const fileKey = generateFileKey(input);
//     const files = Array.from(input.files);
//
//     let storedData = fileDataStore.get(fileKey);
//     if (!storedData) {
//         storedData = { previewed: new Map() };
//         fileDataStore.set(fileKey, storedData);
//     }
//
//     previewContainer.classList.remove('hidden');
//
//     const isImage = files.length && files[0].type.startsWith('image/');
//
//     const isCentered = previewContainer.classList.contains('justify-center');
//     const alignClass = isCentered
//         ? 'justify-center items-center'
//         : 'justify-start';
//
//     let previewWrapper = previewContainer.querySelector('.preview-wrapper');
//
//     if (!previewWrapper) {
//         previewWrapper = document.createElement('div');
//         previewWrapper.className = `preview-wrapper ${
//             isImage
//                 ? `flex flex-wrap gap-3 ${alignClass}`
//                 : `flex flex-col gap-2 w-full ${alignClass}`
//         }`;
//         previewContainer.appendChild(previewWrapper);
//     }
//
//     files.forEach(file => {
//         const fileIdentifier = `${file.name}-${file.size}-${file.lastModified}`;
//
//         if (storedData.previewed.has(fileIdentifier)) return;
//
//         storedData.previewed.set(fileIdentifier, file);
//
//         if (isImage) {
//             const reader = new FileReader();
//             reader.onload = e => {
//                 previewWrapper.appendChild(
//                     createImagePreview(
//                         e.target.result,
//                         file.name,
//                         fileIdentifier
//                     )
//                 );
//             };
//             reader.readAsDataURL(file);
//         } else {
//             previewWrapper.appendChild(
//                 createFileNotification(file, fileIdentifier)
//             );
//         }
//     });
//
//     updateFileButtonText(input);
//
//     // supaya upload file yg sama tetap ke-trigger
//     input.value = '';
// }
//
// /**
//  * Remove preview, hapus identitas file dari store, dan picu input click.
//  */
// export function removePreview(button) {
//     const item =
//         button.closest('.relative') || button.closest('a');
//     if (!item) return;
//
//     const previewContainer = button.closest('[id$="_preview"]');
//     const input = document.querySelector(
//         `input[data-preview-target="${previewContainer.id}"]`
//     );
//
//     const fileIdentifier = button.dataset.fileId;
//
//     if (input) {
//         const fileKey = generateFileKey(input);
//         const storedData = fileDataStore.get(fileKey);
//         if (storedData) {
//             storedData.previewed.delete(fileIdentifier);
//         }
//     }
//
//     /* ===== ANIMATION ===== */
//     item.style.opacity = '0';
//     item.style.transform = 'scale(0.85)';
//     item.style.transition = 'all 0.2s ease-out';
//
//     setTimeout(() => {
//         item.remove();
//
//         const remaining =
//             previewContainer.querySelectorAll('[data-file-id]').length;
//
//         updateFileButtonText(input);
//
//         if (remaining === 0) {
//             hidePreview(previewContainer);
//             if (input) {
//                 input.click(); // auto open input
//             }
//         }
//     }, 200);
// }
//
// /**
//  * Initialize file preview listeners
//  */
// export function initFilePreview(container = document) {
//     const fileInputs = container.querySelectorAll(
//         'input[type="file"][data-file-button="true"]'
//     );
//
//     fileInputs.forEach(input => {
//         input.removeEventListener('change', handleFileChange);
//         input.addEventListener('change', handleFileChange);
//         updateFileButtonText(input);
//     });
// }
//
// // Export untuk global access
// window.FilePreviewHandler = {
//     init: initFilePreview,
//     removePreview: removePreview,
// };
//

/**
 * File: resources/js/components/form/file-preview.js
 * FULL VERSION
 * - Support preview variant (fit / fixed + aspect ratio)
 * - Animasi remove aktif
 * - Auto open input jika file terakhir dihapus
 * - Tidak mengubah nama variabel / const lama
 */

const fileDataStore = new Map();

/* =========================
   UTILITIES
========================= */

function generateFileKey(input) {
    return `${input.name}-${input.id}`;
}

function hidePreview(container) {
    if (!container) return;
    container.innerHTML = '';
    container.classList.add('hidden');
}

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

/* =========================
   IMAGE PREVIEW
========================= */

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

/* =========================
   NON IMAGE PREVIEW
========================= */

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

/* =========================
   HANDLE CHANGE
========================= */

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

/* =========================
   REMOVE PREVIEW (ANIMATED)
========================= */

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

/* =========================
   INIT
========================= */

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

window.FilePreviewHandler = {
    init: initFilePreview,
    removePreview: removePreview,
};
