    <?php
/**
 * BarangayLink Core Application Class
 * Enhanced version matching React App.tsx functionality
 */

class BarangayLinkApp {
    private $authState;
    private $currentView;
    private $isInitializing;
    private $config;
    
    public function __construct() {
        $this->isInitializing = true;
        $this->authState = [
            'isAuthenticated' => false,
            'user' => null
        ];
        $this->currentView = 'dashboard';
        $this->config = require __DIR__ . '/config.php';
        
        $this->initializeApp();
    }
    
    /**
     * Initialize the application with enhanced error handling
     */
    private function initializeApp() {
        try {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Small delay to prevent flash (matching React behavior)
            usleep(100000); // 100ms
            
            // Load saved authentication state
            if (isset($_SESSION['user']) && $_SESSION['user']) {
                $this->authState = [
                    'isAuthenticated' => true,
                    'user' => $_SESSION['user']
                ];
            }
            
            // Set current view based on user role and URL parameters
            $this->currentView = $this->determineCurrentView();
            
        } catch (Exception $e) {
            error_log('Failed to initialize BarangayLink app: ' . $e->getMessage());
            throw new Exception('Application initialization failed');
        } finally {
            $this->isInitializing = false;
        }
    }
    
    /**
     * Determine current view based on user role and URL parameters
     */
    private function determineCurrentView() {
        $view = $_GET['view'] ?? null;
        
        if (!$view) {
            // Default views based on role
            if ($this->authState['isAuthenticated']) {
                return $this->authState['user']['role'] === 'admin' ? 'admin-dashboard' : 'dashboard';
            }
            return 'landing';
        }
        
        return $view;
    }
    
    /**
     * Handle login requests with enhanced validation
     */
    public function handleLogin($email, $password) {
        try {
            // Admin credentials check
            if ($email === $this->config['admin']['email'] && 
                $password === $this->config['admin']['password']) {
                
                $this->authState = [
                    'isAuthenticated' => true,
                    'user' => [
                        'email' => $this->config['admin']['email'],
                        'name' => 'Admin User',
                        'role' => 'admin'
                    ]
                ];
                
                $_SESSION['user'] = $this->authState['user'];
                $this->currentView = 'admin-dashboard';
                return ['success' => true, 'redirect' => 'admin-dashboard'];
            }
            
            // Regular user authentication - load all users to check status
            $users = $this->loadUsers(true); // Include deleted users for status checking
            $user = array_filter($users, function($u) use ($email, $password) {
                return $u['email'] === $email && $u['password'] === $password;
            });
            
            if ($user) {
                $user = reset($user); // Get first match
                
                // Check user status
                $userStatus = $user['status'] ?? 'active';
                if ($userStatus === 'deleted') {
                    return ['success' => false, 'message' => 'Your account has been deleted. Please contact the administrator for assistance.'];
                } elseif ($userStatus === 'suspended') {
                    return ['success' => false, 'message' => 'Your account has been suspended. Please contact the administrator for assistance.'];
                } elseif ($userStatus === 'inactive') {
                    return ['success' => false, 'message' => 'Your account is inactive. Please contact the administrator to reactivate your account.'];
                }
                
                $this->authState = [
                    'isAuthenticated' => true,
                    'user' => [
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'role' => 'user',
                        'address' => $user['address'] ?? '',
                        'phone' => $user['phone'] ?? ''
                    ]
                ];
                
                $_SESSION['user'] = $this->authState['user'];
                $this->currentView = 'dashboard';
                return ['success' => true, 'redirect' => 'dashboard'];
            }
            
            return ['success' => false, 'message' => 'Invalid credentials'];
            
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed due to server error'];
        }
    }
    
