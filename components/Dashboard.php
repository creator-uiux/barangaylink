<?php
function renderDashboard() {
    if (!isset($_SESSION['user'])) {
        header('Location: App.php');
        exit;
    }
    
    $user = $_SESSION['user'];
    $currentSection = $_GET['section'] ?? 'overview';
    
    // Load user data
    $userRequests = getUserRequests($user['id']);
    $userConcerns = getUserConcerns($user['id']);
    $profileData = getUserProfile($user['id']);
    
    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        if (updateUserProfile($user['id'], $phone, $address)) {
            echo '<script>showToast("Profile updated successfully!", "success");</script>';
            // Update session data
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['address'] = $address;
            $profileData = ['phone' => $phone, 'address' => $address];
        } else {
            echo '<script>showToast("Failed to update profile", "error");</script>';
        }
    }
?>

<div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <span class="text-xl font-semibold">BarangayLink</span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="App.php?section=overview" class="hover:bg-white/10 px-3 py-2 rounded-md transition-colors <?php echo $currentSection === 'overview' ? 'bg-white/10' : ''; ?>">
                        Dashboard
                    </a>
                    <a href="App.php?section=profile" class="hover:bg-white/10 px-3 py-2 rounded-md transition-colors <?php echo $currentSection === 'profile' ? 'bg-white/10' : ''; ?>">
                        My Profile
                    </a>
                    <a href="App.php?logout=1" class="text-white hover:bg-white/10 px-3 py-2 rounded-md transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>

                <!-- User Profile -->
                <div class="flex items-center space-x-3">
                    <div class="hidden md:flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Welcome, <?php echo explode(' ', $user['fullName'])[0]; ?></span>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button onclick="toggleMobileMenu()" class="p-2 rounded-md hover:bg-white/10">
                            <div class="w-6 h-6 flex flex-col justify-around">
                                <span id="mobile-line-1" class="h-0.5 w-6 bg-white transform transition"></span>
                                <span id="mobile-line-2" class="h-0.5 w-6 bg-white transition"></span>
                                <span id="mobile-line-3" class="h-0.5 w-6 bg-white transform transition"></span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-dashboard-menu" class="md:hidden bg-blue-700 border-t border-blue-600 hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="App.php?section=overview" class="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left">
                        Dashboard
                    </a>
                    <a href="App.php?section=profile" class="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left">
                        My Profile
                    </a>
                    <a href="App.php?logout=1" class="w-full justify-start text-white hover:bg-white/10 px-3 py-2 rounded-md mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Section Content -->
    <?php if ($currentSection === 'overview'): ?>
        <?php renderOverviewSection($user, $userRequests, $userConcerns); ?>
    <?php elseif ($currentSection === 'profile'): ?>
        <?php renderProfileSection($user, $userRequests, $userConcerns, $profileData); ?>
    <?php endif; ?>

    <!-- Modals -->
    <?php include 'DocumentRequestModal.php'; renderDocumentRequestModal(); ?>
    <?php include 'ConcernModal.php'; renderConcernModal(); ?>
</div>

<script>
let isMobileMenuOpen = false;

function toggleMobileMenu() {
    isMobileMenuOpen = !isMobileMenuOpen;
    const menu = document.getElementById('mobile-dashboard-menu');
    const line1 = document.getElementById('mobile-line-1');
    const line2 = document.getElementById('mobile-line-2');
    const line3 = document.getElementById('mobile-line-3');
    
    if (isMobileMenuOpen) {
        menu.classList.remove('hidden');
        line1.style.transform = 'rotate(45deg) translateY(10px)';
        line2.style.opacity = '0';
        line3.style.transform = 'rotate(-45deg) translateY(-10px)';
    } else {
        menu.classList.add('hidden');
        line1.style.transform = '';
        line2.style.opacity = '1';
        line3.style.transform = '';
    }
}

