export function initSessionAlerts() {
    // Pastikan Class Modal tersedia
    if (typeof Modal === 'undefined') {
        console.warn("Class Modal tidak tersedia. Skipping session alert initialization.");
        return;
    }

    const alertDataElement = document.getElementById('session-alert-data');

    if (alertDataElement) {
        const dataJson = alertDataElement.getAttribute('data-json');

        try {
            const data = JSON.parse(dataJson);

            if (data && data.alert_show_id) {
                const modalSelector = '#' + data.alert_show_id;

                try {
                    // Inisialisasi dan SIMPAN INSTANCE modal
                    const alertModal = new Modal(modalSelector);

                    // Tampilkan modal
                    alertModal.open();
                } catch (e) {
                    // Tangani jika elemen modal tidak ditemukan
                    console.error(`[Error] Failed to initialize or show modal ${modalSelector}. Error: ${e.message}`);
                }

            }
        } catch (e) {
            console.error("Error parsing session alert data:", e);
        }

        // Hapus elemen data setelah digunakan
        alertDataElement.remove();
    }
}

export function initSessionNotifications() {
    // Pastikan fungsi global $notification tersedia
    if (typeof window.$notification === 'undefined') {
        console.warn("$notification function not found. Skipping session notification initialization.");
        return;
    }

    const notificationDataElement = document.getElementById('session-notification-data');

    if (notificationDataElement) {
        const dataJson = notificationDataElement.getAttribute('data-json');

        try {
            const options = JSON.parse(dataJson);

            if (options && options.text) {
                window.$notification({
                    text: options.text,
                    variant: options.type || 'info',
                    position: options.position || 'bottom-right',
                    duration: options.duration || 5000,
                });
                console.log(`[Success] Showing session notification: ${options.text.substring(0, 30)}...`);
            }
        } catch (e) {
            console.error("Error parsing session notification data:", e);
        }

        notificationDataElement.remove();
    }
}

export function initSessionHandlers() {
    initSessionAlerts();
    initSessionNotifications();
}