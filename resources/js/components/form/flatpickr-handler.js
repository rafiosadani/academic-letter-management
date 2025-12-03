/**
 * Flatpickr Handler (Date/Time Picker)
 * File: resources/js/components/flatpickr-handler.js
 */

import flatpickr from 'flatpickr';
// import 'flatpickr/dist/flatpickr.min.css';

// Import Indonesian locale
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

// Store instances
const flatpickrInstances = new Map();

/**
 * Initialize Flatpickr
 */
export function initFlatpickr(container = document) {
    console.log('[Flatpickr] Initializing...');

    const dateInputs = container.querySelectorAll('input[data-flatpickr="true"]');

    if (dateInputs.length === 0) {
        console.log('[Flatpickr] No date inputs found');
        return;
    }

    dateInputs.forEach(input => {
        // Skip if already initialized
        if (input._flatpickr) {
            console.log('[Flatpickr] Already initialized:', input.name);
            return;
        }

        try {
            const instance = createFlatpickrInstance(input);
            flatpickrInstances.set(input, instance);
            console.log('[Flatpickr] ✓ Initialized:', input.name);
        } catch (error) {
            console.error('[Flatpickr] ✗ Failed to initialize:', input.name, error);
        }
    });

    console.log(`[Flatpickr] ✓ Initialized ${dateInputs.length} input(s)`);
}

/**
 * Create Flatpickr instance dengan config dari data attributes
 */
function createFlatpickrInstance(input) {
    const mode = input.dataset.flatpickrMode || 'single';
    const enableTime = input.dataset.flatpickrEnableTime === 'true';
    const dateFormat = input.dataset.flatpickrDateFormat || 'Y-m-d';
    const minDate = input.dataset.flatpickrMinDate || null;
    const maxDate = input.dataset.flatpickrMaxDate || null;
    const defaultDate = input.value || null;

    const config = {
        // Basic settings
        mode: mode, // single, multiple, range
        enableTime: enableTime,
        dateFormat: enableTime ? `${dateFormat} H:i` : dateFormat,
        time_24hr: true,

        // Localization
        locale: Indonesian,

        // Date constraints
        minDate: minDate,
        maxDate: maxDate,
        defaultDate: defaultDate,

        // UI settings
        allowInput: true,
        clickOpens: true,

        // Styling
        prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>',
        nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>',

        // Callbacks
        onReady: function(selectedDates, dateStr, instance) {
            applyDarkMode(instance);
            console.log('[Flatpickr] Ready:', input.name);
        },

        onChange: function(selectedDates, dateStr, instance) {
            console.log('[Flatpickr] Date changed:', dateStr);

            // Trigger change event untuk validation
            input.dispatchEvent(new Event('change', { bubbles: true }));
        },

        onOpen: function(selectedDates, dateStr, instance) {
            applyDarkMode(instance);
        },
    };

    // Mode-specific configurations
    if (mode === 'range') {
        config.dateFormat = enableTime ? 'Y-m-d H:i' : 'Y-m-d';
    }

    if (mode === 'multiple') {
        config.conjunction = ', ';
    }

    return flatpickr(input, config);
}

/**
 * Apply dark mode styling
 */
function applyDarkMode(instance) {
    const isDark = document.documentElement.classList.contains('dark');

    if (isDark && instance.calendarContainer) {
        instance.calendarContainer.classList.add('flatpickr-dark');
    }
}

/**
 * Destroy Flatpickr instance
 */
export function destroyFlatpickr(input) {
    const instance = flatpickrInstances.get(input);
    if (instance) {
        instance.destroy();
        flatpickrInstances.delete(input);
        console.log('[Flatpickr] Destroyed:', input.name);
    }
}

/**
 * Destroy all Flatpickr instances
 */
export function destroyAllFlatpickr() {
    flatpickrInstances.forEach((instance) => {
        instance.destroy();
    });
    flatpickrInstances.clear();
    console.log('[Flatpickr] All instances destroyed');
}

/**
 * Get Flatpickr instance
 */
export function getFlatpickrInstance(input) {
    return flatpickrInstances.get(input);
}

/**
 * Set date programmatically
 */
export function setFlatpickrDate(input, date) {
    const instance = flatpickrInstances.get(input);
    if (instance) {
        instance.setDate(date);
    }
}

/**
 * Clear date
 */
export function clearFlatpickrDate(input) {
    const instance = flatpickrInstances.get(input);
    if (instance) {
        instance.clear();
    }
}

// Export untuk global access
window.FlatpickrHandler = {
    init: initFlatpickr,
    destroy: destroyFlatpickr,
    destroyAll: destroyAllFlatpickr,
    getInstance: getFlatpickrInstance,
    setDate: setFlatpickrDate,
    clearDate: clearFlatpickrDate,
};