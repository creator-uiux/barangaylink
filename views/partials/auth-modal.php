<?php
// Authentication Modal Component
// SYNCHRONIZED with components/AuthModal.tsx
?>
<div id="auth-modal" class="fixed inset-0 bg-gradient-to-br from-blue-900/80 via-purple-900/80 to-indigo-900/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 animate-fade-in">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 rounded-2xl overflow-hidden">
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-20 -left-20 w-32 h-32 bg-gradient-to-br from-indigo-400/20 to-pink-400/20 rounded-full blur-2xl"></div>
        </div>

        <div class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 px-8 py-6 text-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Dynamic Icon Based on Form -->
                    <div id="modal-icon" class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold" id="modal-title">Welcome Back</h2>
                        <p class="text-blue-100 text-sm" id="modal-subtitle">Access your governance dashboard</p>
                    </div>
                </div>
                <button onclick="closeAuthModal()" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative p-8">
            <!-- Login Form -->
            <form id="login-form" method="POST" action="index.php" class="space-y-6">
                <input type="hidden" name="action" value="login">

                <div class="space-y-2">
                    <label class="block text-gray-800 font-semibold text-sm">Email Address *</label>
                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50/50"
                            placeholder="juandelacruz@gmail.com"
                            required
                        />
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-800 font-semibold text-sm">Password *</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="login-password"
                            class="w-full px-4 py-3 pl-12 pr-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50/50"
                            placeholder="Enter your password"
                            required
                        />
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <button
                            type="button"
                            onclick="togglePassword('login-password')"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="error-message" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center space-x-2" style="display: none;">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="error-text"></span>
                </div>

                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 text-white py-4 px-6 rounded-xl transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] disabled:transform-none"
                >
                    <div class="flex items-center justify-center space-x-2">
                        <span>Sign In</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </div>
                </button>

                <div class="mt-6 space-y-4">
                    <div class="text-center">
                        <span class="text-gray-600">Don't have an account?</span>
                        <button
                            type="button"
                            onclick="switchToSignup()"
                            class="ml-2 text-blue-600 hover:text-blue-700"
                        >
                            Sign Up
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <button
                            type="button"
                            onclick="switchToReset()"
                            class="text-gray-600 hover:text-blue-600 text-sm"
                        >
                            Forgot your password?
                        </button>
                    </div>
                </div>
            </form>

            <!-- Signup Form -->
            <form id="signup-form" method="POST" action="index.php" class="space-y-4" style="display: none;">
                <input type="hidden" name="action" value="signup">
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm">
                            <p class="text-yellow-900 mb-1"><strong>Important Notice:</strong></p>
                            <p class="text-yellow-800">
                                Please use your <strong>legal name</strong> as it appears on government-issued IDs. 
                                Fake or incorrect names will result in rejection of your document requests and concerns by officials or administrators.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">First Name *</label>
                        <input
                            type="text"
                            name="first_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Juan"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Last Name *</label>
                        <input
                            type="text"
                            name="last_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Dela Cruz"
                            required
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-800 font-semibold text-sm">Middle Name (Optional)</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="middle_name"
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50/50"
                            placeholder="Santos"
                        />
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-amber-600 font-medium">⚠️ Names cannot be changed after registration</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-800 font-semibold text-sm">Email Address *</label>
                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50/50"
                            placeholder="juandelacruz@gmail.com"
                            required
                        />
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-blue-600 font-medium">Use a valid email (Gmail, Yahoo, Outlook, or .ph domains)</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-800 font-semibold text-sm">Password *</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="signup-password"
                            class="w-full px-4 py-3 pl-12 pr-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50/50"
                            placeholder="At least 8 characters"
                            required
                            minlength="8"
                        />
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <button
                            type="button"
                            onclick="togglePassword('signup-password')"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-green-600 font-medium">Must be at least 8 characters long</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-800 font-semibold text-sm">Complete Address *</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="address"
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50/50"
                            placeholder="123 Main Street, Barangay Centro, City, Province"
                            required
                            minlength="10"
                        />
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-purple-600 font-medium">Provide your complete address (house number, street, barangay, city, province)</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-800 font-semibold text-sm">Phone Number *</label>
                    <div class="relative">
                        <input
                            type="tel"
                            name="phone"
                            class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50/50"
                            placeholder="+63 912 345 6789"
                            required
                        />
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-indigo-600 font-medium">Philippine format: +63 followed by your number</p>
                </div>

                <div id="signup-success-message" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center space-x-2" style="display: none;">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="signup-success-text"></span>
                </div>

                <div id="signup-error-message" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" style="display: none;">
                    <span id="signup-error-text"></span>
                </div>

                <button
                    type="submit"
                    class="w-full bg-black hover:bg-gray-800 disabled:bg-gray-400 text-white py-2 px-4 rounded-lg transition-colors"
                >
                    Create Account
                </button>

                <div class="mt-6 text-center">
                    <span class="text-gray-600">Already have an account?</span>
                    <button
                        type="button"
                        onclick="switchToLogin()"
                        class="ml-2 text-blue-600 hover:text-blue-700"
                    >
                        Sign In
                    </button>
                </div>
                </div> <!-- End signup-form-fields -->
            </form>

            <!-- Password Reset Form -->
            <form id="reset-form" method="POST" action="index.php" class="space-y-4" style="display: none;">
                <input type="hidden" name="action" value="reset_password">
                
                <div>
                    <label class="block text-gray-700 mb-2">Email Address *</label>
                    <input
                        type="email"
                        name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="juandelacruz@gmail.com"
                        required
                    />
                </div>

                <div id="reset-error-message" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" style="display: none;">
                    <span id="reset-error-text"></span>
                </div>

                <button
                    type="submit"
                    class="w-full bg-black hover:bg-gray-800 disabled:bg-gray-400 text-white py-2 px-4 rounded-lg transition-colors"
                >
                    Send Reset Link
                </button>

                <div class="mt-6 text-center">
                    <button
                        type="button"
                        onclick="switchToLogin()"
                        class="text-blue-600 hover:text-blue-700"
                    >
                        ← Back to Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Control Functions
