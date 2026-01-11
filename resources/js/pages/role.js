const onLoad = () => {
    // Dropdown Config
    const dropdownConfig = {
        placement: "bottom",
        modifiers: [{ name: "offset", options: { offset: [0, 4] } }],
    };

    // Setup Popper untuk action menu
    document.querySelectorAll(".role-action-menu").forEach((menu) => {
        if (!menu.id) return; // Skip jika tidak ada ID

        const id = "#" + menu.id;
        new Popper(id, ".popper-ref", ".popper-root", dropdownConfig);
    });

    // Setup observer untuk detect modal dibuka
    setupModalOpenObserver();
};

// =============================================
// GLOBAL FUNCTIONS (dipanggil dari HTML)
// =============================================

window.checkAllPermissions = function() {
    const activeModal = document.querySelector('.modal.is-open');
    if (!activeModal) return;

    activeModal.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateGroupCheckboxesForModal(activeModal);
}

window.uncheckAllPermissions = function() {
    const activeModal = document.querySelector('.modal.is-open');
    if (!activeModal) return;

    activeModal.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateGroupCheckboxesForModal(activeModal);
}

window.toggleGroupPermissions = function(masterCheckbox, groupSlug) {
    const activeModal = document.querySelector('.modal.is-open');
    if (!activeModal) return;

    const isChecked = masterCheckbox.checked;
    activeModal.querySelectorAll(`input[data-group="${groupSlug}"]`).forEach(checkbox => {
        checkbox.checked = isChecked;
    });
}

// =============================================
// HELPER FUNCTIONS
// =============================================

function updateGroupCheckbox(modal, groupSlug) {
    const groupCheckboxes = modal.querySelectorAll(`input[data-group="${groupSlug}"]`);
    const groupMasterCheckbox = modal.querySelector(`input[onchange*="${groupSlug}"]`);

    if (!groupMasterCheckbox || groupCheckboxes.length === 0) return;

    const checkedCount = Array.from(groupCheckboxes).filter(cb => cb.checked).length;
    const totalCount = groupCheckboxes.length;

    if (checkedCount === 0) {
        groupMasterCheckbox.checked = false;
        groupMasterCheckbox.indeterminate = false;
    } else if (checkedCount === totalCount) {
        groupMasterCheckbox.checked = true;
        groupMasterCheckbox.indeterminate = false;
    } else {
        groupMasterCheckbox.checked = false;
        groupMasterCheckbox.indeterminate = true;
    }
}

function updateGroupCheckboxesForModal(modal) {
    const groupSlugs = new Set();
    modal.querySelectorAll('input[data-group]').forEach(checkbox => {
        groupSlugs.add(checkbox.dataset.group);
    });

    groupSlugs.forEach(slug => updateGroupCheckbox(modal, slug));
}

function setupPermissionCheckboxListeners(modal) {
    // Hapus listener lama dengan clone (prevent duplicate)
    const checkboxes = modal.querySelectorAll('input[name="permissions[]"]');

    checkboxes.forEach(checkbox => {
        // Clone untuk remove semua event listeners
        const clone = checkbox.cloneNode(true);
        checkbox.parentNode.replaceChild(clone, checkbox);
    });

    // Setup listener baru
    modal.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const groupSlug = this.dataset.group;
            if (groupSlug) {
                updateGroupCheckbox(modal, groupSlug);
            }
        });
    });

    console.log('[Role] Setup listeners untuk modal:', modal.id, '- Checkboxes:', checkboxes.length);
}

function setupModalOpenObserver() {
    const modals = document.querySelectorAll('[id*="role-modal"]');

    modals.forEach(modal => {
        // Skip jika tidak ada ID
        if (!modal.id) return;

        // Observer untuk detect modal dibuka
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isOpen = modal.classList.contains('is-open');
                    if (isOpen) {
                        console.log('[Role] Modal opened:', modal.id);

                        setTimeout(() => {
                            setupPermissionCheckboxListeners(modal);
                            updateGroupCheckboxesForModal(modal);
                        }, 100);
                    }
                }
            });
        });

        observer.observe(modal, {
            attributes: true,
            attributeFilter: ['class']
        });
    });

    // Backup: Listen klik button
    document.querySelectorAll('[data-toggle="modal"][data-target*="role-modal"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const targetId = btn.dataset.target;
            const targetModal = document.querySelector(targetId);

            if (targetModal) {
                setTimeout(() => {
                    setupPermissionCheckboxListeners(targetModal);
                    updateGroupCheckboxesForModal(targetModal);
                }, 200);
            }
        });
    });

    console.log('[Role] Observer setup complete untuk', modals.length, 'modal(s)');
}

window.addEventListener("app:mounted", onLoad, { once: true });