function handleQuickAction(action) {
    switch (action) {
        case 'announcements':
            showToast('Redirecting to announcements...', 'success');
            break;
        case 'events':
            showToast('Redirecting to events...', 'success');
            break;
        case 'projects':
            showToast('Redirecting to projects...', 'success');
            break;
        case 'documents':
            showDocumentRequestModal();
            break;
        case 'concerns':
            showConcernModal();
            break;
        case 'profile':
            window.location.href = 'App.php?section=profile';
            break;
        default:
            showToast('Feature coming soon!', 'success');
    }
}

function toggleProfileEdit() {
    const form = document.getElementById('profile-form');
    const editBtn = document.getElementById('edit-profile-btn');
    const saveBtn = document.getElementById('save-profile-btn');
    const cancelBtn = document.getElementById('cancel-profile-btn');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    
    const isEditing = !phoneInput.readOnly;
    
    if (isEditing) {
        // Cancel editing
        phoneInput.readOnly = true;
        addressInput.readOnly = true;
        phoneInput.classList.add('bg-gray-50');
        addressInput.classList.add('bg-gray-50');
        editBtn.classList.remove('hidden');
        saveBtn.classList.add('hidden');
        cancelBtn.classList.add('hidden');
        
        // Reset values
        phoneInput.value = phoneInput.dataset.original || '';
        addressInput.value = addressInput.dataset.original || '';
    } else {
        // Start editing
        phoneInput.dataset.original = phoneInput.value;
        addressInput.dataset.original = addressInput.value;
        phoneInput.readOnly = false;
        addressInput.readOnly = false;
        phoneInput.classList.remove('bg-gray-50');
        addressInput.classList.remove('bg-gray-50');
        editBtn.classList.add('hidden');
        saveBtn.classList.remove('hidden');
        cancelBtn.classList.remove('hidden');
    }
}
</script>

<?php
}

