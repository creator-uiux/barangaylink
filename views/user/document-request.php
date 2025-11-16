<?php
/**
 * Document Request Form - 100% SYNCHRONIZED with components/DocumentRequestForm.tsx
 * COMPLETELY FIXED - WORKING NOW!
 */

$conn = getDBConnection();
$userId = $user['id'];

// Get user's document requests
$stmt = $conn->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $userId);
$stmt->execute();
$documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle form submission success
$submitted = isset($_GET['success']) && $_GET['success'] === '1';
?>

<div class="max-w-4xl">
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <!-- Header with Icon - EXACT MATCH TSX Lines 73-81 -->
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl">Request a Document</h2>
                <p class="text-sm text-gray-600">Submit your document request online</p>
            </div>
        </div>

        <?php if ($submitted): ?>
            <!-- Success Message - EXACT MATCH TSX Lines 84-88 -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl text-green-900 mb-2">Request Submitted Successfully!</h3>
                <p class="text-green-700">Your document request has been received. You will be notified of updates.</p>
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = '?view=document-request';
                }, 3000);
            </script>
        <?php else: ?>
            <!-- Form - EXACT MATCH TSX Lines 90-180 -->
            <form method="POST" id="documentForm" class="space-y-6" onsubmit="event.preventDefault(); submitDocumentRequest(event);">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                
                <!-- Document Type - EXACT MATCH TSX Lines 92-106 -->
                <div>
                    <label class="block text-gray-900 mb-2">Document Type *</label>
                    <select
                        name="documentType"
                        id="documentType"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        required
                    >
                        <option value="">Select a document type</option>
                        <option value="Barangay Clearance" data-fee="₱50" data-processing="1-2 days">Barangay Clearance - ₱50</option>
                        <option value="Certificate of Indigency" data-fee="Free" data-processing="1 day">Certificate of Indigency - Free</option>
                        <option value="Certificate of Residency" data-fee="₱30" data-processing="1 day">Certificate of Residency - ₱30</option>
                        <option value="Business Permit" data-fee="₱200" data-processing="3-5 days">Business Permit - ₱200</option>
                        <option value="Building Permit Clearance" data-fee="₱150" data-processing="2-3 days">Building Permit Clearance - ₱150</option>
                        <option value="Community Tax Certificate" data-fee="₱100" data-processing="1 day">Community Tax Certificate - ₱100</option>
                    </select>
                </div>

                <!-- Info Card - EXACT MATCH TSX Lines 108-125 -->
                <div id="docInfo" class="bg-blue-50 border border-blue-100 rounded-lg p-4" style="display: none;">
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-blue-600 mb-1">Document Fee</p>
                            <p class="text-blue-900" id="docFee">-</p>
                        </div>
                        <div>
                            <p class="text-blue-600 mb-1">Processing Time</p>
                            <p class="text-blue-900" id="docProcessing">-</p>
                        </div>
                        <div>
                            <p class="text-blue-600 mb-1">Requirements</p>
                            <p class="text-blue-900">Valid ID, Proof of Residency</p>
                        </div>
                    </div>
                </div>

                <!-- Purpose - EXACT MATCH TSX Lines 127-137 -->
                <div>
                    <label class="block text-gray-900 mb-2">Purpose *</label>
                    <input
                        type="text"
                        name="purpose"
                        id="purpose"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., Employment, Business Registration, etc."
                        required
                    />
                </div>

                <!-- Quantity - EXACT MATCH TSX Lines 139-150 -->
                <div>
                    <label class="block text-gray-900 mb-2">Quantity</label>
                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        min="1"
                        max="10"
                        value="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <p class="text-xs text-gray-500 mt-1">Number of copies needed (max 10)</p>
                </div>

                <!-- Additional Notes - EXACT MATCH TSX Lines 152-160 -->
                <div>
                    <label class="block text-gray-900 mb-2">Additional Notes</label>
                    <textarea
                        name="notes"
                        id="notes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24"
                        placeholder="Any additional information or special requests"
                    ></textarea>
                </div>

                <!-- Warning Box - EXACT MATCH TSX Lines 162-170 -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="text-yellow-900 mb-2">Important Reminders:</h4>
                    <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                        <li>Please bring valid ID and proof of residency when claiming</li>
                        <li>Processing time starts after document verification</li>
                        <li>Payment is made upon document release</li>
                        <li>You will receive a notification when your document is ready</li>
                    </ul>
                </div>

                <!-- Submit Button - EXACT MATCH TSX Lines 172-178 -->
                <button
                    type="submit"
                    id="submitBtn"
                    class="w-full bg-black hover:bg-gray-800 text-white py-3 rounded-lg transition-colors flex items-center justify-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Submit Request</span>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Recent Requests - EXACT MATCH TSX Lines 183-188 -->
    <div class="mt-6 bg-white rounded-lg p-6 border border-gray-200">
        <h3 class="text-xl mb-4">My Recent Requests</h3>
        
        <?php if (count($documents) === 0): ?>
            <p class="text-gray-600 text-center py-6">No document requests yet.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($documents as $doc): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors" data-doc-id="<?php echo $doc['id']; ?>">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-gray-900 mb-1"><?php echo htmlspecialchars($doc['document_type']); ?></h4>
                                <p class="text-sm text-gray-600 mb-1">Purpose: <?php echo htmlspecialchars($doc['purpose']); ?></p>
                                <p class="text-sm text-gray-600">Quantity: <?php echo $doc['quantity']; ?></p>
                                <?php if (!empty($doc['notes'])): ?>
                                    <p class="text-xs text-gray-600 mt-2">Notes: <?php echo htmlspecialchars($doc['notes']); ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500 mt-2">
                                    Requested on <?php echo date('M j, Y', strtotime($doc['created_at'])); ?>
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                    'processing' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                    'approved' => 'bg-green-100 text-green-700 border border-green-200',
                                    'rejected' => 'bg-red-100 text-red-700 border border-red-200'
                                ];
                                $statusClass = $statusColors[$doc['status']] ?? $statusColors['pending'];
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($doc['status']); ?>
                                </span>
                                
                                <?php if ($doc['status'] === 'pending'): ?>
                                    <div class="delete-container">
                                        <button
                                            onclick="showDeleteConfirm(<?php echo $doc['id']; ?>)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors delete-btn"
                                            title="Delete request"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        <div class="confirm-btns hidden flex items-center space-x-1">
                                            <button
                                                onclick="deleteDocument(<?php echo $doc['id']; ?>)"
                                                class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors"
                                            >
                                                Confirm
                                            </button>
                                            <button
                                                onclick="hideDeleteConfirm(<?php echo $doc['id']; ?>)"
                                                class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300 transition-colors"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($doc['status'] === 'pending'): ?>
                            <div class="mt-2 flex items-center space-x-1 text-xs text-gray-500 delete-warning">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>You can delete this request before processing</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Document type change handler
