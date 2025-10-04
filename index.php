<?php
/**
 * BarangayLink Main Entry Point - PHP Version
 * EXACT 1:1 conversion from App.tsx - MATCHING EVERY DETAIL
 */

// Start session for form data handling
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Load core files
try {
    require_once ABSPATH . 'config.php';
    require_once ABSPATH . 'functions/utils.php';
    require_once ABSPATH . 'functions/auth.php';
} catch (Exception $e) {
    die('<div style="background:#fef2f2;color:#dc2626;padding:20px;margin:20px;border-radius:8px;font-family:system-ui;">
        <h2>🚨 Configuration Error</h2>
        <p><strong>Failed to load required files:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
    </div>');
}

// Initialize session
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (Exception $e) {
    error_log('Session error: ' . $e->getMessage());
}

// EXACT same state variables as App.tsx useState
$authState = $_SESSION['authState'] ?? ['isAuthenticated' => false, 'user' => null];
$currentView = $_SESSION['currentView'] ?? 'dashboard';
$showAuthModal = isset($_GET['auth']) && $_GET['auth'] === 'true';
$authMode = $_GET['mode'] ?? 'login';
$isMobileMenuOpen = false; // Will be handled by JavaScript
$isInitialized = true; // PHP doesn't need async initialization like React

// Handle URL parameters for view changes (matching onViewChange)
if (isset($_GET['view'])) {
    $currentView = $_GET['view'];
    $_SESSION['currentView'] = $currentView;
}

// ===== FORM PROCESSING - MUST HAPPEN BEFORE HTML OUTPUT =====
// This prevents "headers already sent" errors by processing redirects first

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $usersFile = __DIR__ . '/data/users.json';
    $users = json_decode(file_get_contents($usersFile), true) ?: [];
    
    // Update user data - improved logic
    $userUpdated = false;
    for ($i = 0; $i < count($users); $i++) {
        if ($users[$i]['email'] === $authState['user']['email']) {
            $users[$i]['name'] = $_POST['name'];
            $users[$i]['phone'] = $_POST['phone'] ?? '';
            $users[$i]['address'] = $_POST['address'] ?? '';
            $userUpdated = true;
            break;
        }
    }
    
    // Only proceed if user was found and updated
    if ($userUpdated) {
        // Save updated users back to file
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        
        // Update session auth state with the new data
        $authState['user']['name'] = $_POST['name'];
        $authState['user']['phone'] = $_POST['phone'] ?? '';
        $authState['user']['address'] = $_POST['address'] ?? '';
        $_SESSION['authState'] = $authState;
    }
    
    // Set success message and redirect
    $_SESSION['profile_saved'] = true;
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Document Request Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_document_request') {
    $requestsFile = __DIR__ . '/data/requests.json';
    $requests = json_decode(file_get_contents($requestsFile), true) ?: [];
    
    // Create new document request
    $newRequest = [
        'id' => 'req_' . time() . '_' . rand(100, 999),
        'documentType' => $_POST['documentType'],
        'purpose' => $_POST['purpose'],
        'quantity' => intval($_POST['quantity'] ?? 1),
        'notes' => $_POST['notes'] ?? '',
        'requestedBy' => $_POST['requestedBy'],
        'requestedByEmail' => $_POST['requestedByEmail'],
        'status' => 'pending',
        'createdAt' => date('c'),
        'estimatedFee' => 0,
        'processingTime' => '1-2 business days'
    ];
    
    // Add to requests array
    $requests[] = $newRequest;
    
    // Save back to file
    file_put_contents($requestsFile, json_encode($requests, JSON_PRETTY_PRINT));
    
    // Set success message and redirect
    $_SESSION['doc_submitted'] = true;
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Concern Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_concern') {
    $concernsFile = __DIR__ . '/data/concerns.json';
    $concerns = json_decode(file_get_contents($concernsFile), true) ?: [];
    
    // Create new concern
    $newConcern = [
        'id' => 'con_' . time() . '_' . rand(100, 999),
        'subject' => $_POST['subject'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'location' => $_POST['location'] ?? '',
        'priority' => 'medium',
        'submittedBy' => $_POST['submittedBy'],
        'submittedByEmail' => $_POST['submittedByEmail'],
        'status' => 'pending',
        'createdAt' => date('c')
    ];
    
    // Add to concerns array
    $concerns[] = $newConcern;
    
    // Save back to file
    file_put_contents($concernsFile, json_encode($concerns, JSON_PRETTY_PRINT));
    
    // Set success message and redirect
    $_SESSION['concern_submitted'] = true;
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Admin Document Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $documentsFile = __DIR__ . '/data/requests.json';
    $documents = json_decode(file_get_contents($documentsFile), true) ?: [];
    
    foreach ($documents as &$doc) {
        if ($doc['id'] === $_POST['doc_id']) {
            $doc['status'] = $_POST['new_status'];
            $doc['updatedAt'] = date('c');
            break;
        }
    }
    
    file_put_contents($documentsFile, json_encode($documents, JSON_PRETTY_PRINT));
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Admin Concern Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_concern_status'])) {
    $concernsFile = __DIR__ . '/data/concerns.json';
    $concerns = json_decode(file_get_contents($concernsFile), true) ?: [];
    
    foreach ($concerns as &$concern) {
        if ($concern['id'] === $_POST['concern_id']) {
            $concern['status'] = $_POST['new_status'];
            $concern['updatedAt'] = date('c');
            break;
        }
    }
    
    file_put_contents($concernsFile, json_encode($concerns, JSON_PRETTY_PRINT));
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Admin User Management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $usersFile = __DIR__ . '/data/users.json';
    $users = json_decode(file_get_contents($usersFile), true) ?: [];
    
    $isEdit = !empty($_POST['original_email']);
    
    $userData = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'role' => $_POST['role'],
        'address' => $_POST['address'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];
    
    if (!$isEdit) {
        // Add password for new users
        $userData['password'] = $_POST['password'];
        $userData['createdAt'] = date('c');
        $users[] = $userData;
    } else {
        // Update existing user
        $originalEmail = $_POST['original_email'];
        foreach ($users as &$user) {
            if ($user['email'] === $originalEmail) {
                $user = array_merge($user, $userData);
                break;
            }
        }
    }
    
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    header('Location: ?view=manage-users&selected=' . urlencode($userData['email']));
    exit;
}

// Handle User Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $usersFile = __DIR__ . '/data/users.json';
    $users = json_decode(file_get_contents($usersFile), true) ?: [];
    
    $emailToDelete = $_POST['user_email'];
    $users = array_filter($users, fn($u) => $u['email'] !== $emailToDelete);
    $users = array_values($users); // Re-index array
    
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    header('Location: ?view=manage-users');
    exit;
}

