    <!-- Global Scripts -->
    <script>
        // Global configuration - SYNCHRONIZED with config.ts
        const APP_CONFIG = {
            name: 'BarangayLink',
            version: '1.0.0',
            apiBaseUrl: '/api',
            csrfToken: '<?php echo generateCSRFToken(); ?>'
        };
        
        // Helper function for API calls with CSRF protection
        async function apiCall(endpoint, method = 'GET', data = null) {
            try {
                const config = {
                    method: method,
                    url: `${APP_CONFIG.apiBaseUrl}/${endpoint}`,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': APP_CONFIG.csrfToken
                    }
                };
                
                if (data && (method === 'POST' || method === 'PUT')) {
                    config.data = data;
                }
                
                const response = await axios(config);
                return response.data;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }
        
        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' :
                'bg-blue-500'
            } text-white max-w-md`;
            toast.textContent = message;
            toast.style.transform = 'translateX(400px)';
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 10);
            
            setTimeout(() => {
                toast.style.transform = 'translateX(400px)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
        
        // Format date helper
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
        
        // Format datetime helper
        function formatDateTime(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
        
        // Time ago helper
        function timeAgo(dateString) {
            const seconds = Math.floor((new Date() - new Date(dateString)) / 1000);
            
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " year" + (Math.floor(interval) > 1 ? "s" : "") + " ago";
            
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " month" + (Math.floor(interval) > 1 ? "s" : "") + " ago";
            
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " day" + (Math.floor(interval) > 1 ? "s" : "") + " ago";
            
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " hour" + (Math.floor(interval) > 1 ? "s" : "") + " ago";
            
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minute" + (Math.floor(interval) > 1 ? "s" : "") + " ago";
            
            return Math.floor(seconds) + " second" + (Math.floor(seconds) > 1 ? "s" : "") + " ago";
        }
        
        // Auto-refresh for real-time data
        function startAutoRefresh(callback, interval = 30000) {
            callback(); // Initial call
            return setInterval(callback, interval);
        }
        
        // Stop auto-refresh
        function stopAutoRefresh(intervalId) {
            if (intervalId) {
                clearInterval(intervalId);
            }
        }
    </script>
</body>
</html>
