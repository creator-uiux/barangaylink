<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Get current user
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header('Location: App.php');
    exit;
}

// Check for remember me cookie
if (!isLoggedIn() && isset($_COOKIE['remember_user'])) {
    $user_key = $_COOKIE['remember_user'];
    include_once 'components/AuthModal.php';
    $users = getUserData();
    
    if (isset($users[$user_key])) {
        $_SESSION['user'] = [
            'email' => $users[$user_key]['email'],
            'fullName' => $users[$user_key]['fullName'],
            'role' => $users[$user_key]['role'],
            'id' => $users[$user_key]['id']
        ];
    }
}

$user = getCurrentUser();
$currentPage = isset($_GET['page']) ? $_GET['page'] : (isLoggedIn() ? 'dashboard' : 'landing');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarangayLink - Your Direct Link to Local Updates and Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/globals.css">
    <style>
        /* Loading spinner */
        .spinner {
            border: 2px solid #f3f4f6;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Smooth transitions */
        .page-transition {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
        }
        
        .page-transition.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Toast notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease-in-out;
        }
        
        .toast.show {
            transform: translateX(0);
        }
        
        .toast.success {
            border-left: 4px solid #10b981;
        }
        
        .toast.error {
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body class="size-full bg-background text-foreground">
    <!-- Loading spinner -->
    <div id="loading" class="fixed inset-0 bg-white z-50 flex items-center justify-center hidden">
        <div class="spinner"></div>
    </div>
    
    <!-- Main Content -->
    <div class="size-full page-transition active">
        <?php
        if ($currentPage === 'landing' || !isLoggedIn()) {
            include_once 'components/LandingPage.php';
            renderLandingPage();
        } elseif ($user['role'] === 'admin') {
            include_once 'components/AdminDashboard.php';
            renderAdminDashboard();
        } else {
            include_once 'components/Dashboard.php';
            renderDashboard();
        }
        ?>
    </div>
    
    <!-- Toast Container -->
    <div id="toast-container"></div>
    
    <script>
        // Show loading spinner
        function showLoading() {
            document.getElementById('loading').classList.remove('hidden');
        }
        
        // Hide loading spinner
        function hideLoading() {
            document.getElementById('loading').classList.add('hidden');
        }
        
        // Navigate to different sections
        function navigateTo(page) {
            showLoading();
            setTimeout(() => {
                window.location.href = 'App.php?page=' + page;
            }, 500);
        }
        
        // Navigate to dashboard
        function navigateToDashboard() {
            navigateTo('dashboard');
        }
        
        // Navigate to landing
        function navigateToLanding() {
            navigateTo('landing');
        }
        
        // Logout function
        function logout() {
            showLoading();
            setTimeout(() => {
                window.location.href = 'App.php?logout=1';
            }, 500);
        }
        
        // Toast notification system
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            toast.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-3">
                            ${type === 'success' ? 
                                '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                                '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                            }
                        </div>
                        <span class="text-sm font-medium text-gray-900">${message}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
        
        // Page transition effect
        document.addEventListener('DOMContentLoaded', function() {
            hideLoading();
            
            // Check for URL parameters to show toasts
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            const type = urlParams.get('type') || 'success';
            
            if (message) {
                showToast(decodeURIComponent(message), type);
                
                // Clean URL
                const url = new URL(window.location);
                url.searchParams.delete('message');
                url.searchParams.delete('type');
                window.history.replaceState({}, document.title, url);
            }
        });
        
        // Handle form submissions with loading states
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.tagName === 'FORM') {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';
                    
                    // Re-enable after 5 seconds as fallback
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }, 5000);
                }
            }
        });
        
        // Smooth scroll for anchor links
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.getAttribute('href')?.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
        
        <?php if (isLoggedIn()): ?>
        // Auto-logout after inactivity (30 minutes)
        let inactivityTimer;
        
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                showToast('Session expired due to inactivity', 'error');
                setTimeout(() => {
                    logout();
                }, 2000);
            }, 30 * 60 * 1000); // 30 minutes
        }
        
        // Reset timer on user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetInactivityTimer, true);
        });
        
        // Initialize timer
        resetInactivityTimer();
        <?php endif; ?>
    </script>
</body>
</html>