function renderOverviewSection($user, $userRequests, $userConcerns) {
    $pendingRequests = array_filter($userRequests, function($req) {
        return $req['status'] === 'pending';
    });
    
    $quickAccessItems = [
        [
            'id' => 'announcements',
            'title' => 'Announcements',
            'description' => 'View latest barangay announcements and updates',
            'icon' => 'users',
            'badge' => '3 New',
            'badgeColor' => 'bg-red-500'
        ],
        [
            'id' => 'events',
            'title' => 'Events',
            'description' => 'Check upcoming community events and activities',
            'icon' => 'calendar',
            'badge' => '2 Upcoming',
            'badgeColor' => 'bg-blue-500'
        ],
        [
            'id' => 'projects',
            'title' => 'Projects',
            'description' => 'Track ongoing barangay development projects',
            'icon' => 'hammer',
            'badge' => null,
            'badgeColor' => ''
        ],
        [
            'id' => 'documents',
            'title' => 'Request Documents',
            'description' => 'Apply for barangay certificates and clearances',
            'icon' => 'file-text',
            'badge' => null,
            'badgeColor' => ''
        ],
        [
            'id' => 'concerns',
            'title' => 'Submit Concerns',
            'description' => 'Report issues or submit feedback to barangay officials',
            'icon' => 'message-square',
            'badge' => null,
            'badgeColor' => ''
        ],
        [
            'id' => 'profile',
            'title' => 'My Profile',
            'description' => 'Update your personal information and settings',
            'icon' => 'settings',
            'badge' => null,
            'badgeColor' => ''
        ]
    ];
    
    $recentUpdates = [
        [
            'id' => 1,
            'type' => 'announcement',
            'icon' => 'users',
            'title' => 'New Announcement: Community Clean-up Drive',
            'description' => 'Join us for our monthly community clean-up drive this Saturday at 7:00 AM.',
            'time' => '2 hours ago'
        ],
        [
            'id' => 2,
            'type' => 'event',
            'icon' => 'calendar',
            'title' => 'Event Reminder: Barangay Assembly Meeting',
            'description' => 'Monthly barangay assembly meeting scheduled for December 20, 2024.',
            'time' => '1 day ago'
        ],
        [
            'id' => 3,
            'type' => 'document',
            'icon' => 'check-circle',
            'title' => 'Document Request Approved',
            'description' => 'Your barangay clearance request has been approved and is ready for pickup.',
            'time' => '2 days ago'
        ],
        [
            'id' => 4,
            'type' => 'project',
            'icon' => 'hammer',
            'title' => 'Project Update: Road Improvement',
            'description' => 'Phase 2 of the road improvement project on Main Street has been completed.',
            'time' => '3 days ago'
        ],
        [
            'id' => 5,
            'type' => 'health',
            'icon' => 'file-text',
            'title' => 'Health Service: Free Medical Check-up',
            'description' => 'Free medical check-up available at the barangay health center every Tuesday and Thursday.',
            'time' => '1 week ago'
        ]
    ];
?>

<!-- Welcome Banner -->
<section class="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row justify-between items-center">
            <div class="text-center lg:text-left mb-6 lg:mb-0">
                <h1 class="text-3xl font-bold mb-2">Welcome, <?php echo explode(' ', $user['fullName'])[0]; ?>!</h1>
                <p class="text-blue-100 mb-4">Stay up to date with the latest barangay news, events, and services.</p>
                <div class="flex items-center justify-center lg:justify-start space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="text-blue-100">Today: Sunny, 28°C</span>
                </div>
            </div>
            <div class="flex space-x-8 text-center">
                <div class="bg-white/10 p-4 rounded-lg">
                    <div class="text-2xl font-bold">3</div>
                    <div class="text-sm text-blue-100">New Announcements</div>
                </div>
                <div class="bg-white/10 p-4 rounded-lg">
                    <div class="text-2xl font-bold">2</div>
                    <div class="text-sm text-blue-100">Upcoming Events</div>
                </div>
                <div class="bg-white/10 p-4 rounded-lg">
                    <div class="text-2xl font-bold"><?php echo count($pendingRequests); ?></div>
                    <div class="text-sm text-blue-100">Pending Requests</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Access Cards -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-8">Quick Access</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($quickAccessItems as $item): ?>
                <div class="cursor-pointer hover:shadow-lg transition-all duration-200 hover:-translate-y-1 border-l-4 border-l-blue-600 relative bg-white rounded-lg p-6 shadow-md" onclick="handleQuickAction('<?php echo $item['id']; ?>')">
                    <?php if ($item['badge']): ?>
                        <div class="absolute top-3 right-3 <?php echo $item['badgeColor']; ?> text-white text-xs px-2 py-1 rounded-full">
                            <?php echo $item['badge']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="pb-3">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <?php echo getIconSvg($item['icon'], 'w-6 h-6 text-blue-600'); ?>
                            </div>
                            <h3 class="text-lg font-bold"><?php echo $item['title']; ?></h3>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-gray-600 text-sm"><?php echo $item['description']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Recent Updates -->
<section class="pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-8">Recent Updates</h2>
        <div class="bg-white rounded-lg shadow-md">
            <div class="divide-y">
                <?php foreach ($recentUpdates as $update): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <?php echo getIconSvg($update['icon'], 'w-6 h-6 text-blue-600'); ?>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-1"><?php echo $update['title']; ?></h4>
                                <p class="text-gray-600 mb-2"><?php echo $update['description']; ?></p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo $update['time']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="text-center mt-6">
            <button onclick="showToast('Loading more updates...', 'success')" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors">
                Load More Updates
            </button>
        </div>
    </div>
</section>

<?php
}

