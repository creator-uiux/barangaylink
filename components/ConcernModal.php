<?php
function renderConcernModal() {
    $message = '';
    $error = '';
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_concern'])) {
        $concernType = $_POST['concernType'] ?? '';
        $concernTitle = trim($_POST['concernTitle'] ?? '');
        $concernDescription = trim($_POST['concernDescription'] ?? '');
        $concernLocation = trim($_POST['concernLocation'] ?? '');
        $urgencyLevel = $_POST['urgencyLevel'] ?? 'medium';
        
        if (empty($concernType) || empty($concernTitle) || empty($concernDescription)) {
            $error = 'Please fill in all required fields';
        } else {
            if (submitConcern($concernType, $concernTitle, $concernDescription, $concernLocation, $urgencyLevel)) {
                $message = 'Concern submitted successfully! We will review it and take appropriate action.';
                echo '<script>hideConcernModal(); showToast("' . addslashes($message) . '", "success");</script>';
            } else {
                $error = 'Failed to submit concern. Please try again.';
            }
        }
    }
?>

<!-- Concern Modal -->
<div id="concern-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Submit Concern</h2>
            <p class="text-slate-600">Report community issues or submit feedback to barangay officials.</p>
        </div>
        
        <?php if ($message): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-md">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4" id="concern-form">
            <input type="hidden" name="submit_concern" value="1">
            
            <div class="space-y-2">
                <label for="concernType" class="block font-medium text-slate-900">Concern Type *</label>
                <select
                    id="concernType"
                    name="concernType"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    required
                >
                    <option value="">Select Concern Type</option>
                    <option value="infrastructure" <?php echo (isset($_POST['concernType']) && $_POST['concernType'] === 'infrastructure') ? 'selected' : ''; ?>>Infrastructure</option>
                    <option value="public-safety" <?php echo (isset($_POST['concernType']) && $_POST['concernType'] === 'public-safety') ? 'selected' : ''; ?>>Public Safety</option>
                    <option value="sanitation" <?php echo (isset($_POST['concernType']) && $_POST['concernType'] === 'sanitation') ? 'selected' : ''; ?>>Sanitation</option>
                    <option value="noise-complaint" <?php echo (isset($_POST['concernType']) && $_POST['concernType'] === 'noise-complaint') ? 'selected' : ''; ?>>Noise Complaint</option>
                    <option value="community-service" <?php echo (isset($_POST['concernType']) && $_POST['concernType'] === 'community-service') ? 'selected' : ''; ?>>Community Service</option>
                    <option value="other" <?php echo (isset($_POST['concernType']) && $_POST['concernType'] === 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="space-y-2">
                <label for="concernTitle" class="block font-medium text-slate-900">Title *</label>
                <input
                    id="concernTitle"
                    name="concernTitle"
                    type="text"
                    placeholder="Brief title of your concern"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    required
                    value="<?php echo isset($_POST['concernTitle']) ? htmlspecialchars($_POST['concernTitle']) : ''; ?>"
                />
            </div>

            <div class="space-y-2">
                <label for="concernDescription" class="block font-medium text-slate-900">Description *</label>
                <textarea
                    id="concernDescription"
                    name="concernDescription"
                    placeholder="Please describe your concern in detail..."
                    rows="4"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background resize-none"
                    required
                ><?php echo isset($_POST['concernDescription']) ? htmlspecialchars($_POST['concernDescription']) : ''; ?></textarea>
            </div>

            <div class="space-y-2">
                <label for="concernLocation" class="block font-medium text-slate-900">Location</label>
                <input
                    id="concernLocation"
                    name="concernLocation"
                    type="text"
                    placeholder="Specific location or address (optional)"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    value="<?php echo isset($_POST['concernLocation']) ? htmlspecialchars($_POST['concernLocation']) : ''; ?>"
                />
            </div>

            <div class="space-y-2">
                <label for="urgencyLevel" class="block font-medium text-slate-900">Urgency Level *</label>
                <select
                    id="urgencyLevel"
                    name="urgencyLevel"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    required
                >
                    <option value="low" <?php echo (isset($_POST['urgencyLevel']) && $_POST['urgencyLevel'] === 'low') ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo (!isset($_POST['urgencyLevel']) || $_POST['urgencyLevel'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo (isset($_POST['urgencyLevel']) && $_POST['urgencyLevel'] === 'high') ? 'selected' : ''; ?>>High</option>
                    <option value="emergency" <?php echo (isset($_POST['urgencyLevel']) && $_POST['urgencyLevel'] === 'emergency') ? 'selected' : ''; ?>>Emergency</option>
                </select>
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors"
                id="submit-concern-btn"
            >
                Submit Concern
            </button>
        </form>
        
        <!-- Close Button -->
        <button onclick="hideConcernModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
function showConcernModal() {
    document.getElementById('concern-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideConcernModal() {
    document.getElementById('concern-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form if no errors
    <?php if (!$error): ?>
    document.getElementById('concern-form').reset();
    // Reset urgency level to default
    document.getElementById('urgencyLevel').value = 'medium';
    <?php endif; ?>
}

// Show modal if there are errors
<?php if ($error && isset($_POST['submit_concern'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showConcernModal();
    });
<?php endif; ?>

// Close modal when clicking outside
document.getElementById('concern-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideConcernModal();
    }
});

// Form submission handling
document.getElementById('concern-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-concern-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting Concern...';
    
    // Re-enable button after timeout as fallback
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Concern';
    }, 5000);
});
</script>

<?php
}

function submitConcern($concernType, $concernTitle, $concernDescription, $concernLocation, $urgencyLevel) {
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    $user = $_SESSION['user'];
    
    // Create concern object
    $concern = [
        'id' => time() . rand(100, 999), // Simple ID generation
        'userId' => $user['id'],
        'concernType' => $concernType,
        'concernTitle' => $concernTitle,
        'concernDescription' => $concernDescription,
        'concernLocation' => $concernLocation,
        'urgencyLevel' => $urgencyLevel,
        'status' => 'submitted',
        'submittedAt' => date('Y-m-d H:i:s')
    ];
    
    // Ensure data directory exists
    $dir = __DIR__ . '/../data';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Load existing concerns
    $file = $dir . '/concerns.json';
    $concerns = [];
    if (file_exists($file)) {
        $concerns = json_decode(file_get_contents($file), true) ?: [];
    }
    
    // Add new concern
    $concerns[] = $concern;
    
    // Save concerns
    return file_put_contents($file, json_encode($concerns, JSON_PRETTY_PRINT)) !== false;
}
?>