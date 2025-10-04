<?php
/**
 * Auth Modal Component - EXACT MATCH to AuthModal.tsx
 * NO PRE-FILLED CREDENTIALS - User must enter their own!
 */

function AuthModal($mode = 'login', $error = null) {
    ob_start();
?>
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" id="auth-modal">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-blue-600 px-6 py-4 text-white rounded-t-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold">
                        <?php 
                        if ($mode === 'login') echo 'Welcome Back';
                        elseif ($mode === 'signup') echo 'Join BarangayLink';
                        else echo 'Reset Password';
                        ?>
                    </h2>
                    <p class="text-blue-100 text-sm">
                        <?php 
                        if ($mode === 'login') echo 'Access your governance dashboard';
                        elseif ($mode === 'signup') echo 'Create your resident account';
                        else echo 'Get back to your account';
                        ?>
                    </p>
                </div>
                <button onclick="window.location.href='index.php'" class="text-white/80 hover:text-white transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="" class="space-y-4" data-ajax="true">
                <input type="hidden" name="action" value="<?php echo $mode === 'login' ? 'login' : ($mode === 'signup' ? 'signup' : 'reset'); ?>">
                <input type="hidden" name="authMode" value="<?php echo $mode; ?>">
                
                <?php if ($mode === 'signup'): ?>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Full Name *</label>
                    <input
                        type="text"
                        name="name"
                        value=""
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your full name"
                        required
                    />
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Email Address *</label>
                    <input
                        type="email"
                        name="email"
                        value=""
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your email address"
                        required
                    />
                </div>

                <?php if ($mode !== 'reset'): ?>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Password *</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            value=""
                            id="password-field"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter your password"
                            required
                        />
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878l-4.242-4.242m0 0L3.172 3.172M21.828 21.828L18.536 18.536m-4.243-4.243a3 3 0 01-4.243-4.243m4.243 4.243L21.828 21.828" />
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($mode === 'signup'): ?>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Address</label>
                    <input
                        type="text"
                        name="address"
                        value=""
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your address"
                    />
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Phone Number</label>
                    <input
                        type="tel"
                        name="phone"
                        value=""
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your phone number"
                    />
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white py-2 px-4 rounded-lg transition-colors font-medium"
                >
                    <span class="submit-text">
                        <?php 
                        if ($mode === 'login') echo 'Sign In';
                        elseif ($mode === 'signup') echo 'Create Account';
                        else echo 'Send Reset Link';
                        ?>
                    </span>
                </button>
            </form>

            <?php if ($mode !== 'reset'): ?>
            <div class="mt-6 space-y-4">
                <div class="text-center">
                    <span class="text-gray-600">
                        <?php echo $mode === 'login' ? "Don't have an account?" : 'Already have an account?'; ?>
                    </span>
                    <a
                        href="?auth=true&mode=<?php echo $mode === 'login' ? 'signup' : 'login'; ?>"
                        class="ml-2 text-blue-600 hover:text-blue-700 font-medium"
                    >
                        <?php echo $mode === 'login' ? 'Sign Up' : 'Sign In'; ?>
                    </a>
                </div>
                
                <?php if ($mode === 'login'): ?>
                <div class="text-center">
                    <a
                        href="?auth=true&mode=reset"
                        class="text-gray-600 hover:text-blue-600 text-sm"
                    >
                        Forgot your password?
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($mode === 'reset'): ?>
            <div class="mt-6 text-center">
                <a
                    href="?auth=true&mode=login"
                    class="text-blue-600 hover:text-blue-700 font-medium"
                >
                    ← Back to Login
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password-field');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        passwordField.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}
</script>
<?php
    return ob_get_clean();
}
?>