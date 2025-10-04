<?php
/**
 * Base Layout Template - PHP Version
 * Contains the basic HTML structure and CSS/JS includes
 */

// Security check removed for development - file is meant to be included
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_CONFIG['name']; ?></title>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="styles/globals.css">
    <link rel="stylesheet" href="styles/php-styles.css">
    
    <!-- Alpine.js for interactive components -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Custom JavaScript -->
    <script src="js/app.js" defer></script>
    
    <!-- Real-time updates -->
    <script>
        // Real-time clock update
        function updateClock() {
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
        }
        
        // Update clock every second
        setInterval(updateClock, 1000);
        
        // Initial update
        document.addEventListener('DOMContentLoaded', updateClock);
        
        // Auto-refresh notifications every 30 seconds
        setInterval(function() {
            const notificationElement = document.getElementById('notification-count');
            if (notificationElement) {
                fetch('api/get-notifications.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.unreadCount > 0) {
                            notificationElement.textContent = data.unreadCount;
                            notificationElement.style.display = 'block';
                        } else {
                            notificationElement.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }
        }, 30000);
    </script>
    
    <style>
        /* Loading animation */
        .loading-spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Hide elements by default for progressive enhancement */
        .js-only {
            display: none;
        }
        
        /* Show elements when JavaScript is enabled */
        .js .js-only {
            display: block;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
    <!-- Add class for JavaScript detection -->
    <script>document.body.parentElement.classList.add('js');</script>
    
    <!-- Global notification container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    <!-- Loading overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex flex-col items-center space-y-4">
            <div class="loading-spinner"></div>
            <p class="text-gray-600">Loading...</p>
        </div>
    </div>
    
    <!-- Main content will be inserted here -->
    <div id="main-content">
        <!-- Content will be loaded by individual pages -->
    </div>
    
    <!-- Common JavaScript functions -->
    <script>
        // Show loading overlay
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            document.getElementById('loading-overlay').classList.add('flex');
        }
        
        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
            document.getElementById('loading-overlay').classList.remove('flex');
        }
        
        // Show notification
        function showNotification(type, title, message) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            
            let bgColor = 'bg-blue-500';
            if (type === 'success') bgColor = 'bg-green-500';
            if (type === 'error') bgColor = 'bg-red-500';
            if (type === 'warning') bgColor = 'bg-yellow-500';
            
            notification.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg max-w-sm`;
            notification.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium">${title}</h4>
                        <p class="text-sm opacity-90">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Handle form submissions with loading states
        function submitForm(form, showLoadingState = true) {
            if (showLoadingState) {
                showLoading();
            }
            
            const formData = new FormData(form);
            
            fetch(form.action || window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response;
            })
            .then(() => {
                // Reload page or handle success
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error', 'An error occurred. Please try again.');
            })
            .finally(() => {
                if (showLoadingState) {
                    hideLoading();
                }
            });
        }
    </script>
</body>
</html><?php
// Don't output anything else from this file
exit;
?>