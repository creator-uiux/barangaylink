// Main application state and logic
class BarangayLinkApp {
    constructor() {
        this.isLoggedIn = false;
        this.currentUser = null;
        this.showAuthModal = false;
        this.currentPage = 'landing';
        this.init();
    }

    init() {
        this.render();
        this.setupGlobalEventListeners();
    }

    // State management methods
    setState(newState) {
        Object.assign(this, newState);
        this.render();
    }

    handleLogin(email, name) {
        this.setState({
            isLoggedIn: true,
            currentUser: name || email.split('@')[0],
            showAuthModal: false
        });
        Toast.success('Login successful!');
    }

    handleLogout() {
        this.setState({
            isLoggedIn: false,
            currentUser: null,
            currentPage: 'landing'
        });
        Toast.success('Logged out successfully!');
    }

    openAuthModal() {
        this.setState({ showAuthModal: true });
    }

    closeAuthModal() {
        this.setState({ showAuthModal: false });
    }

    // Render methods
    render() {
        const app = document.getElementById('app');
        app.innerHTML = '';

        if (this.isLoggedIn) {
            this.renderDashboard(app);
        } else {
            this.renderLandingPage(app);
        }

        if (this.showAuthModal) {
            this.renderAuthModal(app);
        }
    }

    renderLandingPage(container) {
        // Create navigation
        const navbar = createLandingNavbar(() => this.openAuthModal());
        container.appendChild(navbar);

        // Create hero section
        const hero = createHero(
            () => this.openAuthModal(),
            () => scrollToSection('announcements')
        );
        container.appendChild(hero);

        // Create announcements section
        const announcements = createAnnouncementsPreview();
        container.appendChild(announcements);

        // Create events & projects section
        const eventsProjects = this.createEventsProjects();
        container.appendChild(eventsProjects);

        // Create how it works section
        const howItWorks = this.createHowItWorks();
        container.appendChild(howItWorks);

        // Create contact section
        const contact = this.createContact();
        container.appendChild(contact);

        // Create footer
        const footer = this.createFooter();
        container.appendChild(footer);
    }

