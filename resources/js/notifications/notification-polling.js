/**
 * Notification Polling Script
 *
 * Polls the server every 30 seconds for new notifications
 * Updates notification badge and dropdown in real-time
 * Shows toast for new notifications
 */

(function() {
    'use strict';

    // Configuration
    const POLL_INTERVAL = 30000; // 30 seconds
    const API_ENDPOINT = '/api/notifications/check';

    // DOM Elements
    let notificationBadge = null;
    let notificationCountBadge = null;
    let notificationList = null;

    // Track shown notifications to avoid duplicate toasts
    let shownNotificationIds = new Set();

    /**
     * Initialize the polling system
     */
    function init() {
        // Get DOM elements
        notificationBadge = document.getElementById('notification-badge');
        notificationCountBadge = document.getElementById('notification-count-badge');
        notificationList = document.getElementById('notification-list');

        // Check if elements exist
        if (!notificationBadge || !notificationCountBadge || !notificationList) {
            return;
        }

        // Initial fetch
        fetchNotifications();

        // Start polling
        setInterval(fetchNotifications, POLL_INTERVAL);
    }

    /**
     * Fetch notifications from API
     */
    function fetchNotifications() {
        fetch(API_ENDPOINT, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                updateNotificationUI(data);

                // Show toast ONLY for truly new notifications (not shown before)
                if (data.new_notifications && data.new_notifications.length > 0) {
                    data.new_notifications.forEach(notification => {
                        if (!shownNotificationIds.has(notification.id)) {
                            showNewNotificationToast(notification);
                            shownNotificationIds.add(notification.id);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    }

    /**
     * Update notification UI elements
     */
    function updateNotificationUI(data) {
        const unreadCount = data.unread_count || 0;

        // Update badge visibility
        if (unreadCount > 0) {
            notificationBadge.style.display = 'flex';
            notificationCountBadge.textContent = unreadCount;
        } else {
            notificationBadge.style.display = 'none';
            notificationCountBadge.textContent = '0';

            // Clear shown notification tracking when no unread
            shownNotificationIds.clear();
        }

        // Update notification list (show latest 10)
        updateNotificationList(data.all_unread || []);
    }

    /**
     * Update notification dropdown list
     */
    function updateNotificationList(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8">
                    <i class="fa-solid fa-bell-slash text-4xl text-slate-300 dark:text-navy-400"></i>
                    <p class="mt-3 text-sm text-slate-500 dark:text-navy-300">Tidak ada notifikasi baru</p>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.slice(0, 10).forEach(notification => {
            html += createNotificationItem(notification);
        });

        notificationList.innerHTML = html;
    }

    /**
     * Create notification item HTML
     */
    function createNotificationItem(notification) {
        const iconClass = notification.icon || 'fa-bell';
        const colorClass = notification.color || 'primary';
        const title = notification.title || 'Notifikasi';
        const message = notification.message || '';
        const createdAt = notification.created_at || 'Baru saja';
        const actionUrl = notification.action?.url || '#';
        const notificationId = notification.id;

        // If has action URL, make it clickable
        if (actionUrl) {
            return `
                <a href="${actionUrl}" 
                   onclick="markNotificationAsRead('${notificationId}')"
                   class="group flex items-start space-x-3 border-b border-slate-150 px-4 py-3 hover:bg-slate-50 dark:border-navy-600 dark:hover:bg-navy-700 transition-colors cursor-pointer">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-${colorClass}/10 text-${colorClass} transition-colors">
                        <i class="fa-solid ${iconClass}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-slate-700 dark:text-navy-100 group-hover:text-primary dark:group-hover:text-accent-light transition-colors">
                            ${title}
                        </p>
                        <p class="mt-1 text-xs text-justify text-slate-500 dark:text-navy-300">
                            ${message}
                        </p>
                        <p class="mt-1 text-xs text-slate-400 dark:text-navy-400">
                            <i class="fa-solid fa-clock mr-1"></i>${createdAt}
                        </p>
                    </div>
                    <div class="flex items-center justify-center text-primary dark:text-accent-light hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 -mr-2.5 py-2 px-3 rounded-lg cursor-pointer">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </div>
                </a>
            `;
        }

        // No action URL - just display
        return `
            <div class="flex items-start space-x-3 border-b border-slate-150 px-4 py-3 hover:bg-slate-50 dark:border-navy-600 dark:hover:bg-navy-700">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-${colorClass}/10 text-${colorClass}">
                    <i class="fa-solid ${iconClass}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-slate-700 dark:text-navy-100">
                        ${title}
                    </p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-navy-300">
                        ${message}
                    </p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-navy-400">
                        <i class="fa-solid fa-clock mr-1"></i>${createdAt}
                    </p>
                </div>
            </div>
        `;
    }

    /**
     * Show toast notification for new notification
     */
    function showNewNotificationToast(notification) {
        // Check if window.$notification is available
        if (typeof window.$notification !== 'function') {
            return;
        }

        const title = notification.title || 'Notifikasi Baru';
        const message = notification.message || '';

        window.$notification({
            variant: notification.color || 'info',
            text: `${title}: ${message}`,
            position: 'center-top',
            duration: 7000
        });
    }

    /**
     * Mark notification as read
     */
    window.markNotificationAsRead = function(notificationId) {
        fetch(`/api/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh notifications
                    fetchNotifications();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
    };

    /**
     * Mark all notifications as read
     */
    window.markAllNotificationsAsRead = function() {
        fetch('/api/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh notifications
                    fetchNotifications();
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();