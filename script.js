// BarangayLink JavaScript - Authentication and App Logic

// Global State Management
const AppState = {
  currentUser: null,
  isLoading: true,
  currentPage: 'landing',
  modals: {
    auth: false,
    document: false,
    concern: false
  }
};

// Utility Functions
function showToast(type, message, duration = 5000) {
  const container = document.getElementById('toast-container');
  const toastId = 'toast-' + Date.now();
  
  const iconMap = {
    success: 'check-circle',
    error: 'alert-circle',
    info: 'info'
  };
  
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.id = toastId;
  toast.innerHTML = `
    <i data-feather="${iconMap[type]}" class="toast-icon"></i>
    <div class="toast-content">${message}</div>
    <button class="toast-close" onclick="closeToast('${toastId}')">
      <i data-feather="x"></i>
    </button>
  `;
  
  container.appendChild(toast);
  feather.replace();
  
  // Auto-remove toast
  setTimeout(() => {
    closeToast(toastId);
  }, duration);
}

function closeToast(toastId) {
  const toast = document.getElementById(toastId);
  if (toast) {
    toast.classList.add('removing');
    setTimeout(() => {
      toast.remove();
    }, 300);
  }
}

function scrollToSection(sectionId) {
  const element = document.getElementById(sectionId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
  // Close mobile menu if open
  const mobileMenu = document.getElementById('mobile-menu');
  if (mobileMenu.classList.contains('show')) {
    toggleMobileMenu();
  }
}

function toggleMobileMenu() {
  const mobileMenu = document.getElementById('mobile-menu');
  const button = document.querySelector('.mobile-menu-btn');
  
  mobileMenu.classList.toggle('show');
  button.classList.toggle('active');
}

function toggleDashboardMobileMenu() {
  const mobileMenu = document.getElementById('dashboard-mobile-menu');
  const button = document.querySelector('.dashboard-nav .mobile-menu-btn');
  
  mobileMenu.classList.toggle('show');
  button.classList.toggle('active');
}

function setLoading(elementId, isLoading) {
  const element = document.getElementById(elementId);
  if (element) {
    if (isLoading) {
      element.classList.add('loading');
      element.disabled = true;
    } else {
      element.classList.remove('loading');
      element.disabled = false;
    }
  }
}

// Local Storage Utilities
function getUsers() {
  try {
    return JSON.parse(localStorage.getItem('barangaylink_users') || '[]');
  } catch {
    return [];
  }
}

function saveUsers(users) {
  localStorage.setItem('barangaylink_users', JSON.stringify(users));
}

function getCurrentUser() {
  const storedUser = localStorage.getItem('barangaylink_current_user') || 
                   sessionStorage.getItem('barangaylink_current_user');
  
  if (storedUser) {
    try {
      return JSON.parse(storedUser);
    } catch (error) {
      console.error('Error parsing stored user:', error);
    }
  }
  return null;
}

function setCurrentUser(user, rememberMe = false) {
  const userToStore = {
    id: user.id,
    fullName: user.fullName,
    email: user.email,
    createdAt: user.createdAt
  };

  AppState.currentUser = userToStore;

  if (rememberMe) {
    localStorage.setItem('barangaylink_current_user', JSON.stringify(userToStore));
  } else {
    sessionStorage.setItem('barangaylink_current_user', JSON.stringify(userToStore));
  }
}

function clearCurrentUser() {
  AppState.currentUser = null;
  localStorage.removeItem('barangaylink_current_user');
  sessionStorage.removeItem('barangaylink_current_user');
}

// Authentication Functions
async function login(email, password, rememberMe = false) {
  const users = getUsers();
  const foundUser = users.find(u => u.email === email && u.password === password);

  if (!foundUser) {
    return {
      success: false,
      message: 'Invalid email or password. Please sign up if you don\'t have an account.'
    };
  }

  setCurrentUser(foundUser, rememberMe);
  return { success: true, message: 'Login successful!' };
}

async function signup(fullName, email, password) {
  const users = getUsers();

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
    createdAt: new Date().toISOString()
  };

  users.push(newUser);
  saveUsers(users);

  return { success: true, message: 'Account created successfully! Please login.' };
}

