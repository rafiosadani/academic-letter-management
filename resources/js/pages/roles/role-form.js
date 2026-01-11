/**
 * role-form.js
 * Handler untuk halaman form (create/edit) role
 */

const initRoleForm = () => {
    // Setup permission checkbox handlers
    setupPermissionHandlers();

    // Update semua group checkboxes on load
    updateAllGroupCheckboxes();

    // Optional: Setup form validation
    // setupFormValidation();
};

// =============================================
// GLOBAL FUNCTIONS (dipanggil dari HTML)
// =============================================

/**
 * Check semua permissions di halaman
 */
window.checkAllPermissions = function() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');

    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });

    updateAllGroupCheckboxes();
}

/**
 * Uncheck semua permissions di halaman
 */
window.uncheckAllPermissions = function() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');

    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });

    updateAllGroupCheckboxes();
}

/**
 * Toggle permissions per group
 * @param {HTMLInputElement} masterCheckbox - Checkbox master untuk group
 * @param {string} groupSlug - Slug group (e.g., 'master-data')
 */
window.toggleGroupPermissions = function(masterCheckbox, groupSlug) {
    const isChecked = masterCheckbox.checked;
    const groupCheckboxes = document.querySelectorAll(`input[data-group="${groupSlug}"]`);

    groupCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
}

// =============================================
// HELPER FUNCTIONS
// =============================================

/**
 * Setup event listeners untuk semua permission checkboxes
 */
function setupPermissionHandlers() {
    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    if (permissionCheckboxes.length === 0) {
        return;
    }

    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const groupSlug = this.dataset.group;
            if (groupSlug) {
                updateGroupCheckbox(groupSlug);
            }
        });
    });
}

/**
 * Update status checkbox master untuk group tertentu
 * @param {string} groupSlug - Slug group
 */
function updateGroupCheckbox(groupSlug) {
    const groupCheckboxes = document.querySelectorAll(`input[data-group="${groupSlug}"]`);
    const groupMasterCheckbox = document.querySelector(`input[onchange*="${groupSlug}"]`);

    if (!groupMasterCheckbox) {
        console.warn(`[Role Form] Master checkbox untuk group "${groupSlug}" tidak ditemukan`);
        return;
    }

    if (groupCheckboxes.length === 0) {
        console.warn(`[Role Form] Tidak ada checkbox untuk group "${groupSlug}"`);
        return;
    }

    const checkedCount = Array.from(groupCheckboxes).filter(cb => cb.checked).length;
    const totalCount = groupCheckboxes.length;

    // Set status master checkbox
    if (checkedCount === 0) {
        // Tidak ada yang checked
        groupMasterCheckbox.checked = false;
        groupMasterCheckbox.indeterminate = false;
    } else if (checkedCount === totalCount) {
        // Semua checked
        groupMasterCheckbox.checked = true;
        groupMasterCheckbox.indeterminate = false;
    } else {
        // Sebagian checked (indeterminate)
        groupMasterCheckbox.checked = false;
        groupMasterCheckbox.indeterminate = true;
    }
}

/**
 * Update semua group checkboxes
 */
function updateAllGroupCheckboxes() {
    // Dapatkan semua unique group slugs
    const groupSlugs = new Set();

    document.querySelectorAll('input[data-group]').forEach(checkbox => {
        const group = checkbox.dataset.group;
        if (group) {
            groupSlugs.add(group);
        }
    });

    // Update setiap group
    groupSlugs.forEach(slug => updateGroupCheckbox(slug));
}

// =============================================
// FORM VALIDATION (Optional - Uncomment jika perlu)
// =============================================

/**
 * Validasi form sebelum submit
 * Memastikan minimal 1 permission dipilih
 */
function setupFormValidation() {
    const form = document.querySelector('form');

    if (!form) {
        console.warn('[Role Form] Form tidak ditemukan');
        return;
    }

    form.addEventListener('submit', function(e) {
        const selectedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');

        // Cek apakah ada permission yang dipilih
        if (selectedPermissions.length === 0) {
            e.preventDefault();

            // Tampilkan alert
            alert('Harap pilih minimal satu permission untuk role ini!');

            // Scroll ke permission table
            const permissionCard = document.querySelector('.card:has(input[name="permissions[]"])');
            if (permissionCard) {
                permissionCard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
            return false;
        }
    });
}

// =============================================
// INITIALIZE
// =============================================

if (window.addEventListener) {
    window.addEventListener("app:mounted", initRoleForm, { once: true });

    // Fallback untuk direct page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRoleForm);
    } else {
        initRoleForm();
    }
}