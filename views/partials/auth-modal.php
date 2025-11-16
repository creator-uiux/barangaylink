<?php
// Authentication Modal Component
// SYNCHRONIZED with components/AuthModal.tsx
?>
<div id="auth-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-black px-6 py-4 text-white rounded-t-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl" id="modal-title">Welcome Back</h2>
                    <p class="text-gray-400 text-sm" id="modal-subtitle">Access your governance dashboard</p>
                </div>
                <button onclick="closeAuthModal()" class="text-white/80 hover:text-white transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Login Form -->
            <form id="login-form" method="POST" action="index.php" class="space-y-4">
                <input type="hidden" name="action" value="login">
                
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

                <div>
                    <label class="block text-gray-700 mb-2">Password *</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="login-password"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter your password"
                            required
                        />
                        <button
                            type="button"
                            onclick="togglePassword('login-password')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="error-message" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" style="display: none;">
                    <span id="error-text"></span>
                </div>

                <button
                    type="submit"
                    class="w-full bg-black hover:bg-gray-800 disabled:bg-gray-400 text-white py-2 px-4 rounded-lg transition-colors"
                >
                    Sign In
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

                <div>
                    <label class="block text-gray-700 mb-2">Middle Name (Optional)</label>
                    <input
                        type="text"
                        name="middle_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Santos"
                    />
                </div>
                <p class="text-xs text-gray-500 -mt-2">⚠️ Names cannot be changed after registration</p>

                <div>
                    <label class="block text-gray-700 mb-2">Email Address *</label>
                    <input
                        type="email"
                        name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="juandelacruz@gmail.com"
                        required
                    />
                    <p class="text-xs text-gray-500 mt-1">Use a valid email (Gmail, Yahoo, Outlook, or .ph domains)</p>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Password *</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="signup-password"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="At least 8 characters"
                            required
                            minlength="8"
                        />
                        <button
                            type="button"
                            onclick="togglePassword('signup-password')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters long</p>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Complete Address *</label>
                    <input
                        type="text"
                        name="address"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="123 Main Street, Barangay Centro, City, Province"
                        required
                        minlength="10"
                    />
                    <p class="text-xs text-gray-500 mt-1">
                        Provide your complete address (house number, street, barangay, city, province)
                    </p>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Phone Number *</label>
                    <input
                        type="tel"
                        name="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="+63 912 345 6789"
                        required
                    />
                    <p class="text-xs text-gray-500 mt-1">Philippine format: +63 followed by your number</p>
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
}

function switchToSignup() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('signup-form').style.display = 'block';
    document.getElementById('reset-form').style.display = 'none';
    document.getElementById('modal-title').textContent = 'Join BarangayLink';
    document.getElementById('modal-subtitle').textContent = 'Create your resident account';
}

function switchToReset() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('signup-form').style.display = 'none';
    document.getElementById('reset-form').style.display = 'block';
    document.getElementById('modal-title').textContent = 'Reset Password';
    document.getElementById('modal-subtitle').textContent = 'Get back to your account';
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
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
