/**
 * Real-time Dashboard Component
 * Handles live dashboard updates using Laravel Echo and AJAX polling
 */

class RealtimeDashboard {
    constructor(options = {}) {
        this.updateInterval = options.updateInterval || 30000; // 30 seconds
        this.chartUpdateInterval = options.chartUpdateInterval || 60000; // 1 minute
        this.notificationCheckInterval = options.notificationCheckInterval || 10000; // 10 seconds
        
        this.statsContainer = document.getElementById('dashboard-stats');
        this.chartContainer = document.getElementById('dashboard-chart');
        this.notificationContainer = document.getElementById('notification-container');
        
        this.intervalIds = [];
        this.isActive = true;
        
        this.init();
    }

    init() {
        this.setupRealtimeListeners();
        this.startPeriodicUpdates();
        this.loadInitialData();
        this.setupVisibilityHandling();
    }

    setupRealtimeListeners() {
        if (typeof window.Echo !== 'undefined') {
            // Listen for booking updates
            window.Echo.channel('bookings')
                .listen('.booking.status.updated', (e) => {
                    this.handleBookingUpdate(e);
                    this.updateStats();
                });

            // Listen for user-specific notifications
            const userId = this.getCurrentUserId();
            if (userId) {
                window.Echo.private(`user.${userId}`)
                    .listen('.notification.sent', (e) => {
                        this.handleNewNotification(e);
                    });
            }
        }
    }

    startPeriodicUpdates() {
        // Update stats every 30 seconds
        const statsInterval = setInterval(() => {
            if (this.isActive) {
                this.updateStats();
            }
        }, this.updateInterval);
        this.intervalIds.push(statsInterval);

        // Update charts every minute
        const chartInterval = setInterval(() => {
            if (this.isActive) {
                this.updateCharts();
            }
        }, this.chartUpdateInterval);
        this.intervalIds.push(chartInterval);

        // Check notifications every 10 seconds
        const notificationInterval = setInterval(() => {
            if (this.isActive) {
                this.checkNotifications();
            }
        }, this.notificationCheckInterval);
        this.intervalIds.push(notificationInterval);
    }

    async loadInitialData() {
        await Promise.all([
            this.updateStats(),
            this.updateCharts(),
            this.checkNotifications()
        ]);
    }

    async updateStats() {
        try {
            const response = await fetch('/api/realtime/dashboard-stats', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.renderStats(data.stats);
            }
        } catch (error) {
            console.error('Failed to update stats:', error);
        }
    }

    async updateCharts() {
        try {
            const response = await fetch('/api/realtime/booking-stats', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.renderChart(data.hourly_bookings);
            }
        } catch (error) {
            console.error('Failed to update charts:', error);
        }
    }

    async checkNotifications() {
        try {
            const response = await fetch('/api/realtime/notifications', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateNotificationBadge(data.unread_count);
            }
        } catch (error) {
            console.error('Failed to check notifications:', error);
        }
    }

    renderStats(stats) {
        if (!this.statsContainer) return;

        const statsHtml = Object.entries(stats).map(([key, value]) => {
            if (key === 'last_updated') return '';
            
            const label = this.formatStatLabel(key);
            const formattedValue = this.formatStatValue(key, value);
            
            return `
                <div class="stat-card" data-stat="${key}">
                    <div class="stat-label">${label}</div>
                    <div class="stat-value" data-value="${value}">${formattedValue}</div>
                </div>
            `;
        }).join('');

        this.statsContainer.innerHTML = statsHtml;
        
        // Update last updated time
        const lastUpdated = document.getElementById('last-updated');
        if (lastUpdated && stats.last_updated) {
            lastUpdated.textContent = `Last updated: ${stats.last_updated}`;
        }

        // Animate stat changes
        this.animateStatChanges();
    }