// ===== END FORM PROCESSING =====

// EXACT same localStorage helpers as App.tsx
function getStorageItem($key, $defaultValue = null) {
    // In PHP, we use session instead of localStorage
    return $_SESSION[$key] ?? $defaultValue;
}

function setStorageItem($key, $value) {
    try {
        $_SESSION[$key] = $value;
        return true;
    } catch (Exception $e) {
        error_log("Error setting session key \"$key\": " . $e->getMessage());
        return false;
    }
}

function removeStorageItem($key) {
    try {
        unset($_SESSION[$key]);
        return true;
    } catch (Exception $e) {
        error_log("Error removing session key \"$key\": " . $e->getMessage());
        return false;
    }
}

// Initialize app state (matching App.tsx useEffect)
try {
    $savedAuth = getStorageItem(STORAGE_KEYS['auth']);
    if ($savedAuth && $savedAuth['isAuthenticated'] && $savedAuth['user']) {
        $authState = $savedAuth;
    }
} catch (Exception $e) {
    error_log('Error loading saved auth: ' . $e->getMessage());
}

// Handle logout (matching handleLogout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $authState = ['isAuthenticated' => false, 'user' => null];
    $_SESSION['authState'] = $authState;
    $currentView = 'dashboard';
    $_SESSION['currentView'] = $currentView;
    $isMobileMenuOpen = false;
    removeStorageItem(STORAGE_KEYS['auth']);
    header('Location: index.php');
    exit;
}

