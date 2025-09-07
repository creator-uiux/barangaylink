// Component functions for BarangayLink

// Mock data
const mockAnnouncements = [
    {
        id: 1,
        title: "Community Health and Wellness Program",
        description: "Free health screening and vaccination drive for all residents. Bring your health cards and IDs.",
        date: "2024-01-15",
        category: "Health",
        priority: "high",
        attendees: 150
    },
    {
        id: 2,
        title: "Barangay Assembly Meeting",
        description: "Monthly assembly to discuss community projects, budget allocation, and upcoming initiatives.",
        date: "2024-01-20",
        category: "Meeting",
        priority: "medium",
        attendees: 85
    },
    {
        id: 3,
        title: "Youth Skills Development Workshop",
        description: "Digital literacy and entrepreneurship workshop for young adults aged 18-25.",
        date: "2024-01-25",
        category: "Education",
        priority: "medium",
        attendees: 60
    },
    {
        id: 4,
        title: "Emergency Preparedness Seminar",
        description: "Learn essential emergency response procedures and disaster preparedness strategies.",
        date: "2024-01-30",
        category: "Safety",
        priority: "high",
        attendees: 120
    }
];

const mockEvents = [
    {
        id: 1,
        title: "Community Clean-Up Drive",
        description: "Join us in keeping our barangay clean and green. Bring your own cleaning materials.",
        date: "2024-02-10",
        time: "7:00 AM - 11:00 AM",
        location: "Barangay Hall",
        attendees: 75,
        maxAttendees: 100
    },
    {
        id: 2,
        title: "Senior Citizens' Health Check",
        description: "Free health screening exclusively for senior citizens. Registration required.",
        date: "2024-02-15",
        time: "8:00 AM - 4:00 PM",
        location: "Community Health Center",
        attendees: 45,
        maxAttendees: 60
    },
    {
        id: 3,
        title: "Skills Training Workshop",
        description: "Learn new skills in digital marketing, basic computer literacy, and entrepreneurship.",
        date: "2024-02-20",
        time: "1:00 PM - 5:00 PM",
        location: "Multipurpose Hall",
        attendees: 32,
        maxAttendees: 40
    }
];

const mockProjects = [
    {
        id: 1,
        title: "Road Improvement Project",
        description: "Rehabilitation of main roads and installation of proper drainage systems.",
        status: "in-progress",
        progress: 65,
        budget: "₱2,500,000",
        startDate: "2023-11-01",
        expectedCompletion: "2024-03-30"
    },
    {
        id: 2,
        title: "Basketball Court Renovation",
        description: "Complete renovation of the community basketball court with new equipment.",
        status: "completed",
        progress: 100,
        budget: "₱850,000",
        startDate: "2023-09-15",
        expectedCompletion: "2023-12-20"
    },
    {
        id: 3,
        title: "Street Lighting Installation",
        description: "Installation of LED street lights throughout the main thoroughfares.",
        status: "planning",
        progress: 15,
        budget: "₱1,200,000",
        startDate: "2024-01-15",
        expectedCompletion: "2024-06-30"
    },
    {
        id: 4,
        title: "Community Garden Project",
        description: "Establishing a community vegetable garden for sustainable food production.",
        status: "in-progress",
        progress: 40,
        budget: "₱300,000",
        startDate: "2023-12-01",
        expectedCompletion: "2024-04-15"
    }
];

// Navigation Components
const createLandingNavbar = (onOpenAuthModal) => {
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
                    <button onclick="scrollToSection('home')" class="text-gray-700 hover:text-blue-600 transition-colors">Home</button>
                    <button onclick="scrollToSection('announcements')" class="text-gray-700 hover:text-blue-600 transition-colors">Announcements</button>
                    <button onclick="scrollToSection('events')" class="text-gray-700 hover:text-blue-600 transition-colors">Events</button>
                    <button onclick="scrollToSection('projects')" class="text-gray-700 hover:text-blue-600 transition-colors">Projects</button>
                    <button onclick="scrollToSection('contact')" class="text-gray-700 hover:text-blue-600 transition-colors">Contact Us</button>
                </div>

                <!-- Desktop Login Button -->
                <div class="hidden md:block">
                    <button id="desktop-login-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        Login / Signup
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden bg-white border-t hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <button onclick="scrollToSection('home')" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Home</button>
                    <button onclick="scrollToSection('announcements')" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Announcements</button>
                    <button onclick="scrollToSection('events')" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Events</button>
                    <button onclick="scrollToSection('projects')" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Projects</button>
                    <button onclick="scrollToSection('contact')" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Contact Us</button>
                    <div class="px-3 py-2">
                        <button id="mobile-login-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                            Login / Signup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add event listeners
    const desktopLoginBtn = navbar.querySelector('#desktop-login-btn');
    const mobileLoginBtn = navbar.querySelector('#mobile-login-btn');
    const mobileMenuBtn = navbar.querySelector('#mobile-menu-btn');
    const mobileMenu = navbar.querySelector('#mobile-menu');
    
    desktopLoginBtn.addEventListener('click', onOpenAuthModal);
    mobileLoginBtn.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
        onOpenAuthModal();
    });
    
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
};

