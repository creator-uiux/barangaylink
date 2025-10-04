/**
 * JavaScript functionality for BarangayLink PHP version
 * Handles interactive elements and AJAX requests
 */

// Global app object
window.BarangayLink = {
    // Configuration
    config: {
        refreshInterval: 30000, // 30 seconds
        notificationTimeout: 5000, // 5 seconds
    },

    // Initialize the application
    init() {
        this.setupEventListeners();
        this.initializeRealTime();
        this.loadNotifications();
    },

    // Setup global event listeners
    setupEventListeners() {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                if (mobileOverlay) {
                    mobileOverlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
                }
            });
        }

        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                mobileOverlay.style.display = 'none';
            });
        }

        // Form submissions
        const forms = document.querySelectorAll('form[data-ajax="true"]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitForm(form);
            });
        });

        // Notification dismiss
        document.addEventListener('click', (e) => {
            if (e.target.matches('.notification-dismiss')) {
                e.target.closest('.notification').remove();
            }
        });

        // Dropdown toggles
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-toggle="dropdown"]')) {
                const dropdown = document.querySelector(e.target.dataset.target);
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                }
            }
        });

        // Modal triggers
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-toggle="modal"]')) {
                const modalId = e.target.dataset.target;
                this.showModal(modalId);
            }
        });

        // Tab switching
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-toggle="tab"]')) {
                e.preventDefault();
                this.switchTab(e.target);
            }
        });
    },

    // Initialize real-time features
    initializeRealTime() {
        // Update clock every second
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);

        // Refresh data periodically
        setInterval(() => {
            this.refreshData();
        }, this.config.refreshInterval);
    },

    // Update clock display
    updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        const dateString = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const timeElement = document.getElementById('current-time');
        const dateElement = document.getElementById('current-date');

        if (timeElement) timeElement.textContent = timeString;
        if (dateElement) dateElement.textContent = dateString;
    },

    // Refresh data from server
    refreshData() {
        this.loadNotifications();
        this.updateStats();
    },

    // Load notifications
    loadNotifications() {
        fetch('api/get-notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateNotificationBadge(data.unreadCount);
                    this.displayNotifications(data.notifications);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
    },

    // Update notification badge
    updateNotificationBadge(count) {
        const badge = document.getElementById('notification-count');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
    },

    // Display notifications
    displayNotifications(notifications) {
        const container = document.getElementById('notifications-list');
        if (!container) return;

        container.innerHTML = '';
        notifications.forEach(notification => {
            const element = this.createNotificationElement(notification);
            container.appendChild(element);
        });
    },

    // Create notification element
    createNotificationElement(notification) {
        const div = document.createElement('div');
        div.className = `notification p-4 border-b border-gray-200 ${notification.read ? 'opacity-60' : ''}`;
        
        div.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center ${this.getNotificationIconClass(notification.type)}">
                    ${this.getNotificationIcon(notification.type)}
                </div>
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${notification.title}</h4>
                    <p class="text-sm text-gray-600">${notification.message}</p>
                    <p class="text-xs text-gray-500 mt-1">${this.timeAgo(notification.timestamp)}</p>
                </div>
                <button class="notification-dismiss text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        return div;
    },

    // Get notification icon class
    getNotificationIconClass(type) {
        const classes = {
            success: 'bg-green-100 text-green-600',
            warning: 'bg-yellow-100 text-yellow-600',
            error: 'bg-red-100 text-red-600',
            info: 'bg-blue-100 text-blue-600'
        };
        return classes[type] || classes.info;
    },

    // Get notification icon
    getNotificationIcon(type) {
        const icons = {
            success: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            warning: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            error: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            info: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        return icons[type] || icons.info;
    },

    // Time ago function
    timeAgo(dateString) {
        const now = new Date();
        const then = new Date(dateString);
        const diffInSeconds = Math.floor((now - then) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        if (diffInSeconds < 31536000) return `${Math.floor(diffInSeconds / 2592000)}mo ago`;
        return `${Math.floor(diffInSeconds / 31536000)}y ago`;
    },

    // Submit form via AJAX
    submitForm(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML += ' <span class="loading-spinner"></span>';
        }

        fetch(form.action || window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('success', 'Success', data.message || 'Operation completed successfully');
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    // Refresh current data
                    this.refreshData();
                }
            } else {
                this.showNotification('error', 'Error', data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('error', 'Error', 'An unexpected error occurred');
        })
        .finally(() => {
            // Remove loading state
            if (submitBtn) {
                submitBtn.disabled = false;
                const spinner = submitBtn.querySelector('.loading-spinner');
                if (spinner) spinner.remove();
            }
        });
    },

    // Show notification
    showNotification(type, title, message) {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification ${this.getNotificationClass(type)} p-4 rounded-lg shadow-lg max-w-sm mb-2`;
        
        notification.innerHTML = `
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-medium">${title}</h4>
                    <p class="text-sm opacity-90">${message}</p>
                </div>
                <button class="notification-dismiss ml-4 hover:opacity-70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        container.appendChild(notification);

        // Auto-remove after timeout
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, this.config.notificationTimeout);
    },

    // Get notification class
    getNotificationClass(type) {
        const classes = {
            success: 'bg-green-500 text-white',
            warning: 'bg-yellow-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        return classes[type] || classes.info;
    },

    // Show modal
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    },

    // Hide modal
    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    },

    // Switch tab
    switchTab(tabButton) {
        const targetId = tabButton.dataset.target;
        const tabContainer = tabButton.closest('[data-tabs]');
        
        if (!tabContainer) return;

        // Remove active class from all tabs and panels
        tabContainer.querySelectorAll('[data-toggle="tab"]').forEach(btn => {
            btn.classList.remove('active');
        });
        tabContainer.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // Add active class to clicked tab and show target panel
        tabButton.classList.add('active');
        const targetPanel = document.getElementById(targetId);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
        }
    },

    // Update dashboard stats
    updateStats() {
        fetch('api/get-stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Object.keys(data.stats).forEach(key => {
                        const element = document.getElementById(`stat-${key}`);
                        if (element) {
                            element.textContent = data.stats[key];
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error updating stats:', error);
            });
    },

    // Utility functions
    utils: {
        // Format currency
        formatCurrency(amount) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(amount);
        },

        // Format date
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        // Truncate text
        truncateText(text, length = 100) {
            return text.length > length ? text.substring(0, length) + '...' : text;
        },

        // Debounce function
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.BarangayLink.init();
});

// Export for use in other scripts
window.BL = window.BarangayLink;