async function resetPassword(email) {
  const users = getUsers();
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

function logout() {
  clearCurrentUser();
  showCurrentPage('landing');
  showToast('success', 'Logged out successfully');
}

// Page Management
function showCurrentPage(page) {
  AppState.currentPage = page;
  
  const landingPage = document.getElementById('landing-page');
  const dashboardPage = document.getElementById('dashboard-page');
  
  if (page === 'landing') {
    landingPage.style.display = 'block';
    dashboardPage.style.display = 'none';
  } else if (page === 'dashboard') {
    landingPage.style.display = 'none';
    dashboardPage.style.display = 'block';
    updateDashboardUserInfo();
  }
}

function updateDashboardUserInfo() {
  if (AppState.currentUser) {
    const firstName = AppState.currentUser.fullName.split(' ')[0];
    document.getElementById('user-welcome').textContent = `Welcome, ${firstName}`;
    document.getElementById('welcome-message').textContent = `Welcome, ${firstName}!`;
  }
}

// Modal Management
function openAuthModal() {
  AppState.modals.auth = true;
  const modal = document.getElementById('auth-modal');
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeAuthModal() {
  AppState.modals.auth = false;
  const modal = document.getElementById('auth-modal');
  modal.classList.remove('show');
  document.body.style.overflow = '';
  // Reset forms
  document.getElementById('login-form').reset();
  document.getElementById('signup-form').reset();
  document.getElementById('reset-form').reset();
}

function openDocumentModal() {
  AppState.modals.document = true;
  const modal = document.getElementById('document-modal');
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeDocumentModal() {
  AppState.modals.document = false;
  const modal = document.getElementById('document-modal');
  modal.classList.remove('show');
  document.body.style.overflow = '';
  document.getElementById('document-form').reset();
}

function openConcernModal() {
  AppState.modals.concern = true;
  const modal = document.getElementById('concern-modal');
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeConcernModal() {
  AppState.modals.concern = false;
  const modal = document.getElementById('concern-modal');
  modal.classList.remove('show');
  document.body.style.overflow = '';
  document.getElementById('concern-form').reset();
}

// Tab Management
function switchAuthTab(tabName) {
  // Update tab triggers
  document.querySelectorAll('.tab-trigger').forEach(trigger => {
    trigger.classList.remove('active');
  });
  document.querySelector(`[onclick="switchAuthTab('${tabName}')"]`).classList.add('active');
  
  // Update tab content
  document.querySelectorAll('.tab-content').forEach(content => {
    content.classList.remove('active');
  });
  document.getElementById(`${tabName}-tab`).classList.add('active');
}

// Form Handlers
async function handleLogin(e) {
  e.preventDefault();
  setLoading('login-submit', true);

  const formData = new FormData(e.target);
  const email = formData.get('email');
  const password = formData.get('password');
  const rememberMe = formData.get('rememberMe') === 'on';

  // Simulate network delay
  await new Promise(resolve => setTimeout(resolve, 1000));

  const result = await login(email, password, rememberMe);
  
  if (result.success) {
    showToast('success', result.message);
    closeAuthModal();
    showCurrentPage('dashboard');
  } else {
    showToast('error', result.message);
  }
  
  setLoading('login-submit', false);
}

async function handleSignup(e) {
  e.preventDefault();
  setLoading('signup-submit', true);

  const formData = new FormData(e.target);
  const fullName = formData.get('fullName');
  const email = formData.get('email');
  const password = formData.get('password');
  const confirmPassword = formData.get('confirmPassword');

  // Validation
  if (password.length < 6) {
    showToast('error', 'Password must be at least 6 characters long');
    setLoading('signup-submit', false);
    return;
  }

  if (password !== confirmPassword) {
    showToast('error', 'Passwords do not match');
    setLoading('signup-submit', false);
    return;
  }

  // Simulate network delay
  await new Promise(resolve => setTimeout(resolve, 1000));

  const result = await signup(fullName, email, password);
  
  if (result.success) {
    showToast('success', result.message);
    e.target.reset();
    switchAuthTab('login');
  } else {
    showToast('error', result.message);
  }
  
  setLoading('signup-submit', false);
}

async function handleReset(e) {
  e.preventDefault();
  setLoading('reset-submit', true);

  const formData = new FormData(e.target);
  const email = formData.get('email');

  // Simulate network delay
  await new Promise(resolve => setTimeout(resolve, 1000));

  const result = await resetPassword(email);
  
  if (result.success) {
    showToast('success', result.message);
    e.target.reset();
  } else {
    showToast('error', result.message);
  }
  
  setLoading('reset-submit', false);
}

async function handleContactForm(e) {
  e.preventDefault();
  setLoading('contact-submit', true);

  // Simulate form submission
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  showToast('success', 'Thank you for your message! We will get back to you soon.');
  e.target.reset();
  setLoading('contact-submit', false);
}

async function handleDocumentRequest(e) {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  const documentType = formData.get('documentType');
  const purpose = formData.get('purpose');
  const contactNumber = formData.get('contactNumber');
  const additionalNotes = formData.get('additionalNotes');
  
  if (!documentType || !purpose || !contactNumber) {
    showToast('error', 'Please fill in all required fields');
    return;
  }

  setLoading('document-submit', true);

  // Simulate API request
  await new Promise(resolve => setTimeout(resolve, 1500));

  // Store request in localStorage
  const request = {
    id: Date.now(),
    userId: AppState.currentUser?.id,
    documentType,
    purpose,
    contactNumber,
    additionalNotes,
    status: 'pending',
    submittedAt: new Date().toISOString()
  };

  const requests = JSON.parse(localStorage.getItem('barangaylink_requests') || '[]');
  requests.push(request);
  localStorage.setItem('barangaylink_requests', JSON.stringify(requests));

  showToast('success', 'Document request submitted successfully! You will be notified when it\'s ready.');
  
  setLoading('document-submit', false);
  closeDocumentModal();
}

async function handleConcernSubmission(e) {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  const concernType = formData.get('concernType');
  const concernTitle = formData.get('concernTitle');
  const concernDescription = formData.get('concernDescription');
  const concernLocation = formData.get('concernLocation');
  const urgencyLevel = formData.get('urgencyLevel');
  
  if (!concernType || !concernTitle || !concernDescription) {
    showToast('error', 'Please fill in all required fields');
    return;
  }

  setLoading('concern-submit', true);

  // Simulate API request
  await new Promise(resolve => setTimeout(resolve, 1500));

  // Store concern in localStorage
  const concern = {
    id: Date.now(),
    userId: AppState.currentUser?.id,
    concernType,
    concernTitle,
    concernDescription,
    concernLocation,
    urgencyLevel,
    status: 'submitted',
    submittedAt: new Date().toISOString()
  };

  const concerns = JSON.parse(localStorage.getItem('barangaylink_concerns') || '[]');
  concerns.push(concern);
  localStorage.setItem('barangaylink_concerns', JSON.stringify(concerns));

  showToast('success', 'Concern submitted successfully! We will review it and take appropriate action.');
  
  setLoading('concern-submit', false);
  closeConcernModal();
}

// Event Listeners Setup
function setupEventListeners() {
  // Authentication forms
  document.getElementById('login-form').addEventListener('submit', handleLogin);
  document.getElementById('signup-form').addEventListener('submit', handleSignup);
  document.getElementById('reset-form').addEventListener('submit', handleReset);
  
  // Contact form
  document.getElementById('contact-form').addEventListener('submit', handleContactForm);
  
  // Document request form
  document.getElementById('document-form').addEventListener('submit', handleDocumentRequest);
  
  // Concern form
  document.getElementById('concern-form').addEventListener('submit', handleConcernSubmission);

  // Close modals on backdrop click
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
      if (AppState.modals.auth) closeAuthModal();
      if (AppState.modals.document) closeDocumentModal();
      if (AppState.modals.concern) closeConcernModal();
    }
  });

  // Close modals on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if (AppState.modals.auth) closeAuthModal();
      if (AppState.modals.document) closeDocumentModal();
      if (AppState.modals.concern) closeConcernModal();
    }
  });

  // Close mobile menu when clicking outside
  document.addEventListener('click', function(e) {
    const mobileMenu = document.getElementById('mobile-menu');
    const dashboardMobileMenu = document.getElementById('dashboard-mobile-menu');
    const mobileButton = document.querySelector('.mobile-menu-btn');
    const dashboardMobileButton = document.querySelector('.dashboard-nav .mobile-menu-btn');
    
    if (mobileMenu && mobileMenu.classList.contains('show') && 
        !mobileMenu.contains(e.target) && 
        !mobileButton.contains(e.target)) {
      toggleMobileMenu();
    }
    
    if (dashboardMobileMenu && dashboardMobileMenu.classList.contains('show') && 
        !dashboardMobileMenu.contains(e.target) && 
        !dashboardMobileButton.contains(e.target)) {
      toggleDashboardMobileMenu();
    }
  });
}

