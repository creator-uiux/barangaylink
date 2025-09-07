// Utility functions for the BarangayLink application

// Toast notification system
const Toast = {
    show: (message, type = 'info') => {
        const toastContainer = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const bgColor = type === 'success' ? 'bg-green-500' : 
                       type === 'error' ? 'bg-red-500' : 
                       type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
        
        toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        toast.textContent = message;
        
        toastContainer.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toastContainer.contains(toast)) {
                    toastContainer.removeChild(toast);
                }
            }, 300);
        }, 3000);
    },
    
    success: (message) => Toast.show(message, 'success'),
    error: (message) => Toast.show(message, 'error'),
    warning: (message) => Toast.show(message, 'warning'),
    info: (message) => Toast.show(message, 'info')
};

// DOM utility functions
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);

const createElement = (tag, className = '', content = '') => {
    const element = document.createElement(tag);
    if (className) element.className = className;
    if (content) element.innerHTML = content;
    return element;
};

const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

// Form validation utilities
const validateEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

const validateForm = (formData, requiredFields) => {
    for (let field of requiredFields) {
        if (!formData[field] || formData[field].trim() === '') {
            return { isValid: false, message: `Please fill in ${field.replace(/([A-Z])/g, ' $1').toLowerCase()}` };
        }
    }
    return { isValid: true, message: '' };
};

// Icon utility (using Font Awesome classes)
const getIcon = (iconName) => {
    const iconMap = {
        'menu': 'fas fa-bars',
        'x': 'fas fa-times',
        'user': 'fas fa-user',
        'logout': 'fas fa-sign-out-alt',
        'bell': 'fas fa-bell',
        'eye': 'fas fa-eye',
        'eye-off': 'fas fa-eye-slash',
        'arrow-right': 'fas fa-arrow-right',
        'users': 'fas fa-users',
        'clock': 'fas fa-clock',
        'map-pin': 'fas fa-map-marker-alt',
        'phone': 'fas fa-phone',
        'mail': 'fas fa-envelope',
        'calendar': 'fas fa-calendar',
        'megaphone': 'fas fa-bullhorn',
        'building': 'fas fa-building',
        'file-text': 'fas fa-file-alt',
        'message-square': 'fas fa-comment-alt',
        'credit-card': 'fas fa-credit-card',
        'activity': 'fas fa-chart-line',
        'check-circle': 'fas fa-check-circle',
        'user-plus': 'fas fa-user-plus',
        'log-in': 'fas fa-sign-in-alt',
        'heart': 'fas fa-heart',
        'message-circle': 'fas fa-comment',
        'share': 'fas fa-share',
        'facebook': 'fab fa-facebook',
        'twitter': 'fab fa-twitter',
        'instagram': 'fab fa-instagram'
    };
    return iconMap[iconName] || 'fas fa-circle';
};

// Date formatting utility
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return '1 day ago';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
    return `${Math.ceil(diffDays / 30)} months ago`;
};