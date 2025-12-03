/**
 * Tom Select Handler
 * File: resources/js/components/select-handler.js
 */

import TomSelect from 'tom-select';
// import 'tom-select/dist/css/tom-select.css';

// Store instances untuk cleanup
const tomSelectInstances = new Map();

/**
 * Initialize Tom Select pada semua select elements
 */
export function initTomSelect(container = document) {
    console.log('[Tom Select] Initializing...');

    const selects = container.querySelectorAll('select[data-tom-select="true"]');

    if (selects.length === 0) {
        console.log('[Tom Select] No select elements found');
        return;
    }

    selects.forEach(select => {
        // Skip jika sudah diinisialisasi
        if (select.tomselect) {
            console.log('[Tom Select] Already initialized:', select.name);
            return;
        }

        // Get data attributes
        const searchable = select.dataset.tomSearchable !== 'false';
        const creatable = select.dataset.tomCreatable === 'true';
        const placeholder = select.querySelector('option[value=""]')?.textContent || 'Pilih opsi...';

        try {
            const instance = new TomSelect(select, {
                plugins: {
                    remove_button: {
                        title: 'Hapus item ini',
                    }
                },
                allowEmptyOption: true,
                create: creatable,
                searchField: searchable ? ['text'] : [],
                placeholder: placeholder,
                hidePlaceholder: false,
                onInitialize: function() {
                    console.log('[Tom Select] ✓ Initialized:', select.name);
                },
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results">Tidak ada hasil untuk "' + escape(data.input) + '"</div>';
                    },
                }
            });

            // Store instance
            tomSelectInstances.set(select, instance);

        } catch (error) {
            console.error('[Tom Select] ✗ Failed to initialize:', select.name, error);
        }
    });

    console.log(`[Tom Select] ✓ Initialized ${selects.length} select(s)`);
}

/**
 * Destroy Tom Select instance
 */
export function destroyTomSelect(select) {
    const instance = tomSelectInstances.get(select);
    if (instance) {
        instance.destroy();
        tomSelectInstances.delete(select);
        console.log('[Tom Select] Destroyed:', select.name);
    }
}

/**
 * Destroy all Tom Select instances
 */
export function destroyAllTomSelect() {
    tomSelectInstances.forEach((instance, select) => {
        instance.destroy();
    });
    tomSelectInstances.clear();
    console.log('[Tom Select] All instances destroyed');
}

/**
 * Get Tom Select instance
 */
export function getTomSelectInstance(select) {
    return tomSelectInstances.get(select);
}

// Export untuk global access
window.TomSelectHandler = {
    init: initTomSelect,
    destroy: destroyTomSelect,
    destroyAll: destroyAllTomSelect,
    getInstance: getTomSelectInstance,
};