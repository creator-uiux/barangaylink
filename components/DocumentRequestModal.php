<?php
function renderDocumentRequestModal() {
    $message = '';
    $error = '';
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_document_request'])) {
        $documentType = $_POST['documentType'] ?? '';
        $purpose = trim($_POST['purpose'] ?? '');
        $contactNumber = trim($_POST['contactNumber'] ?? '');
        $additionalNotes = trim($_POST['additionalNotes'] ?? '');
        
        if (empty($documentType) || empty($purpose) || empty($contactNumber)) {
            $error = 'Please fill in all required fields';
        } else {
            if (submitDocumentRequest($documentType, $purpose, $contactNumber, $additionalNotes)) {
                $message = 'Document request submitted successfully! You will be notified when it\'s ready.';
                echo '<script>hideDocumentRequestModal(); showToast("' . addslashes($message) . '", "success");</script>';
            } else {
                $error = 'Failed to submit document request. Please try again.';
            }
        }
    }
?>

<!-- Document Request Modal -->
<div id="document-request-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Request Document</h2>
            <p class="text-slate-600">Submit a request for barangay certificates and official documents.</p>
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
        
        <form method="POST" class="space-y-4" id="document-request-form">
            <input type="hidden" name="submit_document_request" value="1">
            
            <div class="space-y-2">
                <label for="documentType" class="block font-medium text-slate-900">Document Type *</label>
                <select
                    id="documentType"
                    name="documentType"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    required
                >
                    <option value="">Select Document Type</option>
                    <option value="barangay-clearance" <?php echo (isset($_POST['documentType']) && $_POST['documentType'] === 'barangay-clearance') ? 'selected' : ''; ?>>Barangay Clearance</option>
                    <option value="certificate-residency" <?php echo (isset($_POST['documentType']) && $_POST['documentType'] === 'certificate-residency') ? 'selected' : ''; ?>>Certificate of Residency</option>
                    <option value="certificate-indigency" <?php echo (isset($_POST['documentType']) && $_POST['documentType'] === 'certificate-indigency') ? 'selected' : ''; ?>>Certificate of Indigency</option>
                    <option value="business-permit" <?php echo (isset($_POST['documentType']) && $_POST['documentType'] === 'business-permit') ? 'selected' : ''; ?>>Business Permit</option>
                    <option value="id-replacement" <?php echo (isset($_POST['documentType']) && $_POST['documentType'] === 'id-replacement') ? 'selected' : ''; ?>>ID Replacement</option>
                </select>
            </div>

            <div class="space-y-2">
                <label for="purpose" class="block font-medium text-slate-900">Purpose *</label>
                <input
                    id="purpose"
                    name="purpose"
                    type="text"
                    placeholder="e.g., Employment, Business, etc."
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    required
                    value="<?php echo isset($_POST['purpose']) ? htmlspecialchars($_POST['purpose']) : ''; ?>"
                />
            </div>

            <div class="space-y-2">
                <label for="contactNumber" class="block font-medium text-slate-900">Contact Number *</label>
                <input
                    id="contactNumber"
                    name="contactNumber"
                    type="tel"
                    placeholder="Your contact number"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    required
                    value="<?php echo isset($_POST['contactNumber']) ? htmlspecialchars($_POST['contactNumber']) : ''; ?>"
                />
            </div>

            <div class="space-y-2">
                <label for="additionalNotes" class="block font-medium text-slate-900">Additional Notes</label>
                <textarea
                    id="additionalNotes"
                    name="additionalNotes"
                    placeholder="Any additional information..."
                    rows="3"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background resize-none"
                ><?php echo isset($_POST['additionalNotes']) ? htmlspecialchars($_POST['additionalNotes']) : ''; ?></textarea>
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors"
                id="submit-document-request-btn"
            >
                Submit Request
            </button>
        </form>
        
        <!-- Close Button -->
        <button onclick="hideDocumentRequestModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
function showDocumentRequestModal() {
    document.getElementById('document-request-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideDocumentRequestModal() {
    document.getElementById('document-request-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form if no errors
    <?php if (!$error): ?>
    document.getElementById('document-request-form').reset();
    <?php endif; ?>
}

// Show modal if there are errors
<?php if ($error && isset($_POST['submit_document_request'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showDocumentRequestModal();
    });
<?php endif; ?>

// Close modal when clicking outside
document.getElementById('document-request-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDocumentRequestModal();
    }
});

// Form submission handling
document.getElementById('document-request-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-document-request-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting Request...';
    
    // Re-enable button after timeout as fallback
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Request';
    }, 5000);
});
</script>

<?php
}

function submitDocumentRequest($documentType, $purpose, $contactNumber, $additionalNotes) {
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    $user = $_SESSION['user'];
    
    // Create request object
    $request = [
        'id' => time() . rand(100, 999), // Simple ID generation
        'userId' => $user['id'],
        'documentType' => $documentType,
        'purpose' => $purpose,
        'contactNumber' => $contactNumber,
        'additionalNotes' => $additionalNotes,
        'status' => 'pending',
        'submittedAt' => date('Y-m-d H:i:s')
    ];
    
    // Ensure data directory exists
    $dir = __DIR__ . '/../data';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Load existing requests
    $file = $dir . '/requests.json';
    $requests = [];
    if (file_exists($file)) {
        $requests = json_decode(file_get_contents($file), true) ?: [];
    }
    
    // Add new request
    $requests[] = $request;
    
    // Save requests
    return file_put_contents($file, json_encode($requests, JSON_PRETTY_PRINT)) !== false;
}
?>