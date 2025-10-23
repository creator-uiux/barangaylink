<?php
/**
 * Document Request Form Component - EXACT MATCH to DocumentRequestForm.tsx
 * Allows users to request barangay documents online
 */

function DocumentRequestForm($user) {
    // Form processing is now handled in index.php to prevent header errors
    
    $submitted = isset($_SESSION['doc_submitted']) ? $_SESSION['doc_submitted'] : false;
    unset($_SESSION['doc_submitted']);
    
    $documentTypes = [
        ['value' => 'Barangay Clearance', 'fee' => '₱50', 'processing' => '1-2 days'],
        ['value' => 'Certificate of Indigency', 'fee' => 'Free', 'processing' => '1 day'],
        ['value' => 'Certificate of Residency', 'fee' => '₱30', 'processing' => '1 day'],
        ['value' => 'Business Permit', 'fee' => '₱200', 'processing' => '3-5 days'],
        ['value' => 'Building Permit Clearance', 'fee' => '₱150', 'processing' => '2-3 days'],
        ['value' => 'Community Tax Certificate', 'fee' => '₱100', 'processing' => '1 day']
    ];
    
    ob_start();
?>
<div class="max-w-4xl">
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-blue-900">Request a Document</h2>
                <p class="text-sm text-blue-600">Submit your document request online</p>
            </div>
        </div>

        <?php if ($submitted): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center" id="success-message">
            <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-green-900 mb-2">Request Submitted Successfully!</h3>
            <p class="text-green-700">Your document request has been received. You will be notified of updates.</p>
        </div>
        <?php else: ?>
        <form method="POST" action="" id="doc-request-form" class="space-y-6">
            <input type="hidden" name="action" value="submit_document_request">
            <input type="hidden" name="requestedBy" value="<?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') ?: $user['name'] ?? 'User'); ?>">
            <input type="hidden" name="requestedByEmail" value="<?php echo htmlspecialchars($user['email']); ?>">
            
            <div>
                <label class="block text-blue-900 mb-2">Document Type *</label>
                <select
                    name="documentType"
                    id="documentType"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                    required
                    onchange="updateDocumentInfo()"
                >
                    <option value="">Select a document type</option>
                    <?php foreach ($documentTypes as $doc): ?>
                    <option value="<?php echo htmlspecialchars($doc['value']); ?>" 
                            data-fee="<?php echo htmlspecialchars($doc['fee']); ?>"
                            data-processing="<?php echo htmlspecialchars($doc['processing']); ?>">
                        <?php echo htmlspecialchars($doc['value']); ?> - <?php echo htmlspecialchars($doc['fee']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="doc-info" class="bg-blue-50 border border-blue-100 rounded-lg p-4 hidden">
                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-blue-600 mb-1">Document Fee</p>
                        <p class="text-blue-900" id="doc-fee">-</p>
                    </div>
                    <div>
                        <p class="text-blue-600 mb-1">Processing Time</p>
                        <p class="text-blue-900" id="doc-processing">-</p>
                    </div>
                    <div>
                        <p class="text-blue-600 mb-1">Requirements</p>
                        <p class="text-blue-900">Valid ID, Proof of Residency</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-blue-900 mb-2">Purpose *</label>
                <input
                    type="text"
                    name="purpose"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., Employment, Business Registration, etc."
                    required
                />
            </div>

            <div>
                <label class="block text-blue-900 mb-2">Quantity</label>
                <input
                    type="number"
                    name="quantity"
                    min="1"
                    max="10"
                    value="1"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p class="text-xs text-blue-600 mt-1">Number of copies needed (max 10)</p>
            </div>

            <div>
                <label class="block text-blue-900 mb-2">Additional Notes</label>
                <textarea
                    name="notes"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24"
                    placeholder="Any additional information or special requests"
                ></textarea>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="text-yellow-900 mb-2">Important Reminders:</h4>
                <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                    <li>Please bring valid ID and proof of residency when claiming</li>
                    <li>Processing time starts after document verification</li>
                    <li>Payment is made upon document release</li>
                    <li>You will receive a notification when your document is ready</li>
                </ul>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg transition-colors flex items-center justify-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <span>Submit Request</span>
            </button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Recent Requests -->
    <div class="mt-6 bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-blue-900 mb-4">My Recent Requests</h3>
        <?php echo RecentDocumentRequests($user['email']); ?>
    </div>
</div>

<script>
function updateDocumentInfo() {
    const select = document.getElementById('documentType');
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('doc-info');
    
    if (option.value) {
        document.getElementById('doc-fee').textContent = option.dataset.fee;
        document.getElementById('doc-processing').textContent = option.dataset.processing;
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

// Auto-hide success message after 3 seconds
<?php if ($submitted): ?>
setTimeout(() => {
    const msg = document.getElementById('success-message');
    if (msg) {
        msg.style.transition = 'opacity 0.5s';
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 500);
    }
}, 3000);
<?php endif; ?>
</script>
<?php
    return ob_get_clean();
}

function RecentDocumentRequests($userEmail) {
    $documentsData = json_decode(file_get_contents(__DIR__ . '/../data/requests.json'), true) ?: [];
    $userDocuments = array_filter($documentsData, fn($d) => $d['requestedByEmail'] === $userEmail);
    $userDocuments = array_slice(array_reverse($userDocuments), 0, 5);
    
    ob_start();
    
    if (empty($userDocuments)):
?>
    <p class="text-blue-600 text-center py-6">No document requests yet.</p>
<?php
    else:
?>
    <div class="space-y-3">
        <?php foreach ($userDocuments as $doc): ?>
        <div class="border border-blue-100 rounded-lg p-4">
            <div class="flex items-start justify-between mb-2">
                <h4 class="text-blue-900"><?php echo htmlspecialchars($doc['documentType']); ?></h4>
                <?php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'processing' => 'bg-blue-100 text-blue-700',
                    'approved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700'
                ];
                $status = $doc['status'] ?? 'pending';
                $colorClass = $statusColors[$status] ?? $statusColors['pending'];
                ?>
                <span class="px-3 py-1 rounded-full text-xs <?php echo $colorClass; ?>">
                    <?php echo htmlspecialchars($status); ?>
                </span>
            </div>
            <p class="text-sm text-blue-600 mb-1">Purpose: <?php echo htmlspecialchars($doc['purpose']); ?></p>
            <p class="text-sm text-blue-600 mb-1">Quantity: <?php echo htmlspecialchars($doc['quantity'] ?? 1); ?></p>
            <?php if (!empty($doc['notes'])): ?>
            <p class="text-sm text-blue-700 mt-2">Notes: <?php echo htmlspecialchars($doc['notes']); ?></p>
            <?php endif; ?>
            <p class="text-xs text-blue-500 mt-2">
                Requested on <?php echo date('F d, Y', strtotime($doc['createdAt'])); ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>
<?php
    endif;
    
    return ob_get_clean();
}
?>