// Navigation Functions
function navigateToDashboard() {
  showCurrentPage('dashboard');
}

function navigateToLanding() {
  showCurrentPage('landing');
}

// Auth Success Handler
function onAuthSuccess() {
  showCurrentPage('dashboard');
}

// App Initialization
function initializeApp() {
  AppState.isLoading = true;
  
  // Show loading screen initially
  const loadingScreen = document.getElementById('loading');
  loadingScreen.style.display = 'flex';

  // Check for stored user session
  const storedUser = getCurrentUser();
  
  if (storedUser) {
    AppState.currentUser = storedUser;
    showCurrentPage('dashboard');
  } else {
    showCurrentPage('landing');
  }
  
  // Setup event listeners
  setupEventListeners();
  
  // Hide loading screen
  AppState.isLoading = false;
  setTimeout(() => {
    loadingScreen.classList.add('hidden');
    setTimeout(() => {
      loadingScreen.style.display = 'none';
    }, 300);
  }, 1000);

  // Replace feather icons
  feather.replace();
}

// Utility function to format dates
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

// Mock data for development
function loadMockData() {
  // Only load if no users exist
  const users = getUsers();
  if (users.length === 0) {
    const mockUsers = [
      {
        id: 1,
        fullName: 'Juan Dela Cruz',
        email: 'juan@example.com',
        password: 'password123',
        createdAt: new Date().toISOString()
      },
      {
        id: 2,
        fullName: 'Maria Santos',
        email: 'maria@example.com',
        password: 'password123',
        createdAt: new Date().toISOString()
      }
    ];
    saveUsers(mockUsers);
  }
}