function renderProfileSection($user, $userRequests, $userConcerns, $profileData) {
?>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">My Profile</h1>
            <p class="text-gray-600">Manage your account information and settings</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Profile Information Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-bold">Personal Information</h3>
                        <button id="edit-profile-btn" onclick="toggleProfileEdit()" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                    </div>
                    
                    <form id="profile-form" method="POST" class="p-6 space-y-4">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label for="fullName" class="block font-medium text-slate-900 mb-2">Full Name</label>
                                <input
                                    id="fullName"
                                    name="fullName"
                                    type="text"
                                    value="<?php echo htmlspecialchars($user['fullName']); ?>"
                                    readonly
                                    class="w-full px-3 py-2 border border-slate-300 rounded-md bg-gray-50"
                                />
                            </div>
                            <div>
                                <label for="email" class="block font-medium text-slate-900 mb-2">Email Address</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>"
                                    readonly
                                    class="w-full px-3 py-2 border border-slate-300 rounded-md bg-gray-50"
                                />
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block font-medium text-slate-900 mb-2">Phone Number</label>
                                <input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    value="<?php echo htmlspecialchars($profileData['phone'] ?? ''); ?>"
                                    readonly
                                    class="w-full px-3 py-2 border border-slate-300 rounded-md bg-gray-50"
                                    placeholder="Not provided"
                                />
                            </div>
                            <div>
                                <label for="address" class="block font-medium text-slate-900 mb-2">Address</label>
                                <input
                                    id="address"
                                    name="address"
                                    type="text"
                                    value="<?php echo htmlspecialchars($profileData['address'] ?? ''); ?>"
                                    readonly
                                    class="w-full px-3 py-2 border border-slate-300 rounded-md bg-gray-50"
                                    placeholder="Not provided"
                                />
                            </div>
                        </div>
                        
                        <div class="flex gap-3 pt-4 border-t hidden" id="profile-buttons">
                            <button id="save-profile-btn" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors hidden">
                                Save Changes
                            </button>
                            <button id="cancel-profile-btn" type="button" onclick="toggleProfileEdit()" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors hidden">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Statistics Card -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold">Account Activity</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600"><?php echo count($userRequests); ?></div>
                                <div class="text-sm text-gray-600">Document Requests</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600"><?php echo count($userConcerns); ?></div>
                                <div class="text-sm text-gray-600">Concerns Submitted</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">
                                    <?php echo isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : '-'; ?>
                                </div>
                                <div class="text-sm text-gray-600">Member Since</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings Card -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold">Security Settings</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="p-4 border rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium">Password</h4>
                                    <p class="text-sm text-gray-600">Last updated: Never</p>
                                </div>
                                <button onclick="showToast('Password change feature coming soon!', 'success')" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Change
                                </button>
                            </div>
                        </div>
                        <div class="p-4 border rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium">Two-Factor Authentication</h4>
                                    <p class="text-sm text-gray-600">Add an extra layer of security</p>
                                </div>
                                <button onclick="showToast('2FA feature coming soon!', 'success')" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Enable
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
}

// Helper functions
function getUserRequests($userId) {
    $file = __DIR__ . '/../data/requests.json';
    if (file_exists($file)) {
        $requests = json_decode(file_get_contents($file), true) ?: [];
        return array_filter($requests, function($req) use ($userId) {
            return $req['userId'] === $userId;
        });
    }
    return [];
}

function getUserConcerns($userId) {
    $file = __DIR__ . '/../data/concerns.json';
    if (file_exists($file)) {
        $concerns = json_decode(file_get_contents($file), true) ?: [];
        return array_filter($concerns, function($concern) use ($userId) {
            return $concern['userId'] === $userId;
        });
    }
    return [];
}

function getUserProfile($userId) {
    $file = __DIR__ . '/../data/profiles.json';
    if (file_exists($file)) {
        $profiles = json_decode(file_get_contents($file), true) ?: [];
        return $profiles[$userId] ?? ['phone' => '', 'address' => ''];
    }
    return ['phone' => '', 'address' => ''];
}

function updateUserProfile($userId, $phone, $address) {
    $dir = __DIR__ . '/../data';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $file = $dir . '/profiles.json';
    $profiles = [];
    if (file_exists($file)) {
        $profiles = json_decode(file_get_contents($file), true) ?: [];
    }
    
    $profiles[$userId] = ['phone' => $phone, 'address' => $address];
    
    return file_put_contents($file, json_encode($profiles, JSON_PRETTY_PRINT)) !== false;
}

function getIconSvg($iconName, $classes = 'w-6 h-6') {
    $icons = [
        'users' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>',
        'calendar' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V9a2 2 0 01-2 2H4a2 2 0 01-2-2V7a2 2 0 012-2h2m6 0h2a2 2 0 012 2v2a2 2 0 01-2 2h-2m-6 0v6a2 2 0 002 2h4a2 2 0 002-2v-6"></path></svg>',
        'hammer' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>',
        'file-text' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'message-square' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>',
        'settings' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'check-circle' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>