// Hero Section
const createHero = (onGetStarted, onViewAnnouncements) => {
    const hero = createElement('section', 'pt-16 bg-gradient-to-br from-blue-50 to-white min-h-screen flex items-center');
    hero.id = 'home';
    
    hero.innerHTML = `
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                            BarangayLink
                            <span class="block text-blue-600">
                                Your Direct Link to Local Updates and Services
                            </span>
                        </h1>
                        <p class="text-lg md:text-xl text-gray-600 max-w-2xl">
                            Stay connected with your community. Get real-time announcements, 
                            event updates, and access essential barangay services all in one place.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button id="get-started-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-6 text-lg rounded-md transition-colors flex items-center justify-center space-x-2">
                            <span>Get Started</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button id="view-announcements-btn" class="border border-blue-600 text-blue-600 hover:bg-blue-50 px-8 py-6 text-lg rounded-md transition-colors">
                            View Announcements
                        </button>
                    </div>

                    <div class="flex items-center space-x-6 pt-8">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-users text-blue-600"></i>
                            <span class="text-gray-600">1,500+ Active Residents</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Trusted by communities nationwide
                        </div>
                    </div>
                </div>

                <!-- Hero Visual -->
                <div class="relative">
                    <div class="bg-blue-600 rounded-2xl p-8 shadow-2xl">
                        <div class="bg-white rounded-xl p-6 space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Live Updates</span>
                            </div>
                            <div class="space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-blue-800">New Announcement</div>
                                    <div class="text-xs text-blue-600">Community Health Program</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-green-800">Upcoming Event</div>
                                    <div class="text-xs text-green-600">Barangay Assembly Meeting</div>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-orange-800">Project Update</div>
                                    <div class="text-xs text-orange-600">Road Improvement Status</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating elements -->
                    <div class="absolute -top-4 -right-4 bg-white p-4 rounded-full shadow-lg">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add event listeners
    hero.querySelector('#get-started-btn').addEventListener('click', onGetStarted);
    hero.querySelector('#view-announcements-btn').addEventListener('click', onViewAnnouncements);
    
    return hero;
};

// Announcements Preview
const createAnnouncementsPreview = () => {
    const section = createElement('section', 'py-20 bg-gray-50');
    section.id = 'announcements';
    
    const getPriorityColor = (priority) => {
        switch (priority) {
            case 'high': return 'bg-red-100 text-red-800 border-red-200';
            case 'medium': return 'bg-orange-100 text-orange-800 border-orange-200';
            default: return 'bg-gray-100 text-gray-800 border-gray-200';
        }
    };

    const getCategoryColor = (category) => {
        switch (category) {
            case 'Health': return 'bg-green-100 text-green-800';
            case 'Meeting': return 'bg-blue-100 text-blue-800';
            case 'Education': return 'bg-purple-100 text-purple-800';
            case 'Safety': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };
    
    let cardsHTML = '';
    mockAnnouncements.forEach(announcement => {
        cardsHTML += `
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${getCategoryColor(announcement.category)}">
                                ${announcement.category}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${getPriorityColor(announcement.priority)}">
                                ${announcement.priority} priority
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-semibold text-gray-900">
                            ${announcement.title}
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            ${announcement.description}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-clock"></i>
                                <span>${formatDate(announcement.date)}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-users"></i>
                                <span>${announcement.attendees} interested</span>
                            </div>
                        </div>
                        <button class="text-blue-600 hover:text-blue-700 font-medium">
                            Read More
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    section.innerHTML = `
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Latest Announcements
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Stay updated with the latest news, events, and important information from your barangay.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8">
                ${cardsHTML}
            </div>

            <div class="text-center mt-12">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    View All Announcements
                </button>
            </div>
        </div>
    `;
    
    return section;
};