function showLoginModal() {
    document.getElementById('auth-modal').style.display = 'flex';
    switchToLogin();
}

function showSignupModal() {
    document.getElementById('auth-modal').style.display = 'flex';
    switchToSignup();
}

function closeAuthModal() {
    document.getElementById('auth-modal').style.display = 'none';
}

function switchToLogin() {
    document.getElementById('login-form').style.display = 'block';
    document.getElementById('signup-form').style.display = 'none';
    document.getElementById('reset-form').style.display = 'none';
    document.getElementById('modal-title').textContent = 'Welcome Back';
    document.getElementById('modal-subtitle').textContent = 'Access your governance dashboard';

    // Update icon
    const iconContainer = document.getElementById('modal-icon');
    iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>';
}

function switchToSignup() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('signup-form').style.display = 'block';
    document.getElementById('reset-form').style.display = 'none';
    document.getElementById('modal-title').textContent = 'Join BarangayLink';
    document.getElementById('modal-subtitle').textContent = 'Create your resident account';

    // Reset form state
    document.getElementById('signup-success-message').style.display = 'none';
    document.getElementById('signup-error-message').style.display = 'none';

    // Update icon
    const iconContainer = document.getElementById('modal-icon');
    iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>';
}

function switchToReset() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('signup-form').style.display = 'none';
    document.getElementById('reset-form').style.display = 'block';
    document.getElementById('modal-title').textContent = 'Reset Password';
    document.getElementById('modal-subtitle').textContent = 'Get back to your account';

    // Update icon
    const iconContainer = document.getElementById('modal-icon');
    iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>';
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function showSignupSuccessPopup(message) {
    // Create popup element
    const popup = document.createElement('div');
    popup.id = 'signup-success-popup';
    popup.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
    popup.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="bg-green-600 px-6 py-4 text-white rounded-t-lg text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold">Success!</h3>
            </div>
            <div class="p-6 text-center">
                <p class="text-gray-700 mb-6">${message}</p>
                <div class="flex space-x-3">
                    <button onclick="closeSignupSuccessPopup()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg transition-colors">
                        Close
                    </button>
                    <button onclick="closeSignupSuccessPopup(); showLoginModal();" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                        Login Now
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(popup);

    // Close on background click
    popup.addEventListener('click', function(e) {
        if (e.target === this) {
            closeSignupSuccessPopup();
        }
    });
}

function closeSignupSuccessPopup() {
    const popup = document.getElementById('signup-success-popup');
    if (popup) {
        popup.remove();
    }
}

// Close modal when clicking outside
document.getElementById('auth-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAuthModal();
    }
});

// Display errors if present
<?php if (isset($_SESSION['error'])): ?>
    showLoginModal();
    document.getElementById('error-message').style.display = 'block';
    document.getElementById('error-text').textContent = '<?php echo htmlspecialchars($_SESSION['error']); ?>';
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>
