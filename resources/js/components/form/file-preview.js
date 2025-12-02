/**
 * File: resources/js/components/form/file-preview.js
 * FINAL VERSION CLEANED: Implementasi penuh fitur, logika input.click(), dan tanpa console.log.
 */

const fileDataStore = new Map();

// --- UTILITY FUNCTIONS ---

function generateFileKey(input) {
    return `${input.name}-${input.id || Math.random()}`;
}

function hidePreview(container) {
    if (container) {
        container.innerHTML = '';
        container.classList.add('hidden');
    }
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

    if (buttonTextSpan && btnLabel && iconElement) {
        iconElement.classList.remove(defaultIconName, selectedIconName);

        if (input.files.length > 0) {
            const fileCount = input.files.length;

            buttonTextSpan.textContent = (fileCount > 1) ? `Change ${fileCount} Files` : changeText;

            btnLabel.title = (fileCount > 1)
                ? `Change ${fileCount} files`
                : `Change file: ${input.files[0].name}`;
            iconElement.classList.add(selectedIconName);

        } else {
            buttonTextSpan.textContent = defaultText;
            btnLabel.title = '';
            iconElement.classList.add(defaultIconName);
        }
    }
}

/**
 * Create image preview element
 */
function createImagePreview(src, filename, fileIdentifier) {
    const wrapper = document.createElement('div');
    wrapper.className = 'relative group inline-block';

    wrapper.innerHTML = `
        <img
            src="${src}"
            alt="${filename}"
            class="h-32 w-32 rounded-lg object-cover border-2 border-slate-200 dark:border-navy-500 shadow-sm"
            title="${filename}"
        />
        <button
            type="button"
            // Mencegah penyebaran event ke elemen di bawahnya
            onclick="event.preventDefault(); event.stopPropagation(); window.FilePreviewHandler.removePreview(this)"
            class="absolute -top-2 -right-2 size-7 rounded-full bg-error text-white shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center text-sm hover:bg-error-focus"
            title="Hapus preview"
            data-file-id="${fileIdentifier}"
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

    fileNotification.className = 'flex items-center space-x-2 p-2 bg-slate-100 dark:bg-navy-700 rounded-lg shadow-sm w-fit max-w-full transition-colors duration-200 hover:bg-slate-200 dark:hover:bg-navy-600 cursor-pointer';
    fileNotification.href = tempUrl;
    fileNotification.target = "_blank";
    fileNotification.rel = "noopener noreferrer";
    fileNotification.title = "Klik untuk Preview File di Tab Baru";

    fileNotification.innerHTML = `
        <i class="fa-solid fa-file-pdf text-lg text-error"></i>
        <span class="text-sm text-slate-700 dark:text-navy-100 truncate flex-grow">${file.name}</span>
        
        <button
            type="button"
            // Mencegah event default (link) dan propagation (input file)
            onclick="event.preventDefault(); event.stopPropagation(); window.FilePreviewHandler.removePreview(this);"
            class="text-error hover:text-error-focus ml-auto p-1"
            title="Hapus file"
            data-file-id="${fileIdentifier}"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    return fileNotification;
}


// --- CORE HANDLERS ---

/**
 * Handle file change event (Preview Gambar dan Notifikasi Nama File)
 */
function handleFileChange(event) {
    const input = event.target;
    const previewId = input.dataset.previewTarget;
    const files = input.files;

    if (input.dataset.fileButton) {
        updateFileButtonText(input);
    }

    if (!previewId || !files || files.length === 0) {
        const previewContainer = document.getElementById(previewId);
        hidePreview(previewContainer);
        return;
    }

    const previewContainer = document.getElementById(previewId);
    previewContainer.classList.remove('hidden');
    previewContainer.innerHTML = '';

    const fileKey = generateFileKey(input);
    const currentFiles = Array.from(files);
    const storedData = fileDataStore.get(fileKey) || { previewed: new Set() };

    if (!input.multiple) {
        storedData.previewed.clear();
    }

    const isImageFile = files[0].type.match('image.*');

    if (isImageFile) {
        // --- KASUS A: Gambar (Image Preview) ---
        let previewGrid = document.createElement('div');
        previewGrid.className = 'flex flex-wrap justify-center gap-3';
        previewContainer.appendChild(previewGrid);

        currentFiles.forEach((file) => {
            const fileIdentifier = `${file.name}-${file.size}-${file.lastModified}`;
            if (storedData.previewed.has(fileIdentifier)) return;
            storedData.previewed.add(fileIdentifier);

            const reader = new FileReader();
            reader.onload = function(e) {
                const imageWrapper = createImagePreview(e.target.result, file.name, fileIdentifier);
                previewGrid.appendChild(imageWrapper);
            };
            reader.readAsDataURL(file);
        });

    } else {
        // --- KASUS B: FILE BUKAN GAMBAR (Notifikasi Link Preview) ---
        const file = currentFiles[0];
        const fileIdentifier = `${file.name}-${file.size}-${file.lastModified}`;

        storedData.previewed.clear();
        storedData.previewed.add(fileIdentifier);

        const fileNotification = createFileNotification(file, fileIdentifier);
        previewContainer.appendChild(fileNotification);
    }

    fileDataStore.set(fileKey, storedData);
}

/**
 * Remove preview, hapus identitas file dari store, dan picu input click.
 */
export function removePreview(button) {
    const wrapper = button.closest('.relative') || button.closest('a.flex.items-center');
    if (!wrapper) return;

    const fileIdentifier = button.dataset.fileId;
    const previewContainer = button.closest('[id$="_preview"]');

    const imageGrid = wrapper.closest('.flex.flex-wrap.justify-center');

    const input = document.querySelector(`input[data-preview-target="${previewContainer.id}"]`);

    if (input && fileIdentifier) {
        const fileKey = generateFileKey(input);
        const storedData = fileDataStore.get(fileKey);
        if (storedData && storedData.previewed.has(fileIdentifier)) {
            storedData.previewed.delete(fileIdentifier);
            fileDataStore.set(fileKey, storedData);
        }
    }

    wrapper.style.opacity = '0';
    wrapper.style.transform = 'scale(0.8)';
    wrapper.style.transition = 'all 0.2s ease-out';

    setTimeout(() => {
        wrapper.remove();

        // Cek apakah grid gambar kosong, lalu hapus gridnya
        if (imageGrid && imageGrid.children.length === 0) {
            imageGrid.remove();
        }

        // Cek apakah previewContainer kosong total
        if (previewContainer && previewContainer.children.length === 0) {
            hidePreview(previewContainer);
        }

        if (input) {
            // 1. Kosongkan value input
            input.value = '';

            // 2. Panggil updateFileButtonText untuk mereset teks tombol
            updateFileButtonText(input);

            // 3. Pemicu input file
            input.click();

            // Timeout untuk mengatasi bug 'Cancel'
            setTimeout(() => {
                if (input.files.length === 0) {
                    updateFileButtonText(input);
                }
            }, 100);
        }

    }, 200);
}


/**
 * Initialize file preview listeners
 */
export function initFilePreview(container = document) {
    const fileInputs = container.querySelectorAll('input[type="file"][data-file-button="true"]');

    if (fileInputs.length === 0) {
        return;
    }

    fileInputs.forEach(input => {
        input.removeEventListener('change', handleFileChange);
        input.addEventListener('change', handleFileChange);

        if (input.dataset.fileButton) {
            updateFileButtonText(input);
        }
    });
}


// Export untuk global access
window.FilePreviewHandler = {
    init: initFilePreview,
    removePreview: removePreview,
};