const docTypeSelect = document.getElementById('documentType');
if (docTypeSelect) {
    docTypeSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const fee = selected.getAttribute('data-fee');
        const processing = selected.getAttribute('data-processing');
        
        if (fee && processing) {
            document.getElementById('docInfo').style.display = 'block';
            document.getElementById('docFee').textContent = fee;
            document.getElementById('docProcessing').textContent = processing;
        } else {
            document.getElementById('docInfo').style.display = 'none';
        }
    });
}

// Form submission handler
async function submitDocumentRequest(event) {
    const documentForm = document.getElementById('documentForm');
    if (documentForm) {
        const submitBtn = document.getElementById('submitBtn');
        const originalHTML = submitBtn.innerHTML;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span class="ml-2">Submitting...</span>';
        
        try {
            const formData = new FormData(documentForm);
            
            console.log('📄 Document form submitted');
            
            console.log('📤 Sending to API:', {
                action: formData.get('action'),
                user_id: formData.get('user_id'),
                documentType: formData.get('documentType'),
                purpose: formData.get('purpose'),
                quantity: formData.get('quantity'),
                notes: formData.get('notes')
            });
            
            const response = await fetch('/api/documents.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('📡 Response status:', response.status);
            
            const result = await response.json();
            
            console.log('📥 Response data:', result);
            
            if (result.success) {
                console.log('✅ Success! Showing success message...');
                
                // Show success message without reload - SYNCHRONIZED with TSX
                documentForm.parentElement.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl text-green-900 mb-2">Request Submitted Successfully!</h3>
                        <p class="text-green-700">Your document request has been received. You will be notified of updates.</p>
                    </div>
                `;
                
                // Reload page after 2 seconds to show the new request in the list
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                console.error('❌ API returned error:', result.error);
                alert('Failed to submit request: ' + (result.error || 'Unknown error'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('❌ Fetch error:', error);
            alert('Failed to submit request. Please check console for details.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    }
}

// Delete functions
function showDeleteConfirm(id) {
    const container = document.querySelector(`[data-doc-id="${id}"] .delete-container`);
    if (container) {
        container.querySelector('.delete-btn').style.display = 'none';
        container.querySelector('.confirm-btns').classList.remove('hidden');
        container.closest('[data-doc-id]').querySelector('.delete-warning').style.display = 'none';
    }
}

function hideDeleteConfirm(id) {
    const container = document.querySelector(`[data-doc-id="${id}"] .delete-container`);
    if (container) {
        container.querySelector('.delete-btn').style.display = 'block';
        container.querySelector('.confirm-btns').classList.add('hidden');
        container.closest('[data-doc-id]').querySelector('.delete-warning').style.display = 'flex';
    }
}

async function deleteDocument(id) {
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        console.log('🗑️ Deleting document:', id);
        
        const response = await fetch('api/documents.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        console.log('📥 Delete result:', result);
        
        if (result.success) {
            const element = document.querySelector(`[data-doc-id="${id}"]`);
            if (element) {
                element.style.transition = 'opacity 0.3s';
                element.style.opacity = '0';
                setTimeout(() => {
                    element.remove();
                    const remaining = document.querySelectorAll('[data-doc-id]');
                    if (remaining.length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        } else {
            alert('Failed to delete request: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('❌ Error deleting document:', error);
        alert('Failed to delete request. Please check console for details.');
    }
}

console.log('✅ Document request page loaded successfully');
</script>