    renderChart(hourlyData) {
        if (!this.chartContainer || !hourlyData) return;

        // Simple chart rendering (you can replace with Chart.js or similar)
        const maxBookings = Math.max(...hourlyData.map(h => h.bookings));
        const chartHeight = 200;

        let chartHtml = '<div class="chart-container">';
        chartHtml += '<div class="chart-title">Hourly Bookings</div>';
        chartHtml += '<div class="chart-bars">';

        hourlyData.forEach(hour => {
            const barHeight = maxBookings > 0 ? (hour.bookings / maxBookings) * chartHeight : 0;
            chartHtml += `
                <div class="chart-bar" style="height: ${barHeight}px;" title="${hour.hour}:00 - ${hour.bookings} bookings">
                    <div class="bar-value">${hour.bookings}</div>
                </div>
            `;
        });

        chartHtml += '</div></div>';
        this.chartContainer.innerHTML = chartHtml;
    }

    handleBookingUpdate(event) {
        const { booking_id, status } = event;
        
        // Show notification
        this.showNotification(`Booking ${booking_id} status updated to ${status}`, 'info');
        
        // Update relevant UI elements
        this.highlightStatCard('today_bookings');
        if (status === 'confirmed') {
            this.highlightStatCard('today_revenue');
        }
    }

    handleNewNotification(event) {
        this.showNotification(event.message, event.priority || 'info');
        this.updateNotificationBadge();
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;

        if (this.notificationContainer) {
            this.notificationContainer.appendChild(notification);
        } else {
            document.body.appendChild(notification);
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    updateNotificationBadge(count) {
        const badge = document.getElementById('notification-badge');
        if (badge) {
            if (count && count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    highlightStatCard(statKey) {
        const card = document.querySelector(`[data-stat="${statKey}"]`);
        if (card) {
            card.classList.add('stat-updated');
            setTimeout(() => {
                card.classList.remove('stat-updated');
            }, 2000);
        }
    }

    animateStatChanges() {
        const statValues = document.querySelectorAll('.stat-value[data-value]');
        statValues.forEach(element => {
            const currentValue = parseInt(element.dataset.value) || 0;
            const previousValue = parseInt(element.dataset.previousValue) || 0;
            
            if (currentValue !== previousValue) {
                element.classList.add('stat-changed');
                setTimeout(() => {
                    element.classList.remove('stat-changed');
                }, 1000);
            }
            
            element.dataset.previousValue = currentValue;
        });
    }

    formatStatLabel(key) {
        const labels = {
            'today_bookings': 'Today\'s Bookings',
            'today_revenue': 'Today\'s Revenue',
            'pending_bookings': 'Pending Bookings',
            'active_schedules': 'Active Schedules',
            'total_users': 'Total Users',
            'total_operators': 'Total Operators',
            'monthly_revenue': 'Monthly Revenue',
            'total_bookings': 'Total Bookings',
            'confirmed_bookings': 'Confirmed Bookings',
            'upcoming_trips': 'Upcoming Trips',
            'total_spent': 'Total Spent'
        };
        return labels[key] || key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    formatStatValue(key, value) {
        if (key.includes('revenue') || key.includes('spent')) {
            return `NRs ${parseFloat(value).toLocaleString()}`;
        }
        return parseInt(value).toLocaleString();
    }

    setupVisibilityHandling() {
        document.addEventListener('visibilitychange', () => {
            this.isActive = !document.hidden;
            if (this.isActive) {
                // Refresh data when tab becomes visible
                this.loadInitialData();
            }
        });
    }

    getCurrentUserId() {
        const meta = document.querySelector('meta[name="user-id"]');
        return meta ? meta.getAttribute('content') : null;
    }

    getAuthToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    destroy() {
        this.isActive = false;
        this.intervalIds.forEach(id => clearInterval(id));
        this.intervalIds = [];
    }
}

// Auto-initialize if container exists
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('dashboard-stats')) {
        window.realtimeDashboard = new RealtimeDashboard();
    }
});

// Export for manual initialization
window.RealtimeDashboard = RealtimeDashboard;