// Load mock data for demo purposes
loadMockData();

// Smooth scrolling polyfill for older browsers
function smoothScrollPolyfill() {
  if (!('scrollBehavior' in document.documentElement.style)) {
    // Polyfill for smooth scrolling
    const scrollToElement = (element, duration = 1000) => {
      const targetPosition = element.offsetTop - 64; // Account for fixed nav
      const startPosition = window.pageYOffset;
      const distance = targetPosition - startPosition;
      let startTime = null;

      function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = ease(timeElapsed, startPosition, distance, duration);
        window.scrollTo(0, run);
        if (timeElapsed < duration) requestAnimationFrame(animation);
      }

      function ease(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
      }

      requestAnimationFrame(animation);
    };

    // Override the scrollToSection function
    const originalScrollToSection = window.scrollToSection;
    window.scrollToSection = function(sectionId) {
      const element = document.getElementById(sectionId);
      if (element) {
        scrollToElement(element);
      }
      // Close mobile menu if open
      const mobileMenu = document.getElementById('mobile-menu');
      if (mobileMenu && mobileMenu.classList.contains('show')) {
        toggleMobileMenu();
      }
    };
  }
}

// Initialize smooth scrolling polyfill
smoothScrollPolyfill();

// Accessibility improvements
function enhanceAccessibility() {
  // Add aria-labels to buttons without text
  document.querySelectorAll('button[data-feather]').forEach(button => {
    if (!button.textContent.trim()) {
      const icon = button.getAttribute('data-feather');
      button.setAttribute('aria-label', icon.replace('-', ' '));
    }
  });

  // Add role and aria-expanded to mobile menu buttons
  document.querySelectorAll('.mobile-menu-btn').forEach(button => {
    button.setAttribute('role', 'button');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-label', 'Toggle mobile menu');
  });

  // Update aria-expanded when mobile menu is toggled
  const originalToggleMobileMenu = window.toggleMobileMenu;
  window.toggleMobileMenu = function() {
    const mobileMenu = document.getElementById('mobile-menu');
    const button = document.querySelector('.mobile-menu-btn');
    const isOpen = mobileMenu.classList.contains('show');
    
    originalToggleMobileMenu();
    
    button.setAttribute('aria-expanded', !isOpen);
  };

  const originalToggleDashboardMobileMenu = window.toggleDashboardMobileMenu;
  window.toggleDashboardMobileMenu = function() {
    const mobileMenu = document.getElementById('dashboard-mobile-menu');
    const button = document.querySelector('.dashboard-nav .mobile-menu-btn');
    const isOpen = mobileMenu.classList.contains('show');
    
    originalToggleDashboardMobileMenu();
    
    button.setAttribute('aria-expanded', !isOpen);
  };
}

// Initialize accessibility enhancements after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(enhanceAccessibility, 100);
});

// Service Worker registration for PWA capabilities (optional)
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    // Uncomment to enable service worker
    // navigator.serviceWorker.register('/sw.js')
    //   .then(function(registration) {
    //     console.log('ServiceWorker registration successful');
    //   })
    //   .catch(function(error) {
    //     console.log('ServiceWorker registration failed');
    //   });
  });
}

// Export functions for global access
window.AppState = AppState;
window.showToast = showToast;
window.scrollToSection = scrollToSection;
window.toggleMobileMenu = toggleMobileMenu;
window.toggleDashboardMobileMenu = toggleDashboardMobileMenu;
window.openAuthModal = openAuthModal;
window.closeAuthModal = closeAuthModal;
window.openDocumentModal = openDocumentModal;
window.closeDocumentModal = closeDocumentModal;
window.openConcernModal = openConcernModal;
window.closeConcernModal = closeConcernModal;
window.switchAuthTab = switchAuthTab;
window.logout = logout;
window.initializeApp = initializeApp;
window.closeToast = closeToast;