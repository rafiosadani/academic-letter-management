/**
 * user-form.js
 * Initialize form components untuk halaman user create/edit
 */

/**
 * PREVIEW IMAGE FUNCTION
 */
function previewImage(input, previewId) {
    const previewContainer = document.getElementById(previewId);
    const files = input.files;

    if (!previewContainer || files.length === 0) return;

    previewContainer.innerHTML = '';
    previewContainer.classList.remove('hidden');

    Array.from(files).forEach((file, index) => {
        if (!file.type.match('image.*')) return;

        const reader = new FileReader();

        reader.onload = function (e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative group overflow-visible';

            wrapper.innerHTML = `
                <img 
                    src="${e.target.result}" 
                    alt="Preview ${index + 1}"
                    class="h-32 w-32 rounded-lg object-cover border-2 border-slate-200 dark:border-navy-500"
                />

                <button
                    type="button"
                    onclick="removePreview(this)"
                    class="absolute -top-1.5 -right-1.5 z-50 h-6 w-6 flex items-center justify-center rounded-full
                           bg-error text-white shadow-md ring-2 ring-white dark:ring-navy-700 hover:bg-error/90
                           transition-all"
                    title="Hapus"
                >
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            `;

            previewContainer.appendChild(wrapper);
        };

        reader.readAsDataURL(file);
    });
}

/**
 * REMOVE PREVIEW (UI Only)
 */
function removePreview(button) {
    button.closest('.relative').remove();

    const container = button.closest('[id$="_preview"]');
    if (container && container.children.length === 0) {
        container.classList.add('hidden');
    }
}

const initUserForm = () => {
    console.log('[User Form] Initializing...');

    // ✅ Cek apakah library Tom Select tersedia
    if (typeof Tom === 'undefined') {
        console.error('[User Form] Tom Select library not found!');
        return;
    }

    // Initialize Tom Select untuk semua select dengan data-tom-select
    initTomSelects();

    console.log('[User Form] ✓ Initialized');
};

/**
 * Initialize Tom Select untuk dropdown
 */
function initTomSelects() {
    const selects = document.querySelectorAll('select[data-tom-select="true"]');

    console.log('[Tom Select] Found', selects.length, 'select elements');

    selects.forEach(select => {
        // Skip jika sudah di-init
        if (select.tomselect) {
            console.log('[Tom Select] Already initialized:', select.name);
            return;
        }

        const config = {
            create: false,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
            placeholder: select.querySelector('option[value=""]')?.textContent || 'Pilih opsi...',
            allowEmptyOption: true,
            onInitialize: function() {
                console.log('[Tom Select] ✓ Initialized:', select.name);
            },
            onDropdownOpen: function() {
                console.log('[Tom Select] Dropdown opened:', select.name);
            }
        };

        try {
            new Tom(select, config);
            console.log('[Tom Select] Success init:', select.name);
        } catch (error) {
            console.error('[Tom Select] Error:', select.name, error);
        }
    });
}

// ✅ Initialize setelah app mounted DAN library loaded
if (window.addEventListener) {
    window.addEventListener("app:mounted", () => {
        // Delay untuk memastikan library loaded
        setTimeout(initUserForm, 300);
    }, { once: true });
}

// ✅ Fallback jika app:mounted tidak trigger
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(initUserForm, 300);
    });
} else {
    setTimeout(initUserForm, 300);
}