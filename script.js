// BarangayLink Application - JavaScript
class BarangayApp {
    constructor() {
        this.currentUser = null;
        this.isLoading = true;
        this.currentPage = 'landing';
        this.currentSection = 'overview';
        this.init();
    }

    init() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Check for stored user session
        this.checkStoredSession();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Hide loading and show appropriate page
        setTimeout(() => {
            this.hideLoading();
            this.showCurrentPage();
        }, 500);
    }

    checkStoredSession() {
        const storedUser = localStorage.getItem('barangaylink_current_user') || 
                          sessionStorage.getItem('barangaylink_current_user');
        
        if (storedUser) {
            try {
                this.currentUser = JSON.parse(storedUser);
                this.currentPage = 'dashboard';
            } catch (error) {
                console.error('Error parsing stored user:', error);
            }
        }
        this.isLoading = false;
    }

    hideLoading() {
        document.getElementById('loading').classList.add('hidden');
    }

    showCurrentPage() {
        // Hide all pages
        document.getElementById('landing-page').classList.add('hidden');
        document.getElementById('user-dashboard').classList.add('hidden');
        document.getElementById('admin-dashboard').classList.add('hidden');

        if (this.currentPage === 'landing') {
            document.getElementById('landing-page').classList.remove('hidden');
        } else if (this.currentUser?.role === 'admin') {
            document.getElementById('admin-dashboard').classList.remove('hidden');
            this.loadAdminDashboard();
        } else {
            document.getElementById('user-dashboard').classList.remove('hidden');
            this.loadUserDashboard();
        }
    }

    setupEventListeners() {
        // Landing page events
        document.getElementById('login-btn')?.addEventListener('click', () => this.openAuthModal());
        document.getElementById('get-started-btn')?.addEventListener('click', () => this.openAuthModal());
        document.getElementById('hero-get-started')?.addEventListener('click', () => this.openAuthModal());

        // Auth modal events
        this.setupAuthModalEvents();

        // User dashboard events
        this.setupUserDashboardEvents();

        // Admin dashboard events
        this.setupAdminDashboardEvents();

        // Modal events
        this.setupModalEvents();
    }

    setupAuthModalEvents() {
        // Tab switching
        document.getElementById('login-tab')?.addEventListener('click', () => this.switchAuthTab('login'));
        document.getElementById('signup-tab')?.addEventListener('click', () => this.switchAuthTab('signup'));
        document.getElementById('reset-tab')?.addEventListener('click', () => this.switchAuthTab('reset'));

        // Form submissions
        document.getElementById('login-submit')?.addEventListener('click', () => this.handleLogin());
        document.getElementById('signup-submit')?.addEventListener('click', () => this.handleSignup());
        document.getElementById('reset-submit')?.addEventListener('click', () => this.handlePasswordReset());

        // Close modal
        document.getElementById('close-auth-modal')?.addEventListener('click', () => this.closeAuthModal());
        document.getElementById('auth-modal')?.addEventListener('click', (e) => {
            if (e.target.id === 'auth-modal') this.closeAuthModal();
        });
    }

    setupUserDashboardEvents() {
        // Navigation
        document.getElementById('user-overview-btn')?.addEventListener('click', () => this.showUserSection('overview'));
        document.getElementById('user-profile-btn')?.addEventListener('click', () => this.showUserSection('profile'));
        document.getElementById('user-overview-mobile-btn')?.addEventListener('click', () => this.showUserSection('overview'));
        document.getElementById('user-profile-mobile-btn')?.addEventListener('click', () => this.showUserSection('profile'));

        // Logout
        document.getElementById('user-logout-btn')?.addEventListener('click', () => this.logout());
        document.getElementById('user-logout-mobile-btn')?.addEventListener('click', () => this.logout());

        // Mobile menu
        document.getElementById('user-mobile-menu-btn')?.addEventListener('click', () => this.toggleMobileMenu('user'));

        // Profile editing
        document.getElementById('edit-profile-btn')?.addEventListener('click', () => this.toggleProfileEdit());
        document.getElementById('save-profile-btn')?.addEventListener('click', () => this.saveProfile());
        document.getElementById('cancel-profile-btn')?.addEventListener('click', () => this.cancelProfileEdit());
    }

    setupAdminDashboardEvents() {
        // Navigation
        document.getElementById('admin-overview-btn')?.addEventListener('click', () => this.showAdminSection('overview'));
        document.getElementById('admin-requests-btn')?.addEventListener('click', () => this.showAdminSection('requests'));
        document.getElementById('admin-concerns-btn')?.addEventListener('click', () => this.showAdminSection('concerns'));
        document.getElementById('admin-users-btn')?.addEventListener('click', () => this.showAdminSection('users'));

        // Mobile navigation
        document.getElementById('admin-overview-mobile-btn')?.addEventListener('click', () => this.showAdminSection('overview'));
        document.getElementById('admin-requests-mobile-btn')?.addEventListener('click', () => this.showAdminSection('requests'));
        document.getElementById('admin-concerns-mobile-btn')?.addEventListener('click', () => this.showAdminSection('concerns'));
        document.getElementById('admin-users-mobile-btn')?.addEventListener('click', () => this.showAdminSection('users'));

        // Logout
        document.getElementById('admin-logout-btn')?.addEventListener('click', () => this.logout());
        document.getElementById('admin-logout-mobile-btn')?.addEventListener('click', () => this.logout());

        // Mobile menu
        document.getElementById('admin-mobile-menu-btn')?.addEventListener('click', () => this.toggleMobileMenu('admin'));

        // Search functionality
        document.getElementById('admin-requests-search')?.addEventListener('input', (e) => this.searchRequests(e.target.value));
        document.getElementById('admin-concerns-search')?.addEventListener('input', (e) => this.searchConcerns(e.target.value));
        document.getElementById('admin-users-search')?.addEventListener('input', (e) => this.searchUsers(e.target.value));
    }

    setupModalEvents() {
        // Document modal
        document.getElementById('close-document-modal')?.addEventListener('click', () => this.closeDocumentModal());
        document.getElementById('document-modal')?.addEventListener('click', (e) => {
            if (e.target.id === 'document-modal') this.closeDocumentModal();
        });
        document.getElementById('document-request-form')?.addEventListener('submit', (e) => this.handleDocumentRequest(e));

        // Concern modal
        document.getElementById('close-concern-modal')?.addEventListener('click', () => this.closeConcernModal());
        document.getElementById('concern-modal')?.addEventListener('click', (e) => {
            if (e.target.id === 'concern-modal') this.closeConcernModal();
        });
        document.getElementById('concern-form')?.addEventListener('submit', (e) => this.handleConcernSubmission(e));
    }

    // Authentication methods
    openAuthModal() {
        document.getElementById('auth-modal').classList.remove('hidden');
        document.getElementById('auth-modal').classList.add('flex');
    }

    closeAuthModal() {
        document.getElementById('auth-modal').classList.add('hidden');
        document.getElementById('auth-modal').classList.remove('flex');
        this.clearAuthForms();
    }

    switchAuthTab(tab) {
        // Update tab styles
        document.querySelectorAll('#auth-modal button[id$="-tab"]').forEach(btn => {
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        document.getElementById(`${tab}-tab`).classList.add('border-blue-600', 'text-blue-600');
        document.getElementById(`${tab}-tab`).classList.remove('border-transparent', 'text-gray-500');

        // Show corresponding form
        document.getElementById('login-form').classList.add('hidden');
        document.getElementById('signup-form').classList.add('hidden');
        document.getElementById('reset-form').classList.add('hidden');
        document.getElementById(`${tab}-form`).classList.remove('hidden');
    }

    clearAuthForms() {
        document.querySelectorAll('#auth-modal input').forEach(input => {
            if (input.type !== 'radio') {
                input.value = '';
            }
        });
        // Reset radio buttons to default
        document.querySelector('input[name="login-role"][value="user"]').checked = true;
        document.querySelector('input[name="signup-role"][value="user"]').checked = true;
    }

    async handleLogin() {
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const role = document.querySelector('input[name="login-role"]:checked').value;
        const rememberMe = document.getElementById('remember-me').checked;

        if (!email || !password || !role) {
            this.showToast('Please fill in all fields and select your role', 'error');
            return;
        }

        const result = await this.login(email, password, role, rememberMe);
        
        if (result.success) {
            this.showToast(result.message, 'success');
            this.closeAuthModal();
            this.currentPage = 'dashboard';
            this.showCurrentPage();
        } else {
            this.showToast(result.message, 'error');
        }
    }

    async handleSignup() {
        const fullName = document.getElementById('signup-fullname').value;
        const email = document.getElementById('signup-email').value;
        const password = document.getElementById('signup-password').value;
        const confirmPassword = document.getElementById('signup-confirm-password').value;
        const role = document.querySelector('input[name="signup-role"]:checked').value;

        if (!fullName || !email || !password || !confirmPassword || !role) {
            this.showToast('Please fill in all fields and select your role', 'error');
            return;
        }

        if (password.length < 6) {
            this.showToast('Password must be at least 6 characters long', 'error');
            return;
        }

        if (password !== confirmPassword) {
            this.showToast('Passwords do not match', 'error');
            return;
        }

        const result = await this.signup(fullName, email, password, role);
        
        if (result.success) {
            this.showToast(result.message, 'success');
            this.clearAuthForms();
            this.switchAuthTab('login');
        } else {
            this.showToast(result.message, 'error');
        }
    }

    async handlePasswordReset() {
        const email = document.getElementById('reset-email').value;

        if (!email) {
            this.showToast('Please enter your email address', 'error');
            return;
        }

        const result = await this.resetPassword(email);
        this.showToast(result.message, result.success ? 'success' : 'error');
        
        if (result.success) {
            document.getElementById('reset-email').value = '';
        }
    }

    async login(email, password, role, rememberMe = false) {
        const users = this.getUsers();
        const foundUser = users.find(u => u.email === email && u.password === password && u.role === role);

        if (!foundUser) {
            return {
                success: false,
                message: 'Invalid email, password, or role selection. Please check your credentials and role.'
            };
        }

        const userToStore = {
            id: foundUser.id,
            fullName: foundUser.fullName,
            email: foundUser.email,
            role: foundUser.role,
            phone: foundUser.phone || '',
            address: foundUser.address || '',
            createdAt: foundUser.createdAt
        };

        this.currentUser = userToStore;

        if (rememberMe) {
            localStorage.setItem('barangaylink_current_user', JSON.stringify(userToStore));
        } else {
            sessionStorage.setItem('barangaylink_current_user', JSON.stringify(userToStore));
        }

        return { success: true, message: 'Login successful!' };
    }

    async signup(fullName, email, password, role) {
        const users = this.getUsers();

        if (users.find(u => u.email === email)) {
            return {
                success: false,
                message: 'An account with this email already exists'
            };
        }

        const newUser = {
            id: Date.now(),
            fullName,
            email,
            password,
            role,
            phone: '',
            address: '',
            createdAt: new Date().toISOString()
        };

        users.push(newUser);
        this.saveUsers(users);

        return { success: true, message: 'Account created successfully! Please login.' };
    }

    async resetPassword(email) {
        const users = this.getUsers();
        const userExists = users.find(u => u.email === email);

        if (!userExists) {
            return {
                success: false,
                message: 'No account found with this email address. Please sign up first.'
            };
        }

        return {
            success: true,
            message: 'Password reset instructions have been sent to your email address.'
        };
    }

    logout() {
        this.currentUser = null;
        localStorage.removeItem('barangaylink_current_user');
        sessionStorage.removeItem('barangaylink_current_user');
        this.currentPage = 'landing';
        this.showToast('Logged out successfully', 'success');
        this.showCurrentPage();
    }

    // User dashboard methods
    loadUserDashboard() {
        if (!this.currentUser) return;

        // Update user name in navigation
        const firstName = this.currentUser.fullName.split(' ')[0];
        document.getElementById('user-name-nav').textContent = firstName;
        document.getElementById('user-welcome-name').textContent = firstName;

        // Load user statistics
        this.updateUserStatistics();

        // Load profile information
        this.loadUserProfile();

        // Show overview section by default
        this.showUserSection('overview');
    }

    showUserSection(section) {
        // Update navigation active states
        document.querySelectorAll('#user-dashboard button[id^="user-"]').forEach(btn => {
            btn.classList.remove('bg-white/10');
        });
        document.getElementById(`user-${section}-btn`)?.classList.add('bg-white/10');

        // Hide all sections
        document.getElementById('user-overview-section').classList.add('hidden');
        document.getElementById('user-profile-section').classList.add('hidden');

        // Show selected section
        document.getElementById(`user-${section}-section`).classList.remove('hidden');

        // Hide mobile menu
        document.getElementById('user-mobile-menu').classList.add('hidden');

        this.currentSection = section;
    }

    updateUserStatistics() {
        const userRequests = this.getUserRequests();
        const pendingRequests = userRequests.filter(r => r.status === 'pending').length;
        
        document.getElementById('user-pending-requests').textContent = pendingRequests;
    }

    loadUserProfile() {
        if (!this.currentUser) return;

        document.getElementById('profile-fullname').value = this.currentUser.fullName || '';
        document.getElementById('profile-email').value = this.currentUser.email || '';
        document.getElementById('profile-phone').value = this.currentUser.phone || '';
        document.getElementById('profile-address').value = this.currentUser.address || '';

        // Update statistics
        const userRequests = this.getUserRequests();
        const userConcerns = this.getUserConcerns();
        
        document.getElementById('profile-requests-count').textContent = userRequests.length;
        document.getElementById('profile-concerns-count').textContent = userConcerns.length;
        
        const memberSince = new Date(this.currentUser.createdAt).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short'
        });
        document.getElementById('profile-member-since').textContent = memberSince;
    }

    toggleProfileEdit() {
        const isEditing = !document.getElementById('profile-phone').readOnly;
        
        if (!isEditing) {
            // Enable editing
            document.getElementById('profile-phone').readOnly = false;
            document.getElementById('profile-address').readOnly = false;
            document.getElementById('profile-phone').classList.remove('bg-gray-50');
            document.getElementById('profile-address').classList.remove('bg-gray-50');
            document.getElementById('profile-edit-actions').classList.remove('hidden');
            document.getElementById('edit-profile-btn').classList.add('hidden');
        }
    }

    cancelProfileEdit() {
        // Restore original values
        this.loadUserProfile();
        
        // Disable editing
        document.getElementById('profile-phone').readOnly = true;
        document.getElementById('profile-address').readOnly = true;
        document.getElementById('profile-phone').classList.add('bg-gray-50');
        document.getElementById('profile-address').classList.add('bg-gray-50');
        document.getElementById('profile-edit-actions').classList.add('hidden');
        document.getElementById('edit-profile-btn').classList.remove('hidden');
    }

    saveProfile() {
        const phone = document.getElementById('profile-phone').value;
        const address = document.getElementById('profile-address').value;

        // Update current user
        this.currentUser.phone = phone;
        this.currentUser.address = address;

        // Update stored user
        const storageKey = localStorage.getItem('barangaylink_current_user') ? 
                          'barangaylink_current_user' : 'barangaylink_current_user';
        
        if (localStorage.getItem('barangaylink_current_user')) {
            localStorage.setItem('barangaylink_current_user', JSON.stringify(this.currentUser));
        } else {
            sessionStorage.setItem('barangaylink_current_user', JSON.stringify(this.currentUser));
        }

        // Update users database
        const users = this.getUsers();
        const userIndex = users.findIndex(u => u.id === this.currentUser.id);
        if (userIndex !== -1) {
            users[userIndex] = { ...users[userIndex], phone, address };
            this.saveUsers(users);
        }

        // Disable editing
        this.cancelProfileEdit();
        
        this.showToast('Profile updated successfully', 'success');
    }

    toggleMobileMenu(type) {
        const menu = document.getElementById(`${type}-mobile-menu`);
        menu.classList.toggle('hidden');
    }

    // Admin dashboard methods
    loadAdminDashboard() {
        if (!this.currentUser || this.currentUser.role !== 'admin') return;

        // Update admin name in navigation
        const firstName = this.currentUser.fullName.split(' ')[0];
        document.getElementById('admin-name-nav').textContent = firstName;

        // Load admin statistics
        this.updateAdminStatistics();

        // Show overview section by default
        this.showAdminSection('overview');
    }

    showAdminSection(section) {
        // Update navigation active states
        document.querySelectorAll('#admin-dashboard button[id^="admin-"]').forEach(btn => {
            btn.classList.remove('bg-white/10');
        });
        document.getElementById(`admin-${section}-btn`)?.classList.add('bg-white/10');

        // Hide all sections
        document.getElementById('admin-overview-section').classList.add('hidden');
        document.getElementById('admin-requests-section').classList.add('hidden');
        document.getElementById('admin-concerns-section').classList.add('hidden');
        document.getElementById('admin-users-section').classList.add('hidden');

        // Show selected section
        document.getElementById(`admin-${section}-section`).classList.remove('hidden');

        // Load section data
        if (section === 'requests') {
            this.loadAdminRequests();
        } else if (section === 'concerns') {
            this.loadAdminConcerns();
        } else if (section === 'users') {
            this.loadAdminUsers();
        } else if (section === 'overview') {
            this.loadAdminOverview();
        }

        // Hide mobile menu
        document.getElementById('admin-mobile-menu').classList.add('hidden');

        this.currentSection = section;
    }

    updateAdminStatistics() {
        const allUsers = this.getUsers().filter(u => u.role === 'user');
        const allRequests = this.getAllRequests();
        const allConcerns = this.getAllConcerns();
        const pendingRequests = allRequests.filter(r => r.status === 'pending');

        document.getElementById('admin-total-users').textContent = allUsers.length;
        document.getElementById('admin-total-requests').textContent = allRequests.length;
        document.getElementById('admin-total-concerns').textContent = allConcerns.length;
        document.getElementById('admin-pending-requests').textContent = pendingRequests.length;
    }

    loadAdminOverview() {
        this.updateAdminStatistics();
        this.loadRecentActivity();
    }

    loadRecentActivity() {
        const requests = this.getAllRequests().slice(0, 3);
        const concerns = this.getAllConcerns().slice(0, 3);
        
        const allActivity = [...requests, ...concerns]
            .sort((a, b) => new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime())
            .slice(0, 5);

        const container = document.getElementById('admin-recent-activity');
        container.innerHTML = '';

        if (allActivity.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center">No recent activity</p>';
            return;
        }

        allActivity.forEach(item => {
            const user = this.getUserById(item.userId);
            const isRequest = 'documentType' in item;
            
            const activityItem = document.createElement('div');
            activityItem.className = 'flex items-center space-x-4 p-4 bg-gray-50 rounded-lg';
            
            activityItem.innerHTML = `
                <div class="p-2 rounded-full ${isRequest ? 'bg-blue-100' : 'bg-orange-100'}">
                    <i data-lucide="${isRequest ? 'file-text' : 'message-square'}" class="w-4 h-4 ${isRequest ? 'text-blue-600' : 'text-orange-600'}"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">
                        ${isRequest ? 
                            `New document request: ${this.getDocumentTypeLabel(item.documentType)}` :
                            `New concern: ${item.concernTitle}`
                        }
                    </p>
                    <p class="text-sm text-gray-600">
                        by ${user?.fullName || 'Unknown User'} - ${this.formatDate(item.submittedAt)}
                    </p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full ${this.getStatusBadgeColor(item.status)}">
                    ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                </span>
            `;
            
            container.appendChild(activityItem);
        });

        // Re-initialize Lucide icons for new elements
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    loadAdminRequests(searchTerm = '') {
        const requests = this.getAllRequests();
        const filteredRequests = this.filterRequests(requests, searchTerm);
        
        const container = document.getElementById('admin-requests-list');
        container.innerHTML = '';

        if (filteredRequests.length === 0) {
            container.innerHTML = `
                <div class="bg-white rounded-lg shadow-md text-center py-12">
                    <i data-lucide="file-text" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">No document requests</h3>
                    <p class="text-gray-600">No document requests have been submitted yet.</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            return;
        }

        filteredRequests.forEach(request => {
            const user = this.getUserById(request.userId);
            const requestCard = this.createRequestCard(request, user);
            container.appendChild(requestCard);
        });

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    createRequestCard(request, user) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow';
        
        card.innerHTML = `
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-1">
                            ${this.getDocumentTypeLabel(request.documentType)}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">
                            Requested by: ${user?.fullName || 'Unknown User'} (${user?.email || 'N/A'})
                        </p>
                    </div>
                    <div class="flex flex-col md:flex-row gap-2">
                        <span class="px-2 py-1 text-xs rounded-full ${this.getStatusBadgeColor(request.status)}">
                            ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                        </span>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Purpose</p>
                        <p class="text-sm text-gray-900">${request.purpose}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Contact Number</p>
                        <p class="text-sm text-gray-900">${request.contactNumber}</p>
                    </div>
                    ${request.additionalNotes ? `
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Additional Notes</p>
                            <p class="text-sm text-gray-900">${request.additionalNotes}</p>
                        </div>
                    ` : ''}
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between pt-4 border-t gap-4">
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="app.updateRequestStatus(${request.id}, 'pending')" 
                                class="px-3 py-1 text-sm rounded-md transition-colors ${request.status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}" 
                                ${request.status === 'pending' ? 'disabled' : ''}>
                            Pending
                        </button>
                        <button onclick="app.updateRequestStatus(${request.id}, 'approved')" 
                                class="px-3 py-1 text-sm rounded-md transition-colors ${request.status === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}" 
                                ${request.status === 'approved' ? 'disabled' : ''}>
                            Approve
                        </button>
                        <button onclick="app.updateRequestStatus(${request.id}, 'completed')" 
                                class="px-3 py-1 text-sm rounded-md transition-colors ${request.status === 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}" 
                                ${request.status === 'completed' ? 'disabled' : ''}>
                            Complete
                        </button>
                        <button onclick="app.updateRequestStatus(${request.id}, 'rejected')" 
                                class="px-3 py-1 text-sm rounded-md transition-colors ${request.status === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}" 
                                ${request.status === 'rejected' ? 'disabled' : ''}>
                            Reject
                        </button>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span>Request ID: #${request.id}</span>
                        <span class="mx-2">•</span>
                        <span>Submitted: ${this.formatDate(request.submittedAt)}</span>
                    </div>
                </div>
            </div>
        `;
        
        return card;
    }

    updateRequestStatus(requestId, newStatus) {
        try {
            const requests = this.getAllRequests();
            const updatedRequests = requests.map(req => 
                req.id === requestId ? { ...req, status: newStatus } : req
            );
            localStorage.setItem('barangaylink_requests', JSON.stringify(updatedRequests));
            this.loadAdminRequests();
            this.updateAdminStatistics();
            this.showToast('Request status updated successfully', 'success');
        } catch (error) {
            console.error('Error updating request status:', error);
            this.showToast('Failed to update request status', 'error');
        }
    }

    loadAdminConcerns(searchTerm = '') {
        const concerns = this.getAllConcerns();
        const filteredConcerns = this.filterConcerns(concerns, searchTerm);
        
        const container = document.getElementById('admin-concerns-list');
        container.innerHTML = '';

        if (filteredConcerns.length === 0) {
            container.innerHTML = `
                <div class="bg-white rounded-lg shadow-md text-center py-12">
                    <i data-lucide="message-square" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">No concerns submitted</h3>
                    <p class="text-gray-600">No community concerns have been submitted yet.</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            return;
        }

        filteredConcerns.forEach(concern => {
            const user = this.getUserById(concern.userId);
            const concernCard = this.createConcernCard(concern, user);
            container.appendChild(concernCard);
        });

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    createConcernCard(concern, user) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow';
        
        card.innerHTML = `
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-1">
                            ${concern.concernTitle}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">
                            Submitted by: ${user?.fullName || 'Unknown User'} (${user?.email || 'N/A'})
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 text-xs rounded-full ${this.getUrgencyBadgeColor(concern.urgencyLevel)}">
                            ${concern.urgencyLevel.charAt(0).toUpperCase() + concern.urgencyLevel.slice(1)}
                        </span>
                        <span class="px-2 py-1 text-xs rounded-full ${this.getStatusBadgeColor(concern.status)}">
                            ${concern.status.charAt(0).toUpperCase() + concern.status.slice(1)}
                        </span>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Type</p>
                        <p class="text-sm text-gray-900">${this.getConcernTypeLabel(concern.concernType)}</p>
                    </div>
                    ${concern.concernLocation ? `
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Location</p>
                            <p class="text-sm text-gray-900">${concern.concernLocation}</p>
                        </div>
                    ` : ''}
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Description</p>
                        <p class="text-sm text-gray-900">${concern.concernDescription}</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between pt-4 border-t gap-4">
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="app.updateConcernStatus(${concern.id}, 'submitted')" 
                                class="px-3 py-1 text-sm rounded-md transition-colors ${concern.status === 'submitted' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}" 
                                ${concern.status === 'submitted' ? 'disabled' : ''}>
                            Submitted
                        </button>
                        <button onclick="app.updateConcernStatus(${concern.id}, 'in-progress')" 
                                class="px-3 py-1 text-sm rounded-md transition-colors ${concern.status === 'in-progress' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}" 
                                ${concern.status === 'in-progress' ? 'disabled' : ''}>
                            In Progress
                        </button>
                        <button onclick="app.updateConcernStatus(${concern.id}, 'completed')" 
                                class="px-3 py-1 text-sm rounded-md transition-colors ${concern.status === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}" 
                                ${concern.status === 'completed' ? 'disabled' : ''}>
                            Complete
                        </button>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span>Concern ID: #${concern.id}</span>
                        <span class="mx-2">•</span>
                        <span>Submitted: ${this.formatDate(concern.submittedAt)}</span>
                    </div>
                </div>
            </div>
        `;
        
        return card;
    }

    updateConcernStatus(concernId, newStatus) {
        try {
            const concerns = this.getAllConcerns();
            const updatedConcerns = concerns.map(concern => 
                concern.id === concernId ? { ...concern, status: newStatus } : concern
            );
            localStorage.setItem('barangaylink_concerns', JSON.stringify(updatedConcerns));
            this.loadAdminConcerns();
            this.updateAdminStatistics();
            this.showToast('Concern status updated successfully', 'success');
        } catch (error) {
            console.error('Error updating concern status:', error);
            this.showToast('Failed to update concern status', 'error');
        }
    }

    loadAdminUsers(searchTerm = '') {
        const users = this.getUsers().filter(u => u.role === 'user');
        const filteredUsers = this.filterUsers(users, searchTerm);
        
        const container = document.getElementById('admin-users-list');
        container.innerHTML = '';

        if (filteredUsers.length === 0) {
            container.innerHTML = `
                <div class="bg-white rounded-lg shadow-md text-center py-12 md:col-span-2 lg:col-span-3">
                    <i data-lucide="users" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">No users registered</h3>
                    <p class="text-gray-600">No users have registered yet.</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            return;
        }

        filteredUsers.forEach(user => {
            const userCard = this.createUserCard(user);
            container.appendChild(userCard);
        });

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    createUserCard(user) {
        const userRequests = this.getAllRequests().filter(req => req.userId === user.id);
        const userConcerns = this.getAllConcerns().filter(con => con.userId === user.id);
        
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow';
        
        card.innerHTML = `
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="user-check" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">${user.fullName}</h3>
                        <p class="text-sm text-gray-600">${user.email}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-lg font-bold text-blue-600">${userRequests.length}</div>
                        <div class="text-xs text-gray-600">Requests</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-lg font-bold text-orange-600">${userConcerns.length}</div>
                        <div class="text-xs text-gray-600">Concerns</div>
                    </div>
                </div>
                
                <div class="text-sm text-gray-500 text-center">
                    Joined: ${this.formatDate(user.createdAt)}
                </div>
            </div>
        `;
        
        return card;
    }

    // Search methods
    searchRequests(searchTerm) {
        this.loadAdminRequests(searchTerm);
    }

    searchConcerns(searchTerm) {
        this.loadAdminConcerns(searchTerm);
    }

    searchUsers(searchTerm) {
        this.loadAdminUsers(searchTerm);
    }

    filterRequests(requests, searchTerm) {
        if (!searchTerm) return requests;
        
        return requests.filter(request => {
            const user = this.getUserById(request.userId);
            return (
                user?.fullName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                user?.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                this.getDocumentTypeLabel(request.documentType).toLowerCase().includes(searchTerm.toLowerCase()) ||
                request.purpose.toLowerCase().includes(searchTerm.toLowerCase())
            );
        });
    }

    filterConcerns(concerns, searchTerm) {
        if (!searchTerm) return concerns;
        
        return concerns.filter(concern => {
            const user = this.getUserById(concern.userId);
            return (
                user?.fullName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                user?.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                concern.concernTitle.toLowerCase().includes(searchTerm.toLowerCase()) ||
                concern.concernDescription.toLowerCase().includes(searchTerm.toLowerCase()) ||
                this.getConcernTypeLabel(concern.concernType).toLowerCase().includes(searchTerm.toLowerCase())
            );
        });
    }

    filterUsers(users, searchTerm) {
        if (!searchTerm) return users;
        
        return users.filter(user => {
            return (
                user.fullName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                user.email.toLowerCase().includes(searchTerm.toLowerCase())
            );
        });
    }

    // Modal methods
    openDocumentModal() {
        document.getElementById('document-modal').classList.remove('hidden');
        document.getElementById('document-modal').classList.add('flex');
    }

    closeDocumentModal() {
        document.getElementById('document-modal').classList.add('hidden');
        document.getElementById('document-modal').classList.remove('flex');
        document.getElementById('document-request-form').reset();
    }

    openConcernModal() {
        document.getElementById('concern-modal').classList.remove('hidden');
        document.getElementById('concern-modal').classList.add('flex');
    }

    closeConcernModal() {
        document.getElementById('concern-modal').classList.add('hidden');
        document.getElementById('concern-modal').classList.remove('flex');
        document.getElementById('concern-form').reset();
    }

    handleDocumentRequest(e) {
        e.preventDefault();
        
        if (!this.currentUser) {
            this.showToast('Please log in to submit a request', 'error');
            return;
        }

        const documentType = document.getElementById('document-type').value;
        const purpose = document.getElementById('document-purpose').value;
        const contactNumber = document.getElementById('document-contact').value;
        const additionalNotes = document.getElementById('document-notes').value;

        if (!documentType || !purpose || !contactNumber) {
            this.showToast('Please fill in all required fields', 'error');
            return;
        }

        const request = {
            id: Date.now(),
            userId: this.currentUser.id,
            documentType,
            purpose,
            contactNumber,
            additionalNotes,
            status: 'pending',
            submittedAt: new Date().toISOString()
        };

        const requests = this.getAllRequests();
        requests.push(request);
        localStorage.setItem('barangaylink_requests', JSON.stringify(requests));

        this.closeDocumentModal();
        this.showToast('Document request submitted successfully!', 'success');
        
        // Update user statistics if on user dashboard
        if (this.currentUser.role === 'user') {
            this.updateUserStatistics();
        }
    }

    handleConcernSubmission(e) {
        e.preventDefault();
        
        if (!this.currentUser) {
            this.showToast('Please log in to submit a concern', 'error');
            return;
        }

        const concernType = document.getElementById('concern-type').value;
        const concernTitle = document.getElementById('concern-title').value;
        const concernDescription = document.getElementById('concern-description').value;
        const concernLocation = document.getElementById('concern-location').value;
        const urgencyLevel = document.getElementById('concern-urgency').value;

        if (!concernType || !concernTitle || !concernDescription || !urgencyLevel) {
            this.showToast('Please fill in all required fields', 'error');
            return;
        }

        const concern = {
            id: Date.now(),
            userId: this.currentUser.id,
            concernType,
            concernTitle,
            concernDescription,
            concernLocation,
            urgencyLevel,
            status: 'submitted',
            submittedAt: new Date().toISOString()
        };

        const concerns = this.getAllConcerns();
        concerns.push(concern);
        localStorage.setItem('barangaylink_concerns', JSON.stringify(concerns));

        this.closeConcernModal();
        this.showToast('Concern submitted successfully!', 'success');
    }

    // Global methods that need to be accessible
    showUserProfile() {
        this.showUserSection('profile');
    }

    // Utility methods
    getUsers() {
        try {
            return JSON.parse(localStorage.getItem('barangaylink_users') || '[]');
        } catch {
            return [];
        }
    }

    saveUsers(users) {
        localStorage.setItem('barangaylink_users', JSON.stringify(users));
    }

    getUserById(userId) {
        const users = this.getUsers();
        return users.find(u => u.id === userId);
    }

    getUserRequests() {
        if (!this.currentUser) return [];
        return this.getAllRequests().filter(req => req.userId === this.currentUser.id);
    }

    getUserConcerns() {
        if (!this.currentUser) return [];
        return this.getAllConcerns().filter(concern => concern.userId === this.currentUser.id);
    }

    getAllRequests() {
        try {
            return JSON.parse(localStorage.getItem('barangaylink_requests') || '[]')
                .sort((a, b) => new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime());
        } catch {
            return [];
        }
    }

    getAllConcerns() {
        try {
            return JSON.parse(localStorage.getItem('barangaylink_concerns') || '[]')
                .sort((a, b) => new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime());
        } catch {
            return [];
        }
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    getStatusBadgeColor(status) {
        switch (status) {
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            case 'approved': return 'bg-green-100 text-green-800';
            case 'completed': return 'bg-blue-100 text-blue-800';
            case 'rejected': return 'bg-red-100 text-red-800';
            case 'submitted': return 'bg-blue-100 text-blue-800';
            case 'in-progress': return 'bg-orange-100 text-orange-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    getUrgencyBadgeColor(urgency) {
        switch (urgency) {
            case 'low': return 'bg-gray-100 text-gray-800';
            case 'medium': return 'bg-yellow-100 text-yellow-800';
            case 'high': return 'bg-orange-100 text-orange-800';
            case 'emergency': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    getDocumentTypeLabel(type) {
        const labels = {
            'barangay-clearance': 'Barangay Clearance',
            'certificate-residency': 'Certificate of Residency',
            'certificate-indigency': 'Certificate of Indigency',
            'business-permit': 'Business Permit',
            'id-replacement': 'ID Replacement'
        };
        return labels[type] || type;
    }

    getConcernTypeLabel(type) {
        const labels = {
            'infrastructure': 'Infrastructure',
            'public-safety': 'Public Safety',
            'sanitation': 'Sanitation',
            'noise-complaint': 'Noise Complaint',
            'community-service': 'Community Service',
            'other': 'Other'
        };
        return labels[type] || type;
    }

    showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const bgColor = type === 'success' ? 'bg-green-500' : 
                       type === 'error' ? 'bg-red-500' : 
                       type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
        
        toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 opacity-0 translate-x-full`;
        toast.textContent = message;
        
        container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-full');
        }, 100);
        
        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => {
                if (container.contains(toast)) {
                    container.removeChild(toast);
                }
            }, 300);
        }, 5000);
    }
}

// Global functions that need to be accessible from HTML
function openDocumentModal() {
    window.app.openDocumentModal();
}

function openConcernModal() {
    window.app.openConcernModal();
}

function showUserProfile() {
    window.app.showUserProfile();
}

// Initialize the application
window.app = new BarangayApp();