    /**
     * Handle signup requests with enhanced validation
     */
    public function handleSignup($first_name, $last_name, $email, $password, $address = '', $phone = '', $middle_name = '') {
        try {
            // Validate required fields
            if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($address) || empty($phone)) {
                return ['success' => false, 'message' => 'All fields are required including address and phone number'];
            }
            
            $users = $this->loadUsers();
            
            // Check for existing user
            if (array_filter($users, function($u) use ($email) {
                return $u['email'] === $email;
            })) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Create new user
            $newUser = [
                'email' => $email,
                'password' => $password, // In production, hash this!
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'name' => trim($first_name . ' ' . $middle_name . ' ' . $last_name), // For backward compatibility
                'role' => 'user',
                'status' => 'active', // Set default status
                'address' => $address,
                'phone' => $phone,
                'createdAt' => date('c')
            ];
            
            $users[] = $newUser;
            $this->saveUsers($users);
            
            // Auto-login the new user
            $this->authState = [
                'isAuthenticated' => true,
                'user' => [
                    'email' => $newUser['email'],
                    'first_name' => $newUser['first_name'],
                    'middle_name' => $newUser['middle_name'],
                    'last_name' => $newUser['last_name'],
                    'name' => $newUser['name'],
                    'role' => 'user',
                    'address' => $newUser['address'],
                    'phone' => $newUser['phone']
                ]
            ];
            
            $_SESSION['user'] = $this->authState['user'];
            $this->currentView = 'dashboard';
            return ['success' => true, 'redirect' => 'dashboard'];
            
        } catch (Exception $e) {
            error_log('Signup error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Signup failed due to server error'];
        }
    }
    
    /**
     * Handle logout requests
     */
    public function handleLogout() {
        try {
            $this->authState = [
                'isAuthenticated' => false,
                'user' => null
            ];
            
            $_SESSION['user'] = null;
            unset($_SESSION['user']);
            $this->currentView = 'landing';
            
            return ['success' => true, 'redirect' => 'index.php'];
            
        } catch (Exception $e) {
            error_log('Logout error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Logout failed'];
        }
    }
    
    /**
     * Render the appropriate content based on current state
     */
    public function renderContent() {
        try {
            if ($this->isInitializing) {
                return $this->renderInitialLoading();
            }
            
            if (!$this->authState['isAuthenticated']) {
                return $this->renderUnauthenticatedContent();
            }
            
            if ($this->authState['user']['role'] === 'admin') {
                return $this->renderAdminContent();
            }
            
            return $this->renderUserContent();
            
        } catch (Exception $e) {
            error_log('Render error: ' . $e->getMessage());
            return $this->renderErrorFallback($e);
        }
    }
    
    /**
     * Render initial loading screen
     */
    private function renderInitialLoading() {
        return '
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex items-center justify-center">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-slate-600 font-medium">Initializing BarangayLink...</p>
            </div>
        </div>';
    }
    
    /**
     * Render content for unauthenticated users
     */
    private function renderUnauthenticatedContent() {
        require_once __DIR__ . '/components/LandingPage.php';
        $content = LandingPage();
        
        // Add auth modal if requested
        if (isset($_GET['auth']) && $_GET['auth'] === 'true') {
            require_once __DIR__ . '/components/AuthModal.php';
            $content .= AuthModal($_GET['mode'] ?? 'login');
        }
        
        return $content;
    }
    
    /**
     * Render content for admin users
     */
    private function renderAdminContent() {
        require_once __DIR__ . '/components/layouts/AdminLayout.php';
        
        // Determine which admin component to load
        $component = '';
        switch ($this->currentView) {
            case 'admin-dashboard':
                require_once __DIR__ . '/components/AdminDashboard.php';
                $component = AdminDashboard();
                break;
            case 'manage-documents':
                require_once __DIR__ . '/components/AdminDocumentManagement.php';
                $component = AdminDocumentManagement();
                break;
            case 'manage-concerns':
                require_once __DIR__ . '/components/AdminConcernManagement.php';
                $component = AdminConcernManagement();
                break;
            case 'manage-users':
                require_once __DIR__ . '/components/AdminUserManagement.php';
                $component = AdminUserManagement();
                break;
            default:
                require_once __DIR__ . '/components/AdminDashboard.php';
                $component = AdminDashboard();
        }
        
        return AdminLayout($this->authState['user'], $this->currentView, false, $component);
    }
    
    /**
     * Render content for regular users
     */
    private function renderUserContent() {
        require_once __DIR__ . '/components/layouts/UserLayout.php';
        
        // Determine which user component to load
        $component = '';
        switch ($this->currentView) {
            case 'dashboard':
                require_once __DIR__ . '/components/UserDashboard.php';
                $component = UserDashboard($this->authState['user']);
                break;
            case 'profile':
                require_once __DIR__ . '/components/ProfileManagement.php';
                $component = ProfileManagement($this->authState['user']);
                break;
            case 'document-request':
                require_once __DIR__ . '/components/DocumentRequestForm.php';
                $component = DocumentRequestForm($this->authState['user']);
                break;
            case 'submit-concern':
                require_once __DIR__ . '/components/SubmitConcernForm.php';
                $component = SubmitConcernForm($this->authState['user']);
                break;
            case 'community-directory':
                require_once __DIR__ . '/components/CommunityDirectory.php';
                $component = CommunityDirectory();
                break;
            case 'emergency-alerts':
                require_once __DIR__ . '/components/EmergencyAlerts.php';
                $component = EmergencyAlerts();
                break;
            case 'information-hub':
                require_once __DIR__ . '/components/InformationHub.php';
                $component = InformationHub();
                break;
            default:
                require_once __DIR__ . '/components/UserDashboard.php';
                $component = UserDashboard($this->authState['user']);
        }
        
        return UserLayout($this->authState['user'], $this->currentView, false, $component);
    }
    
    /**
     * Render enhanced error fallback
     */
    private function renderErrorFallback($exception) {
        require_once __DIR__ . '/components/ErrorBoundary.php';
        return displayPhpError(
            'Something went wrong while loading the application. This might be due to a server issue or browser compatibility.',
            $exception->getMessage()
        );
    }
    
    /**
     * Load users from JSON file
     */
    private function loadUsers($includeDeleted = false) {
        $usersFile = __DIR__ . '/data/users.json';
        if (file_exists($usersFile)) {
            $content = file_get_contents($usersFile);
            $users = json_decode($content, true) ?? [];
            
            // Filter out deleted users by default unless specifically requested
            if (!$includeDeleted) {
                return array_filter($users, function($user) {
                    $status = $user['status'] ?? 'active';
                    return $status !== 'deleted';
                });
            }
            return $users;
        }
        return [];
    }
    
    /**
     * Save users to JSON file
     */
    private function saveUsers($users) {
        $usersFile = __DIR__ . '/data/users.json';
        $directory = dirname($usersFile);
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }
    
    /**
     * Get current authentication state
     */
    public function getAuthState() {
        return $this->authState;
    }
    
    /**
     * Get current view
     */
    public function getCurrentView() {
        return $this->currentView;
    }
    
    /**
     * Check if app is still initializing
     */
    public function isInitializing() {
        return $this->isInitializing;
    }
    
    /**
     * Enhanced health check for the application
     */
    public function healthCheck() {
        $checks = [
            'data_directory' => is_dir(__DIR__ . '/data') && is_writable(__DIR__ . '/data'),
            'session_active' => session_status() === PHP_SESSION_ACTIVE,
            'components_available' => file_exists(__DIR__ . '/components/LandingPage.php'),
            'config_loaded' => isset($this->config['admin']['email'])
        ];
        
        $healthy = array_reduce($checks, function($carry, $check) {
            return $carry && $check;
        }, true);
        
        return [
            'healthy' => $healthy,
            'checks' => $checks,
            'timestamp' => date('c')
        ];
    }
}

// Global function for easy access (matching React pattern)
function createBarangayLinkApp() {
    return new BarangayLinkApp();
}

// Export the class for use in other files
if (!class_exists('BarangayLinkApp')) {
    throw new Exception('BarangayLinkApp class failed to load');
}
?>