    renderDashboard(container) {
        // Create dashboard navigation
        const navbar = this.createDashboardNavbar();
        container.appendChild(navbar);

        // Main dashboard content
        const main = createElement('main', 'pt-16 min-h-screen bg-gray-50');
        main.innerHTML = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div id="dashboard-content"></div>
            </div>
        `;
        container.appendChild(main);

        // Add dashboard components
        const dashboardContent = main.querySelector('#dashboard-content');
        
        // Welcome section
        const welcome = this.createDashboardWelcome();
        dashboardContent.appendChild(welcome);

        // Quick access
        const quickAccess = this.createDashboardQuickAccess();
        dashboardContent.appendChild(quickAccess);

        // Recent updates
        const recentUpdates = this.createDashboardRecentUpdates();
        dashboardContent.appendChild(recentUpdates);
    }

    createDashboardNavbar() {
        const navbar = createElement('nav', 'fixed top-0 left-0 right-0 bg-white shadow-sm z-50');
        
        navbar.innerHTML = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">BL</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">BarangayLink</span>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <button class="text-blue-600 font-medium">Dashboard</button>
                        <button class="text-gray-700 hover:text-blue-600 transition-colors">Announcements</button>
                        <button class="text-gray-700 hover:text-blue-600 transition-colors">Events</button>
                        <button class="text-gray-700 hover:text-blue-600 transition-colors">Projects</button>
                        <button class="text-gray-700 hover:text-blue-600 transition-colors">My Requests</button>
                    </div>

                    <!-- Desktop User Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </button>

                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button id="profile-dropdown-btn" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="text-gray-700 font-medium">${this.currentUser}</span>
                            </button>

                            <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</button>
                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</button>
                                <hr class="my-1">
                                <button id="logout-btn" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center space-x-2">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button id="mobile-menu-btn-dash" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div id="mobile-menu-dash" class="md:hidden bg-white border-t hidden">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <button class="block w-full text-left px-3 py-2 text-blue-600 bg-blue-50 font-medium rounded-md">Dashboard</button>
                        <button class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Announcements</button>
                        <button class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Events</button>
                        <button class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Projects</button>
                        <button class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">My Requests</button>
                        
                        <hr class="my-2">
                        
                        <div class="px-3 py-2">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="text-gray-700 font-medium">${this.currentUser}</span>
                            </div>
                            
                            <div class="space-y-1">
                                <button class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">Profile Settings</button>
                                <button class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">Notifications (3)</button>
                                <button id="mobile-logout-btn" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-50 rounded-md">Logout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add event listeners
        const profileDropdownBtn = navbar.querySelector('#profile-dropdown-btn');
        const profileDropdown = navbar.querySelector('#profile-dropdown');
        const logoutBtn = navbar.querySelector('#logout-btn');
        const mobileLogoutBtn = navbar.querySelector('#mobile-logout-btn');
        const mobileMenuBtn = navbar.querySelector('#mobile-menu-btn-dash');
        const mobileMenu = navbar.querySelector('#mobile-menu-dash');

        let isProfileDropdownOpen = false;
        profileDropdownBtn.addEventListener('click', () => {
            isProfileDropdownOpen = !isProfileDropdownOpen;
            if (isProfileDropdownOpen) {
                profileDropdown.classList.remove('hidden');
            } else {
                profileDropdown.classList.add('hidden');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileDropdownBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
                isProfileDropdownOpen = false;
            }
        });

        logoutBtn.addEventListener('click', () => this.handleLogout());
        mobileLogoutBtn.addEventListener('click', () => this.handleLogout());

        let isMobileMenuOpen = false;
        mobileMenuBtn.addEventListener('click', () => {
            isMobileMenuOpen = !isMobileMenuOpen;
            if (isMobileMenuOpen) {
                mobileMenu.classList.remove('hidden');
                mobileMenuBtn.innerHTML = '<i class="fas fa-times text-2xl"></i>';
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars text-2xl"></i>';
            }
        });

        return navbar;
    }

    createDashboardWelcome() {
        const currentDate = new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const section = createElement('div', 'mb-8');
        section.innerHTML = `
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-8 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-white mb-4 md:mb-0">
                        <h1 class="text-3xl font-bold mb-2">
                            Welcome back, ${this.currentUser}!
                        </h1>
                        <p class="text-blue-100 text-lg">
                            ${currentDate}
                        </p>
                        <p class="text-blue-100 mt-1">
                            Stay connected with your community and access all barangay services in one place.
                        </p>
                    </div>
                    
                    <div class="text-white text-right">
                        <div class="text-sm text-blue-100 mb-1">Your Status</div>
                        <div class="text-xl font-semibold">Active Resident</div>
                        <div class="text-sm text-blue-100 mt-1">Verified Account</div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                            <i class="fas fa-calendar text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">3</div>
                            <div class="text-gray-600">Upcoming Events</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-lg bg-green-100 text-green-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">1,524</div>
                            <div class="text-gray-600">Active Residents</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-lg bg-orange-100 text-orange-600">
                            <i class="fas fa-map-marker-alt text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">12</div>
                            <div class="text-gray-600">Service Requests</div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return section;
    }