// Handle form submissions (matching handleLogin, handleSignup, handlePasswordReset)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];
    
    try {
        switch ($action) {
            case 'login':
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                // Admin login (matching ADMIN_CREDENTIALS check)
                if ($email === ADMIN_CREDENTIALS['email'] && $password === ADMIN_CREDENTIALS['password']) {
                    $authState = [
                        'isAuthenticated' => true,
                        'user' => [
                            'email' => ADMIN_CREDENTIALS['email'],
                            'name' => 'Admin User',
                            'role' => 'admin'
                        ]
                    ];
                    $_SESSION['authState'] = $authState;
                    setStorageItem(STORAGE_KEYS['auth'], $authState);
                    $currentView = 'admin-dashboard';
                    $_SESSION['currentView'] = $currentView;
                    $response = ['success' => true, 'redirect' => 'index.php'];
                } else {
                    // Regular user login (matching user lookup)
                    $users = getStorageItem(STORAGE_KEYS['users'], []);
                    if (empty($users)) {
                        $users = loadJsonData('users');
                    }
                    
                    $foundUser = null;
                    foreach ($users as $u) {
                        if ($u['email'] === $email && $u['password'] === $password) {
                            $foundUser = $u;
                            break;
                        }
                    }
                    
                    if ($foundUser) {
                        $authState = [
                            'isAuthenticated' => true,
                            'user' => [
                                'email' => $foundUser['email'],
                                'name' => $foundUser['name'],
                                'role' => 'user',
                                'address' => $foundUser['address'] ?? '',
                                'phone' => $foundUser['phone'] ?? ''
                            ]
                        ];
                        $_SESSION['authState'] = $authState;
                        setStorageItem(STORAGE_KEYS['auth'], $authState);
                        $currentView = 'dashboard';
                        $_SESSION['currentView'] = $currentView;
                        $response = ['success' => true, 'redirect' => 'index.php'];
                    } else {
                        $response = ['success' => false, 'message' => 'Invalid email or password'];
                    }
                }
                break;
                
            case 'signup':
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $address = $_POST['address'] ?? '';
                $phone = $_POST['phone'] ?? '';
                
                if (empty($name) || empty($email) || empty($password)) {
                    $response = ['success' => false, 'message' => 'Please fill in all required fields'];
                    break;
                }
                
                $users = getStorageItem(STORAGE_KEYS['users'], []);
                if (empty($users)) {
                    $users = loadJsonData('users');
                }
                
                // Check if user exists
                $userExists = false;
                foreach ($users as $u) {
                    if ($u['email'] === $email) {
                        $userExists = true;
                        break;
                    }
                }
                
                if ($userExists) {
                    $response = ['success' => false, 'message' => 'User with this email already exists'];
                } else {
                    $newUser = [
                        'email' => $email,
                        'password' => $password,
                        'name' => $name,
                        'role' => 'user',
                        'address' => $address,
                        'phone' => $phone,
                        'createdAt' => date('c')
                    ];
                    
                    $users[] = $newUser;
                    setStorageItem(STORAGE_KEYS['users'], $users);
                    saveJsonData('users', $users);
                    
                    $authState = [
                        'isAuthenticated' => true,
                        'user' => [
                            'email' => $newUser['email'],
                            'name' => $newUser['name'],
                            'role' => 'user',
                            'address' => $newUser['address'],
                            'phone' => $newUser['phone']
                        ]
                    ];
                    $_SESSION['authState'] = $authState;
                    setStorageItem(STORAGE_KEYS['auth'], $authState);
                    $currentView = 'dashboard';
                    $_SESSION['currentView'] = $currentView;
                    $response = ['success' => true, 'redirect' => 'index.php'];
                }
                break;
                
            case 'reset':
                $email = $_POST['email'] ?? '';
                $response = ['success' => true, 'message' => 'Password reset link sent to ' . $email];
                break;

            // Handle other form submissions
            default:
                // Include other form handlers
                if (file_exists(ABSPATH . 'functions/concerns.php')) {
                    include_once ABSPATH . 'functions/concerns.php';
                }
                if (file_exists(ABSPATH . 'functions/documents.php')) {
                    include_once ABSPATH . 'functions/documents.php';
                }
                if (file_exists(ABSPATH . 'functions/profile.php')) {
                    include_once ABSPATH . 'functions/profile.php';
                }
                
                switch ($action) {
                    case 'submit_concern':
                        if (function_exists('handleSubmitConcern')) {
                            $response = handleSubmitConcern($_POST, $authState['user']);
                        }
                        break;
                    case 'submit_document_request':
                        if (function_exists('handleSubmitDocumentRequest')) {
                            $response = handleSubmitDocumentRequest($_POST, $authState['user']);
                        }
                        break;
                    case 'update_profile':
                        if (function_exists('handleUpdateProfile')) {
                            $response = handleUpdateProfile($_POST, $authState['user']);
                            if ($response['success']) {
                                // Update session with new user data
                                $authState['user'] = array_merge($authState['user'], [
                                    'name' => $_POST['name'] ?? $authState['user']['name'],
                                    'phone' => $_POST['phone'] ?? $authState['user']['phone'],
                                    'address' => $_POST['address'] ?? $authState['user']['address']
                                ]);
                                $_SESSION['authState'] = $authState;
                                setStorageItem(STORAGE_KEYS['auth'], $authState);
                            }
                        }
                        break;
                }
                break;
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
    
    // Handle AJAX responses
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else if ($response['success'] && isset($response['redirect'])) {
        header('Location: ' . $response['redirect']);
        exit;
    } else if (!$response['success']) {
        $showAuthModal = true;
        $authMode = $_POST['authMode'] ?? 'login';
    }
}

// Save auth state when it changes (matching App.tsx useEffect)
if ($authState['isAuthenticated']) {
    setStorageItem(STORAGE_KEYS['auth'], $authState);
} else {
    removeStorageItem(STORAGE_KEYS['auth']);
}

// Props for layouts (matching App.tsx props)
function getLayoutProps() {
    global $authState, $currentView, $isMobileMenuOpen;
    
    return [
        'user' => $authState['user'],
        'currentView' => $currentView,
        'onViewChange' => function($view) {
            global $currentView;
            $currentView = $view;
            $_SESSION['currentView'] = $view;
        },
        'onLogout' => function() {
            global $authState, $currentView, $isMobileMenuOpen;
            $authState = ['isAuthenticated' => false, 'user' => null];
            $_SESSION['authState'] = $authState;
            $currentView = 'dashboard';
            $_SESSION['currentView'] = $currentView;
            $isMobileMenuOpen = false;
            removeStorageItem(STORAGE_KEYS['auth']);
        },
        'isMobileMenuOpen' => $isMobileMenuOpen,
        'setIsMobileMenuOpen' => function($isOpen) {
            global $isMobileMenuOpen;
            $isMobileMenuOpen = $isOpen;
        }
    ];
}

?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_CONFIG['name']; ?> - Digital Governance Platform</title>
    
    <!-- EXACT same imports as App.tsx -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- EXACT same globals.css as TSX version -->
    <link href="styles/globals.css" rel="stylesheet">
    
    <script>
        // Global app state (matching App.tsx useState)
        window.AppState = {
            authState: <?php echo json_encode($authState); ?>,
            currentView: <?php echo json_encode($currentView); ?>,
            showAuthModal: <?php echo json_encode($showAuthModal); ?>,
            authMode: <?php echo json_encode($authMode); ?>,
            isMobileMenuOpen: <?php echo json_encode($isMobileMenuOpen); ?>,
            isInitialized: <?php echo json_encode($isInitialized); ?>
        };

        // EXACT same localStorage helpers as App.tsx
        const getStorageItem = (key, defaultValue = null) => {
            try {
                const item = localStorage.getItem(key);
                return item ? JSON.parse(item) : defaultValue;
            } catch (error) {
                console.warn(`Error reading localStorage key "${key}":`, error);
                return defaultValue;
            }
        };

        const setStorageItem = (key, value) => {
            try {
                localStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (error) {
                console.warn(`Error setting localStorage key "${key}":`, error);
                return false;
            }
        };

        const removeStorageItem = (key) => {
            try {
                localStorage.removeItem(key);
                return true;
            } catch (error) {
                console.warn(`Error removing localStorage key "${key}":`, error);
                return false;
            }
        };

        // Event handlers (matching App.tsx callbacks)
        const handleLogin = (email, password) => {
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);
            formData.append('ajax', 'true');
            
            return fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || 'index.php';
                    return true;
                }
                return false;
            });
        };

        const handleSignup = (name, email, password, address = '', phone = '') => {
            const formData = new FormData();
            formData.append('action', 'signup');
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('address', address);
            formData.append('phone', phone);
            formData.append('ajax', 'true');
            
            return fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || 'index.php';
                    return true;
                }
                return false;
            });
        };

        const handleLogout = () => {
            window.location.href = '?action=logout';
        };

        const openAuthModal = (mode) => {
            window.location.href = `?auth=true&mode=${mode}`;
        };

        const handlePasswordReset = (email) => {
            alert(`Password reset link sent to ${email}`);
            window.location.href = 'index.php';
            return true;
        };

        const onViewChange = (view) => {
            window.location.href = `?view=${view}`;
        };

        const setIsMobileMenuOpen = (isOpen) => {
            window.AppState.isMobileMenuOpen = isOpen;
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            if (sidebar && overlay) {
                if (isOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                }
            }
        };

        // Initialize (matching App.tsx useEffect)
        document.addEventListener('DOMContentLoaded', function() {
            // Sync with localStorage (matching App.tsx initialization)
            if (window.AppState.authState.isAuthenticated) {
                setStorageItem('barangaylink_auth', window.AppState.authState);
            }

            // Form submission handlers
            document.querySelectorAll('form[data-ajax]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    formData.append('ajax', 'true');
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalHTML = submitBtn.innerHTML;
                        
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center space-x-2">
                                <div class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                <span>Processing...</span>
                            </div>
                        `;
                        
                        fetch('', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                alert(data.message || 'An error occurred');
                            }
                        })
                        .catch(error => {
                            alert('Network error occurred');
                            console.error('Form submission error:', error);
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHTML;
                        });
                    }
                });
            });
        });
    </script>
</head>
<body class="h-full bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">

<?php
// EXACT same conditional rendering as App.tsx
try {
    // Show loading until initialized (matching !isInitialized check)
    if (!$isInitialized) {
        // LoadingFallback component
        echo '<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex items-center justify-center">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-slate-800 font-semibold">Loading BarangayLink...</p>
            </div>
        </div>';
        exit;
    }

    // Not authenticated - show landing page (matching !authState.isAuthenticated)
    if (!$authState['isAuthenticated']) {
        // Suspense fallback for LandingPage
        include ABSPATH . 'components/LandingPage.php';
        echo LandingPage();
        
        // AuthModal if showAuthModal is true (matching {showAuthModal && <AuthModal />})
        if ($showAuthModal) {
            include ABSPATH . 'components/AuthModal.php';
            echo AuthModal($authMode, $_GET['error'] ?? null);
        }
    }
    // Admin user interface (matching authState.user?.role === 'admin')
    elseif ($authState['user'] && $authState['user']['role'] === 'admin') {
        $layoutProps = getLayoutProps();
        
        // Suspense fallback for AdminLayout
        include ABSPATH . 'components/layouts/AdminLayout.php';
        
        // Children components (matching Suspense with component switches)
        $children = '';
        ob_start();
        
        echo '<div class="animate-fade-in">';  // Suspense fallback equivalent
        
        // Match exact component switching from App.tsx
        switch ($currentView) {
            case 'admin-dashboard':
                include ABSPATH . 'components/AdminDashboard.php';
                echo AdminDashboard();
                break;
            case 'manage-documents':
                include ABSPATH . 'components/AdminDocumentManagement.php';
                echo AdminDocumentManagement();
                break;
            case 'manage-concerns':
                include ABSPATH . 'components/AdminConcernManagement.php';
                echo AdminConcernManagement();
                break;
            case 'manage-users':
                include ABSPATH . 'components/AdminUserManagement.php';
                echo AdminUserManagement();
                break;
            default:
                include ABSPATH . 'components/AdminDashboard.php';
                echo AdminDashboard();
                break;
        }
        
        echo '</div>';
        $children = ob_get_clean();
        
        echo AdminLayout($layoutProps['user'], $layoutProps['currentView'], $children);
    }
    // Regular user interface (matching else case)
    else {
        $layoutProps = getLayoutProps();
        
        // Suspense fallback for UserLayout
        include ABSPATH . 'components/layouts/UserLayout.php';
        
        // Children components (matching Suspense with component switches)
        $children = '';
        ob_start();
        
        echo '<div class="animate-fade-in">';  // Suspense fallback equivalent
        
        // Match exact component switching from App.tsx
        switch ($currentView) {
            case 'dashboard':
                include ABSPATH . 'components/UserDashboard.php';
                echo UserDashboard($layoutProps['user']);
                break;
            case 'profile':
                include ABSPATH . 'components/ProfileManagement.php';
                // Ensure we pass the most current user data from session
                $currentUser = $_SESSION['authState']['user'] ?? $layoutProps['user'];
                echo ProfileManagement($currentUser);
                break;
            case 'document-request':
                include ABSPATH . 'components/DocumentRequestForm.php';
                echo DocumentRequestForm($layoutProps['user']);
                break;
            case 'submit-concern':
                include ABSPATH . 'components/SubmitConcernForm.php';
                echo SubmitConcernForm($layoutProps['user']);
                break;
            case 'community-directory':
                include ABSPATH . 'components/CommunityDirectory.php';
                echo CommunityDirectory();
                break;
            case 'emergency-alerts':
                include ABSPATH . 'components/EmergencyAlerts.php';
                echo EmergencyAlerts();
                break;
            case 'information-hub':
                include ABSPATH . 'components/InformationHub.php';
                echo InformationHub();
                break;
            default:
                include ABSPATH . 'components/UserDashboard.php';
                echo UserDashboard($layoutProps['user']);
                break;
        }
        
        echo '</div>';
        $children = ob_get_clean();
        
        echo UserLayout($layoutProps['user'], $layoutProps['currentView'], $children);
    }
    
} catch (Exception $e) {
    // Error boundary (matching App.tsx error handling)
    echo '<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md mx-4">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800">Application Error</h2>
            </div>
            <p class="text-slate-600 mb-6">
                ' . htmlspecialchars($e->getMessage()) . '
            </p>
            <div class="space-y-2">
                <button onclick="window.location.reload()" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Refresh Page
                </button>
            </div>
        </div>
    </div>';
}
?>

</body>
</html>