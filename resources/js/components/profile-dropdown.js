export function initProfileDropdown() {
    initSingleProfile('header-profile', 'bottom-end');
    initSingleProfile('sidebar-profile', 'right-end');
}

function initSingleProfile(prefix, placement = 'bottom-end') {
    const wrapperId = `${prefix}-wrapper`;
    const wrapper = document.querySelector(`#${wrapperId}`);

    if (!wrapper) {
        return;
    }

    const config = {
        placement: placement,
        modifiers: [{
            name: "offset",
            options: {offset: [0, 12]}
        }]
    };

    if (typeof window.Popper !== 'undefined') {
        new window.Popper(
            `#${wrapperId}`,
            `#${prefix}-ref`,
            `#${prefix}-box`,
            config
        );
    }
}