    createEventsProjects() {
        const section = createElement('section', 'py-20 bg-white');
        section.id = 'events';
        
        section.innerHTML = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Events & Projects</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Discover upcoming community events and track the progress of ongoing barangay projects.
                    </p>
                </div>

                <!-- Tabs -->
                <div class="flex justify-center mb-12">
                    <div class="bg-gray-100 p-1 rounded-lg">
                        <button id="events-tab" class="px-6 py-2 rounded-md font-medium transition-colors bg-white text-blue-600 shadow-sm">
                            Upcoming Events
                        </button>
                        <button id="projects-tab" class="px-6 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-blue-600">
                            Community Projects
                        </button>
                    </div>
                </div>

                <!-- Events Content -->
                <div id="events-content" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    ${this.createEventsCards()}
                </div>

                <!-- Projects Content -->
                <div id="projects-content" class="grid md:grid-cols-2 gap-8 hidden">
                    ${this.createProjectsCards()}
                </div>
            </div>
        `;

        // Add tab functionality
        const eventsTab = section.querySelector('#events-tab');
        const projectsTab = section.querySelector('#projects-tab');
        const eventsContent = section.querySelector('#events-content');
        const projectsContent = section.querySelector('#projects-content');

        eventsTab.addEventListener('click', () => {
            eventsTab.className = 'px-6 py-2 rounded-md font-medium transition-colors bg-white text-blue-600 shadow-sm';
            projectsTab.className = 'px-6 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-blue-600';
            eventsContent.classList.remove('hidden');
            projectsContent.classList.add('hidden');
        });

        projectsTab.addEventListener('click', () => {
            projectsTab.className = 'px-6 py-2 rounded-md font-medium transition-colors bg-white text-blue-600 shadow-sm';
            eventsTab.className = 'px-6 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-blue-600';
            projectsContent.classList.remove('hidden');
            eventsContent.classList.add('hidden');
        });

        return section;
    }

    createEventsCards() {
        return mockEvents.map(event => `
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <h3 class="text-xl font-semibold text-gray-900">${event.title}</h3>
                        <p class="text-gray-600">${event.description}</p>
                    </div>
                    <div class="space-y-2 text-sm text-gray-500">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-calendar"></i>
                            <span>${formatDate(event.date)}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock"></i>
                            <span>${event.time}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${event.location}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-users"></i>
                            <span>${event.attendees}/${event.maxAttendees} registered</span>
                        </div>
                    </div>
                    <div class="pt-4">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-medium transition-colors">
                            Register for Event
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    createProjectsCards() {
        return mockProjects.map(project => {
            const statusColor = project.status === 'completed' ? 'bg-green-100 text-green-800' :
                              project.status === 'in-progress' ? 'bg-blue-100 text-blue-800' :
                              'bg-orange-100 text-orange-800';
            
            const statusIcon = project.status === 'completed' ? 'fas fa-check-circle' :
                             project.status === 'in-progress' ? 'fas fa-chart-line' :
                             'fas fa-clock';

            return `
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2 flex-1">
                                <h3 class="text-xl font-semibold text-gray-900">${project.title}</h3>
                                <p class="text-gray-600">${project.description}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${statusColor} flex items-center space-x-1">
                                <i class="${statusIcon}"></i>
                                <span>${project.status.replace('-', ' ')}</span>
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Progress</span>
                                <span class="font-medium">${project.progress}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: ${project.progress}%"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Budget:</span>
                                <div class="font-medium">${project.budget}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Expected Completion:</span>
                                <div class="font-medium">${formatDate(project.expectedCompletion)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    createHowItWorks() {
        const section = createElement('section', 'py-20 bg-gray-50');
        
        const steps = [
            { icon: 'fas fa-user-plus', title: 'Register', description: 'Create your account with basic information to join the BarangayLink community.', step: '01' },
            { icon: 'fas fa-sign-in-alt', title: 'Login', description: 'Access your personalized dashboard with secure login credentials.', step: '02' },
            { icon: 'fas fa-bell', title: 'Stay Updated', description: 'Receive real-time notifications about announcements, events, and community updates.', step: '03' },
            { icon: 'fas fa-file-alt', title: 'Request Documents', description: 'Submit requests for barangay certificates and other official documents online.', step: '04' }
        ];

        const stepsHTML = steps.map((step, index) => `
            <div class="relative">
                ${index < steps.length - 1 ? `
                    <div class="hidden lg:block absolute top-16 left-full w-full h-0.5 bg-blue-200 transform translate-x-4 z-0">
                        <div class="absolute right-0 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-blue-400 rounded-full"></div>
                    </div>
                ` : ''}
                <div class="relative bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 z-10">
                    <div class="text-center space-y-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 text-blue-600 rounded-full font-bold text-lg mb-4">
                            ${step.step}
                        </div>
                        <div class="flex justify-center">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="${step.icon} text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold text-gray-900">${step.title}</h3>
                            <p class="text-gray-600 leading-relaxed">${step.description}</p>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        section.innerHTML = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Getting connected with your barangay is simple. Follow these easy steps to start accessing community services.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    ${stepsHTML}
                </div>
                <div class="text-center mt-16">
                    <div class="bg-blue-600 rounded-2xl p-8 md:p-12">
                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Ready to Get Connected?</h3>
                        <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">
                            Join thousands of residents who are already enjoying seamless access to barangay services and community updates.
                        </p>
                        <button onclick="app.openAuthModal()" class="bg-white text-blue-600 hover:bg-gray-50 px-8 py-3 rounded-lg font-medium transition-colors">
                            Sign Up Today
                        </button>
                    </div>
                </div>
            </div>
        `;

        return section;
    }

    createContact() {
        const section = createElement('section', 'py-20 bg-white');
        section.id = 'contact';

        section.innerHTML = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Contact Us</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Get in touch with us for any questions, concerns, or feedback. We're here to help you.
                    </p>
                </div>
                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Get in Touch</h3>
                            <p class="text-gray-600 mb-8">
                                Have questions about our services or need assistance? Our team is ready to help you. 
                                Reach out to us through any of the following channels.
                            </p>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Address</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">Barangay Hall, Main Street, Poblacion</p>
                                    <p class="text-gray-500 text-sm">City, Province 1234</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-phone text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Phone</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">(02) 8123-4567</p>
                                    <p class="text-gray-500 text-sm">Mobile: +63 912 345 6789</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">info@barangaylink.gov.ph</p>
                                    <p class="text-gray-500 text-sm">barangay.poblacion@city.gov.ph</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Office Hours</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">Monday - Friday: 8:00 AM - 5:00 PM</p>
                                    <p class="text-gray-500 text-sm">Saturday: 8:00 AM - 12:00 PM</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <h4 class="font-semibold text-red-800 mb-2">Emergency Hotline</h4>
                            <p class="text-red-700">For urgent matters and emergencies, call:</p>
                            <p class="text-xl font-bold text-red-800">911 or (02) 8911-HELP</p>
                        </div>
                    </div>
                    <!-- Contact Form -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-6">Send us a Message</h3>
                        <form id="contact-form" class="space-y-6">
                            <div>
                                <label for="contact-name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" id="contact-name" name="name" placeholder="Enter your full name" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>
                            <div>
                                <label for="contact-email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" id="contact-email" name="email" placeholder="Enter your email address" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>
                            <div>
                                <label for="contact-message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                                <textarea id="contact-message" name="message" placeholder="Enter your message or inquiry" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required></textarea>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md font-medium transition-colors">
                                Send Message
                            </button>
                        </form>
                        <div class="mt-6 text-center text-sm text-gray-500">
                            We typically respond within 24 hours during business days.
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add form submission handler
        section.querySelector('#contact-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = {
                name: formData.get('name'),
                email: formData.get('email'),
                message: formData.get('message')
            };

            const validation = validateForm(data, ['name', 'email', 'message']);
            if (!validation.isValid) {
                Toast.error(validation.message);
                return;
            }

            if (!validateEmail(data.email)) {
                Toast.error('Please enter a valid email address');
                return;
            }

            Toast.success('Message sent successfully! We\'ll get back to you soon.');
            e.target.reset();
        });

        return section;
    }

    createFooter() {
        const footer = createElement('footer', 'bg-gray-900 text-white');
        
        footer.innerHTML = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Barangay Information -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold">BL</span>
                            </div>
                            <span class="text-xl font-bold">BarangayLink</span>
                        </div>
                        <p class="text-gray-300 leading-relaxed">
                            Connecting communities through technology. Your direct link to local government services and community updates.
                        </p>
                        <div class="space-y-2 text-sm text-gray-300">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Barangay Poblacion, Main Street</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-phone"></i>
                                <span>(02) 8123-4567</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-envelope"></i>
                                <span>info@barangaylink.gov.ph</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><button onclick="scrollToSection('home')" class="text-gray-300 hover:text-white transition-colors">Home</button></li>
                            <li><button onclick="scrollToSection('announcements')" class="text-gray-300 hover:text-white transition-colors">Announcements</button></li>
                            <li><button onclick="scrollToSection('events')" class="text-gray-300 hover:text-white transition-colors">Events</button></li>
                            <li><button onclick="scrollToSection('projects')" class="text-gray-300 hover:text-white transition-colors">Projects</button></li>
                            <li><button onclick="scrollToSection('contact')" class="text-gray-300 hover:text-white transition-colors">Contact Us</button></li>
                        </ul>
                    </div>
                    
                    <!-- Services -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Services</h3>
                        <ul class="space-y-2 text-gray-300">
                            <li>Barangay Clearance</li>
                            <li>Certificate of Residency</li>
                            <li>Business Permit</li>
                            <li>Indigency Certificate</li>
                            <li>Community Tax Certificate</li>
                        </ul>
                    </div>
                    
                    <!-- Office Hours & Social -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Office Hours</h3>
                        <div class="text-gray-300 space-y-1 text-sm">
                            <p>Monday - Friday</p>
                            <p>8:00 AM - 5:00 PM</p>
                            <p>Saturday</p>
                            <p>8:00 AM - 12:00 PM</p>
                            <p class="text-red-300">Sunday: Closed</p>
                        </div>
                        <div class="pt-4">
                            <h4 class="text-sm font-semibold mb-3">Follow Us</h4>
                            <div class="flex space-x-3">
                                <button class="w-10 h-10 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center transition-colors">
                                    <i class="fab fa-facebook"></i>
                                </button>
                                <button class="w-10 h-10 bg-blue-400 hover:bg-blue-500 rounded-full flex items-center justify-center transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </button>
                                <button class="w-10 h-10 bg-pink-600 hover:bg-pink-700 rounded-full flex items-center justify-center transition-colors">
                                    <i class="fab fa-instagram"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom Section -->
                <div class="border-t border-gray-800 mt-12 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="text-gray-300 text-sm">
                            © 2024 BarangayLink. All rights reserved. | Government of the Philippines
                        </div>
                        <div class="flex space-x-6 text-sm text-gray-300">
                            <button class="hover:text-white transition-colors">Privacy Policy</button>
                            <button class="hover:text-white transition-colors">Terms of Service</button>
                            <button class="hover:text-white transition-colors">Accessibility</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return footer;
    }

    createDashboardQuickAccess() {
        const quickAccessItems = [
            { icon: 'fas fa-bullhorn', title: 'Announcements', description: 'View latest community updates and important notices', color: 'bg-blue-100 text-blue-600', notifications: 5 },
            { icon: 'fas fa-calendar', title: 'Events', description: 'Discover upcoming community events and activities', color: 'bg-green-100 text-green-600', notifications: 3 },
            { icon: 'fas fa-building', title: 'Projects', description: 'Track progress of ongoing barangay projects', color: 'bg-purple-100 text-purple-600', notifications: 0 },
            { icon: 'fas fa-file-alt', title: 'Request Documents', description: 'Apply for certificates and official documents', color: 'bg-orange-100 text-orange-600', notifications: 0 },
            { icon: 'fas fa-comment-alt', title: 'Submit Concerns', description: 'Report issues or submit feedback to officials', color: 'bg-red-100 text-red-600', notifications: 0 },
            { icon: 'fas fa-credit-card', title: 'Pay Services', description: 'Online payment for barangay fees and services', color: 'bg-indigo-100 text-indigo-600', notifications: 2 }
        ];

        const section = createElement('div', 'mb-8');
        
        const cardsHTML = quickAccessItems.map(item => `
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer group border border-gray-100">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="p-3 rounded-lg ${item.color} group-hover:scale-110 transition-transform duration-300">
                            <i class="${item.icon} text-xl"></i>
                        </div>
                        ${item.notifications > 0 ? `
                            <div class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">${item.notifications}</div>
                        ` : ''}
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                            ${item.title}
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">${item.description}</p>
                    </div>
                    <div class="flex items-center text-blue-600 text-sm font-medium group-hover:text-blue-700">
                        <span>Access Now</span>
                        <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform duration-300"></i>
                    </div>
                </div>
            </div>
        `).join('');

        section.innerHTML = `
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Quick Access</h2>
                <button class="text-blue-600 hover:text-blue-700 font-medium flex items-center space-x-1">
                    <span>View All Services</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                ${cardsHTML}
            </div>
        `;

        return section;
    }

    createDashboardRecentUpdates() {
        const recentUpdates = [
            {
                id: 1, type: 'announcement', title: 'Community Health Program Registration Open',
                content: 'Free health screening and vaccination program for all residents. Registration is now open at the Barangay Health Center.',
                timestamp: '2 hours ago', category: 'Health', likes: 24, comments: 8, priority: 'high'
            },
            {
                id: 2, type: 'event', title: 'Monthly Barangay Assembly Meeting',
                content: 'Join us for our monthly assembly meeting to discuss community projects, budget allocation, and upcoming initiatives.',
                timestamp: '5 hours ago', category: 'Meeting', likes: 15, comments: 3, priority: 'medium'
            },
            {
                id: 3, type: 'project', title: 'Road Improvement Project Update',
                content: 'Phase 2 of the main road rehabilitation is now 65% complete. Expected completion by end of March 2024.',
                timestamp: '1 day ago', category: 'Infrastructure', likes: 32, comments: 12, priority: 'medium'
            },
            {
                id: 4, type: 'announcement', title: 'New Waste Management Schedule',
                content: 'Updated garbage collection schedule effective January 15, 2024. Please check the new pickup days for your area.',
                timestamp: '2 days ago', category: 'Environment', likes: 18, comments: 5, priority: 'low'
            }
        ];

        const getTypeColor = (type) => {
            switch (type) {
                case 'announcement': return 'bg-blue-100 text-blue-800';
                case 'event': return 'bg-green-100 text-green-800';
                case 'project': return 'bg-purple-100 text-purple-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        };

        const getCategoryColor = (category) => {
            switch (category) {
                case 'Health': return 'bg-red-100 text-red-800';
                case 'Meeting': return 'bg-blue-100 text-blue-800';
                case 'Infrastructure': return 'bg-gray-100 text-gray-800';
                case 'Environment': return 'bg-green-100 text-green-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        };

        const section = createElement('div', 'mb-8');
        
        const updatesHTML = recentUpdates.map(update => `
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${getTypeColor(update.type)}">
                                ${update.type}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${getCategoryColor(update.category)}">
                                ${update.category}
                            </span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            ${update.timestamp}
                        </div>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 cursor-pointer">
                            ${update.title}
                        </h3>
                        <p class="text-gray-600 leading-relaxed">${update.content}</p>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-6">
                            <button class="flex items-center space-x-1 text-gray-500 hover:text-red-500 transition-colors">
                                <i class="fas fa-heart"></i>
                                <span class="text-sm">${update.likes}</span>
                            </button>
                            <button class="flex items-center space-x-1 text-gray-500 hover:text-blue-500 transition-colors">
                                <i class="fas fa-comment"></i>
                                <span class="text-sm">${update.comments}</span>
                            </button>
                            <button class="flex items-center space-x-1 text-gray-500 hover:text-green-500 transition-colors">
                                <i class="fas fa-share"></i>
                                <span class="text-sm">Share</span>
                            </button>
                        </div>
                        <button class="text-blue-600 hover:text-blue-700 font-medium text-sm">Read More</button>
                    </div>
                </div>
            </div>
        `).join('');

        section.innerHTML = `
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Recent Updates</h2>
                <button class="text-blue-600 hover:text-blue-700 font-medium">View All Updates</button>
            </div>
            <div class="space-y-4">
                ${updatesHTML}
            </div>
            <div class="text-center mt-6">
                <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Load More Updates
                </button>
            </div>
        `;

        return section;
    }

    renderAuthModal(container) {
        const modal = createElement('div', 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4');
        modal.id = 'auth-modal';
        
        modal.innerHTML = `
            <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome to BarangayLink</h2>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Tabs -->
                <div class="flex border-b">
                    <button id="login-tab" class="flex-1 py-3 px-4 text-sm font-medium transition-colors text-blue-600 border-b-2 border-blue-600 bg-blue-50">
                        Login
                    </button>
                    <button id="signup-tab" class="flex-1 py-3 px-4 text-sm font-medium transition-colors text-gray-500 hover:text-gray-700">
                        Sign Up
                    </button>
                    <button id="reset-tab" class="flex-1 py-3 px-4 text-sm font-medium transition-colors text-gray-500 hover:text-gray-700">
                        Reset Password
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Login Form -->
                    <div id="login-content">
                        <form id="login-form" class="space-y-4">
                            <div>
                                <label for="login-email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="login-email" name="email" placeholder="Enter your email" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>
                            <div>
                                <label for="login-password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <input type="password" id="login-password" name="password" placeholder="Enter your password" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <button type="button" id="toggle-login-password" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" id="remember-me" class="text-blue-600">
                                    <span class="text-sm text-gray-600">Remember me</span>
                                </label>
                                <button type="button" id="forgot-password" class="text-sm text-blue-600 hover:text-blue-700">Forgot Password?</button>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-medium transition-colors">
                                Login
                            </button>
                        </form>
                    </div>

                    <!-- Signup Form -->
                    <div id="signup-content" class="hidden">
                        <form id="signup-form" class="space-y-4">
                            <div>
                                <label for="signup-name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" id="signup-name" name="fullName" placeholder="Enter your full name" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>
                            <div>
                                <label for="signup-email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="signup-email" name="email" placeholder="Enter your email" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>
                            <div>
                                <label for="signup-password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <input type="password" id="signup-password" name="password" placeholder="Create a password" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <button type="button" id="toggle-signup-password" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="signup-confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" id="signup-confirm-password" name="confirmPassword" placeholder="Confirm your password" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <button type="button" id="toggle-signup-confirm-password" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">Password must be at least 8 characters long</div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-medium transition-colors">
                                Create Account
                            </button>
                        </form>
                    </div>

                    <!-- Reset Form -->
                    <div id="reset-content" class="hidden">
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Reset Your Password</h3>
                            <p class="text-sm text-gray-600">Enter your email address and we'll send you a link to reset your password.</p>
                        </div>
                        <form id="reset-form" class="space-y-4">
                            <div>
                                <label for="reset-email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="reset-email" name="email" placeholder="Enter your email" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-medium transition-colors">
                                Send Reset Link
                            </button>
                            <div class="text-center">
                                <button type="button" id="back-to-login" class="text-sm text-blue-600 hover:text-blue-700">Back to Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(modal);

        // Add event listeners for the modal
        this.setupAuthModalEventListeners();
    }

 setupAuthModalEventListeners = function () {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const resetForm = document.getElementById('reset-form');

    const loginTab = document.getElementById('login-tab');
    const signupTab = document.getElementById('signup-tab');
    const resetTab = document.getElementById('reset-tab');

    const loginContent = document.getElementById('login-content');
    const signupContent = document.getElementById('signup-content');
    const resetContent = document.getElementById('reset-content');

    // Tab switching
    function switchTab(active) {
        loginContent.classList.add('hidden');
        signupContent.classList.add('hidden');
        resetContent.classList.add('hidden');
        if (active === 'login') loginContent.classList.remove('hidden');
        if (active === 'signup') signupContent.classList.remove('hidden');
        if (active === 'reset') resetContent.classList.remove('hidden');
    }

    loginTab.addEventListener('click', () => switchTab('login'));
    signupTab.addEventListener('click', () => switchTab('signup'));
    resetTab.addEventListener('click', () => switchTab('reset'));
    document.getElementById('back-to-login').addEventListener('click', () => switchTab('login'));

    // Close modal
    document.getElementById('close-modal').addEventListener('click', () => {
        document.getElementById('auth-modal').remove();
    });

    // Helpers
    function showError(message) {
        alert(message); // you can swap this for Toast.error()
    }

    function getUsers() {
        return JSON.parse(localStorage.getItem('users')) || [];
    }

    function saveUsers(users) {
        localStorage.setItem('users', JSON.stringify(users));
    }

    // --- SIGNUP VALIDATION ---
    signupForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const fullName = document.getElementById('signup-name').value.trim();
        const email = document.getElementById('signup-email').value.trim();
        const password = document.getElementById('signup-password').value.trim();
        const confirmPassword = document.getElementById('signup-confirm-password').value.trim();

        if (!fullName || !email || !password || !confirmPassword) {
            return showError('All fields are required.');
        }
        if (password.length < 8) {
            return showError('Password must be at least 8 characters long.');
        }
        if (password !== confirmPassword) {
            return showError('Passwords do not match.');
        }

        let users = getUsers();
        if (users.some(u => u.email === email)) {
            return showError('This email is already registered.');
        }

        users.push({ fullName, email, password });
        saveUsers(users);

        alert('Account created successfully! Please login.');
        switchTab('login');
    });

    // --- LOGIN VALIDATION ---
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('login-email').value.trim();
        const password = document.getElementById('login-password').value.trim();

        if (!email || !password) {
            return showError('Please enter both email and password.');
        }

        const users = getUsers();
        if (users.length === 0) {
            return showError('No registered users found. Please sign up first.');
        }

        const user = users.find(u => u.email === email && u.password === password);
        if (!user) {
            return showError('Invalid email or password.');
        }

        localStorage.setItem('loggedInUser', JSON.stringify(user));
        alert(`Welcome ${user.fullName}, you are now logged in!`);
        document.getElementById('auth-modal').remove();
        // redirect to dashboard page if needed
        // window.location.href = '/dashboard.html';
    });

    // --- RESET VALIDATION ---
    resetForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('reset-email').value.trim();

        if (!email) {
            return showError('Please enter your email.');
        }

        const users = getUsers();
        if (users.length === 0) {
            return showError('No registered users. Please sign up first.');
        }

        const user = users.find(u => u.email === email);
        if (!user) {
            return showError('No account found with this email. Please sign up first.');
        }

        alert(`Password reset link sent to ${email} (simulation).`);
        switchTab('login');
    });
};


    setupGlobalEventListeners() {
        // Make scrollToSection available globally
        window.scrollToSection = scrollToSection;
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new BarangayLinkApp();
});