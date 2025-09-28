    <!-- JavaScript -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Toast notification system
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${type === 'success' ? '<i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>' : '<i data-lucide="x-circle" class="w-5 h-5 text-red-500"></i>'}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button type="button" class="inline-flex text-gray-400 hover:text-gray-500" onclick="this.closest('.toast').remove()">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            lucide.createIcons();
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }
        
        // Modal system
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                const modal = event.target.closest('[id*="modal"]');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            }
        });
        
        // AJAX helper
        async function makeRequest(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };
            
            const config = { ...defaultOptions, ...options };
            
            // Add CSRF token to POST requests
            if (config.method === 'POST' && config.body) {
                const body = JSON.parse(config.body);
                body.csrf_token = '<?php echo $csrfToken; ?>';
                config.body = JSON.stringify(body);
            }
            
            try {
                const response = await fetch(url, config);
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Request failed');
                }
                
                return data;
            } catch (error) {
                console.error('Request error:', error);
                throw error;
            }
        }
        
        // Form submission helper
        function submitForm(formElement, callback) {
            const formData = new FormData(formElement);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            const submitButton = formElement.querySelector('[type="submit"]');
            const originalText = submitButton.textContent;
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="loading-spinner"></span> Processing...';
            
            makeRequest(formElement.action, {
                method: 'POST',
                body: JSON.stringify(data)
            })
            .then(response => {
                if (callback) {
                    callback(response);
                } else {
                    showToast(response.message);
                    location.reload();
                }
            })
            .catch(error => {
                showToast(error.message, 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        }
        
        // Confirmation dialog
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
        
        // Auto-refresh for real-time updates
        function startAutoRefresh(interval = 30000) {
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    location.reload();
                }
            }, interval);
        }
        
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltips = document.querySelectorAll('[data-tooltip]');
            tooltips.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'absolute z-50 px-2 py-1 text-sm bg-gray-900 text-white rounded shadow-lg';
                    tooltip.textContent = this.getAttribute('data-tooltip');
                    tooltip.style.top = this.getBoundingClientRect().bottom + 5 + 'px';
                    tooltip.style.left = this.getBoundingClientRect().left + 'px';
                    tooltip.id = 'tooltip-' + Date.now();
                    document.body.appendChild(tooltip);
                    
                    this.addEventListener('mouseleave', function() {
                        tooltip.remove();
                    });
                });
            });
        });
        
        // Handle flash messages
        <?php if (isset($_SESSION['flash_message'])): ?>
            showToast('<?php echo $_SESSION['flash_message']['message']; ?>', '<?php echo $_SESSION['flash_message']['type']; ?>');
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
    </script>
</body>
</html>