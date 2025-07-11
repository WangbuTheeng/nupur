/**
 * Real-time Notifications Component
 * Handles live notifications using Laravel Echo and WebSockets
 */

class RealtimeNotifications {
    constructor(options = {}) {
        this.container = options.container || document.getElementById('notification-container');
        this.badgeElement = options.badge || document.getElementById('notification-badge');
        this.bellElement = options.bell || document.querySelector('[data-notification-bell]');
        this.dropdownElement = options.dropdown || document.getElementById('notification-dropdown');
        
        this.notifications = [];
        this.unreadCount = 0;
        this.maxNotifications = options.maxNotifications || 5;
        this.autoHideDelay = options.autoHideDelay || 5000;
        
        this.init();
    }

    init() {
        this.setupRealtimeListeners();
        this.setupUIHandlers();
        this.loadInitialNotifications();
    }

    setupRealtimeListeners() {
        if (typeof window.Echo !== 'undefined') {
            const userId = this.getCurrentUserId();
            if (userId) {
                // Listen for user-specific notifications
                window.Echo.private(`user.${userId}`)
                    .listen('.notification.sent', (e) => {
                        this.handleNewNotification(e);
                    });
            }
        }
    }

    setupUIHandlers() {
        // Bell click handler
        if (this.bellElement) {
            this.bellElement.addEventListener('click', () => {
                this.toggleDropdown();
            });
        }

        // Click outside to close dropdown
        document.addEventListener('click', (e) => {
            if (this.dropdownElement && 
                !this.dropdownElement.contains(e.target) && 
                !this.bellElement?.contains(e.target)) {
                this.hideDropdown();
            }
        });

        // Mark all as read button
        const markAllReadBtn = document.getElementById('mark-all-read-btn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }
    }

    async loadInitialNotifications() {
        try {
            const response = await fetch('/api/realtime/notifications', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                this.updateBadge();
                this.renderDropdownNotifications();
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    handleNewNotification(notificationData) {
        // Add to notifications array
        this.notifications.unshift(notificationData);
        
        // Keep only max notifications
        if (this.notifications.length > this.maxNotifications) {
            this.notifications = this.notifications.slice(0, this.maxNotifications);
        }

        // Update unread count
        this.unreadCount++;
        this.updateBadge();

        // Show toast notification
        this.showToastNotification(notificationData);

        // Update dropdown if open
        if (this.dropdownElement && !this.dropdownElement.classList.contains('hidden')) {
            this.renderDropdownNotifications();
        }

        // Play notification sound
        this.playNotificationSound(notificationData.priority);
    }

    showToastNotification(notification) {
        const toast = document.createElement('div');
        toast.className = `notification-toast notification-${notification.priority || 'medium'}`;
        
        toast.innerHTML = `
            <div class="notification-toast-content">
                <div class="notification-toast-header">
                    <h4 class="notification-toast-title">${notification.title}</h4>
                    <button class="notification-toast-close" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <p class="notification-toast-message">${notification.message}</p>
                ${notification.action_url ? `
                    <div class="notification-toast-actions">
                        <a href="${notification.action_url}" class="notification-toast-action">
                            ${notification.action_text || 'View'}
                        </a>
                    </div>
                ` : ''}
            </div>
        `;

        // Add to container
        if (this.container) {
            this.container.appendChild(toast);
        } else {
            document.body.appendChild(toast);
        }

        // Auto-hide after delay
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, this.autoHideDelay);

        // Add entrance animation
        setTimeout(() => {
            toast.classList.add('notification-toast-show');
        }, 10);
    }

    updateBadge() {
        if (this.badgeElement) {
            if (this.unreadCount > 0) {
                this.badgeElement.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                this.badgeElement.classList.remove('hidden');
                this.badgeElement.classList.add('notification-badge-pulse');
            } else {
                this.badgeElement.classList.add('hidden');
                this.badgeElement.classList.remove('notification-badge-pulse');
            }
        }
    }

    toggleDropdown() {
        if (this.dropdownElement) {
            if (this.dropdownElement.classList.contains('hidden')) {
                this.showDropdown();
            } else {
                this.hideDropdown();
            }
        }
    }

    showDropdown() {
        if (this.dropdownElement) {
            this.dropdownElement.classList.remove('hidden');
            this.renderDropdownNotifications();
        }
    }

    hideDropdown() {
        if (this.dropdownElement) {
            this.dropdownElement.classList.add('hidden');
        }
    }

    renderDropdownNotifications() {
        if (!this.dropdownElement) return;

        const notificationsList = this.dropdownElement.querySelector('.notifications-list');
        if (!notificationsList) return;

        if (this.notifications.length === 0) {
            notificationsList.innerHTML = `
                <div class="notification-empty">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h8v-2H4v2zM4 11h10V9H4v2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No notifications</p>
                </div>
            `;
            return;
        }

        const notificationsHtml = this.notifications.map(notification => `
            <div class="notification-item ${notification.read_at ? 'read' : 'unread'}" data-id="${notification.id}">
                <div class="notification-item-content">
                    <div class="notification-item-header">
                        <h5 class="notification-item-title">${notification.title}</h5>
                        <span class="notification-item-time">${this.formatTime(notification.timestamp)}</span>
                    </div>
                    <p class="notification-item-message">${notification.message}</p>
                    ${notification.action_url ? `
                        <div class="notification-item-actions">
                            <a href="${notification.action_url}" class="notification-item-action">
                                ${notification.action_text || 'View'}
                            </a>
                        </div>
                    ` : ''}
                </div>
                ${!notification.read_at ? '<div class="notification-unread-indicator"></div>' : ''}
            </div>
        `).join('');

        notificationsList.innerHTML = notificationsHtml;

        // Add click handlers for marking as read
        notificationsList.querySelectorAll('.notification-item.unread').forEach(item => {
            item.addEventListener('click', () => {
                this.markAsRead(item.dataset.id);
            });
        });
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                }
            });

            if (response.ok) {
                // Update local notification
                const notification = this.notifications.find(n => n.id == notificationId);
                if (notification && !notification.read_at) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    this.updateBadge();
                    this.renderDropdownNotifications();
                }
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                }
            });

            if (response.ok) {
                // Update all notifications as read
                this.notifications.forEach(notification => {
                    notification.read_at = new Date().toISOString();
                });
                this.unreadCount = 0;
                this.updateBadge();
                this.renderDropdownNotifications();
            }
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }

    playNotificationSound(priority = 'medium') {
        // Create audio element for notification sound
        const audio = new Audio();
        
        switch (priority) {
            case 'high':
                audio.src = '/sounds/notification-high.mp3';
                break;
            case 'low':
                audio.src = '/sounds/notification-low.mp3';
                break;
            default:
                audio.src = '/sounds/notification-medium.mp3';
        }

        // Play sound (with user permission)
        audio.play().catch(() => {
            // Ignore errors if user hasn't interacted with page yet
        });
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));

        if (diffInMinutes < 1) {
            return 'Just now';
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes}m ago`;
        } else if (diffInMinutes < 1440) {
            return `${Math.floor(diffInMinutes / 60)}h ago`;
        } else {
            return date.toLocaleDateString();
        }
    }

    getCurrentUserId() {
        const meta = document.querySelector('meta[name="user-id"]');
        return meta ? meta.getAttribute('content') : null;
    }

    getAuthToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    destroy() {
        // Clean up event listeners and intervals
        if (this.bellElement) {
            this.bellElement.removeEventListener('click', this.toggleDropdown);
        }
    }
}

// Auto-initialize if notification elements exist
document.addEventListener('DOMContentLoaded', () => {
    const bellElement = document.querySelector('[data-notification-bell]');
    if (bellElement) {
        window.realtimeNotifications = new RealtimeNotifications();
    }
});

// Export for manual initialization
window.RealtimeNotifications = RealtimeNotifications;
