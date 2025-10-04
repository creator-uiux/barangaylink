<?php
/**
 * Error Boundary Component - PHP Version
 * EXACT conversion from ErrorBoundary.tsx preserving ALL Tailwind CSS classes
 */

function ErrorBoundary($children = '', $fallback = null) {
    // In PHP, we don't have React's error boundary functionality,
    // but we can provide a similar error display function
    ob_start();
    
    if ($fallback !== null) {
        echo $fallback;
    } else {
        ?>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg p-8 max-w-md mx-4">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-slate-800">Something went wrong</h2>
                </div>
                <p class="text-slate-600 mb-6">
                    We're sorry, but something unexpected happened. Please try refreshing the page.
                </p>
                <button
                    onclick="window.location.reload()"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Refresh Page
                </button>
            </div>
        </div>
        <?php
    }
    
    return ob_get_clean();
}

// PHP Error Handler Function (can be used to catch PHP errors)
function displayPhpError($error_message = 'An error occurred', $error_details = '') {
    ob_start();
    ?>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md mx-4">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800">Application Error</h2>
            </div>
            <p class="text-slate-600 mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </p>
            <?php if (!empty($error_details) && defined('DEBUG_MODE') && DEBUG_MODE): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                <p class="text-red-700 text-sm font-mono">
                    <?php echo htmlspecialchars($error_details); ?>
                </p>
            </div>
            <?php endif; ?>
            <div class="space-y-2">
                <button
                    onclick="window.location.reload()"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Refresh Page
                </button>
                <button
                    onclick="window.location.href='index.php'"
                    class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    Go to Home
                </button>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Set custom error handler for PHP
function customErrorHandler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $error_details = "Error: $message in $file on line $line";
    
    // Log the error
    error_log($error_details);
    
    // In development, show detailed error
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo displayPhpError('A PHP error occurred', $error_details);
    } else {
        // In production, show generic error
        echo displayPhpError('We encountered an unexpected error. Please try again.');
    }
    
    return true;
}

// Set custom exception handler for PHP
function customExceptionHandler($exception) {
    $error_details = "Uncaught exception: " . $exception->getMessage() . 
                    " in " . $exception->getFile() . 
                    " on line " . $exception->getLine();
    
    // Log the exception
    error_log($error_details);
    
    // Show error boundary
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo displayPhpError('An uncaught exception occurred', $error_details);
    } else {
        echo displayPhpError('We encountered an unexpected error. Please try again.');
    }
}

// Register error handlers (can be called in config.php)
function registerErrorHandlers() {
    set_error_handler('customErrorHandler');
    set_exception_handler('customExceptionHandler');
}
?>