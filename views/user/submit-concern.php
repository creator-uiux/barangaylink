<?php
/**
 * Submit Concern Form - 100% SYNCHRONIZED with components/SubmitConcernForm.tsx
 * EVERY FEATURE, EVERY FIELD, EVERY STYLE MATCHED EXACTLY
 */

$db = getDB();
$userId = $user['id'];

// Get user's concerns
$stmt = $db->prepare("SELECT * FROM concerns WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$concerns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission success max-w-6xl
$submitted = isset($_GET['success']) && $_GET['success'] === '1';
?>

<div class="space-y-6"> 
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <!-- Header with Icon - EXACT MATCH TSX Lines 71-79 -->
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl">Submit a Concern</h2>
                <p class="text-sm text-gray-600">Report community issues for resolution</p>
            </div>
        </div>

        <?php if ($submitted): ?>
            <!-- Success Message - EXACT MATCH TSX Lines 82-86 -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl text-green-900 mb-2">Concern Submitted Successfully!</h3>
                <p class="text-green-700">Your concern has been forwarded to the barangay officials. You will be notified of updates.</p>
            </div>
            <script>
                // Auto-hide success message and reload after 3 seconds
                setTimeout(() => {
                    window.location.href = '?view=submit-concern';
                }, 3000);
            </script>
        <?php else: ?>
            <!-- Form - EXACT MATCH TSX Lines 88-146 -->
            <form method="POST" id="concernForm" class="space-y-6" onsubmit="event.preventDefault(); submitConcern(event);">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                
                <!-- Category - EXACT MATCH TSX Lines 90-102 -->
                <div>
                    <label class="block text-gray-900 mb-2">Category *</label>
                    <select
                        name="category"
                        id="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white"
                        required
                    >
                        <option value="">Select a category</option>
                        <option value="Infrastructure">Infrastructure</option>
                        <option value="Public Safety">Public Safety</option>
                        <option value="Health & Sanitation">Health & Sanitation</option>
                        <option value="Environmental">Environmental</option>
                        <option value="Social Services">Social Services</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Subject - EXACT MATCH TSX Lines 104-114 -->
                <div>
                    <label class="block text-gray-900 mb-2">Subject *</label>
                    <input
                        type="text"
                        name="subject"
                        id="subject"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Brief title of your concern"
                        required
                    />
                </div>

                <!-- Location - EXACT MATCH TSX Lines 116-125 (OPTIONAL, not required!) -->
                <div>
                    <label class="block text-gray-900 mb-2">Location</label>
                    <input
                        type="text"
                        name="location"
                        id="location"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Specific location (street, landmark, etc.)"
                    />
                </div>

                <!-- Description - EXACT MATCH TSX Lines 127-136 -->
                <div>
                    <label class="block text-gray-900 mb-2">Description *</label>
                    <textarea
                        name="description"
                        id="description"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 h-32"
                        placeholder="Provide detailed information about your concern"
                        required
                    ></textarea>
                </div>


                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <h4 class="text-yellow-900 mb-2">Important Reminders:</h4>
    <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
        <li>Select the most appropriate category so your concern is routed to the correct department</li>
        <li>Provide a clear and specific subject to help identify the issue quickly</li>
        <li>Indicate the exact location or nearest landmark for accurate assessment</li>
        <li>Describe the concern in detail so the team can understand the situation properly</li>
        <li>Double-check your information before submitting to avoid delays in processing</li>
    </ul>
</div>

                <!-- Submit Button - EXACT MATCH TSX Lines 138-144 (BLACK, not yellow!) -->
                <button
                    type="submit"
                    id="submitBtn"
                    class="w-full bg-black hover:bg-gray-800 text-white py-3 rounded-lg transition-colors flex items-center justify-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Submit Concern</span>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Recent Concerns - EXACT MATCH TSX Lines 150-155 -->
    <div class="mt-6 bg-white rounded-lg p-6 border border-gray-200">
        <h3 class="text-xl mb-4">My Recent Concerns</h3>
        
        <?php if (count($concerns) === 0): ?>
            <!-- Empty State - EXACT MATCH TSX Lines 196-198 -->
            <p class="text-gray-600 text-center py-6">No concerns submitted yet.</p>
        <?php else: ?>
            <!-- Concern List - EXACT MATCH TSX Lines 202-259 -->
            <div class="space-y-3" id="concernsList">
                <?php foreach ($concerns as $concern): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors" data-concern-id="<?php echo $concern['id']; ?>">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-gray-900 mb-1"><?php echo htmlspecialchars($concern['subject']); ?></h4>
                                <p class="text-sm text-gray-600 mb-1"><?php echo htmlspecialchars($concern['category']); ?></p>
                                <p class="text-sm text-gray-700"><?php echo htmlspecialchars($concern['description']); ?></p>
                                <?php if (!empty($concern['location'])): ?>
                                    <p class="text-xs text-gray-600 mt-2 flex items-center">
                                        <span class="mr-1">📍</span>
                                        <?php echo htmlspecialchars($concern['location']); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500 mt-2">
                                    Submitted on <?php echo date('M j, Y', strtotime($concern['created_at'])); ?>
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Status Badge - EXACT MATCH TSX Lines 262-274 -->
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                    'in-progress' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                    'resolved' => 'bg-green-100 text-green-700 border border-green-200',
                                    'rejected' => 'bg-red-100 text-red-700 border border-red-200'
                                ];
                                $statusClass = $statusColors[$concern['status']] ?? $statusColors['pending'];
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($concern['status']); ?>
                                </span>
                                
                                <!-- Delete Button - EXACT MATCH TSX Lines 222-247 -->
                                <?php if ($concern['status'] === 'pending'): ?>
                                    <div class="delete-container">
                                        <button
                                            onclick="showDeleteConfirm(<?php echo $concern['id']; ?>)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors delete-btn"
                                            title="Delete concern"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        <div class="confirm-btns hidden flex items-center space-x-1">
                                            <button
                                                onclick="deleteConcern(<?php echo $concern['id']; ?>)"
                                                class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors"
                                            >
                                                Confirm
                                            </button>
                                            <button
                                                onclick="hideDeleteConfirm(<?php echo $concern['id']; ?>)"
                                                class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300 transition-colors"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Delete Warning - EXACT MATCH TSX Lines 250-255 -->
                        <?php if ($concern['status'] === 'pending'): ?>
                            <div class="mt-2 flex items-center space-x-1 text-xs text-gray-500 delete-warning">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>You can delete this concern before admin reviews it</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Form submission handling - EXACT MATCH TSX behavior (lines 29-57)
async function submitConcern(event) {
    const form = event.target;
    const submitBtn = document.getElementById('submitBtn');
    const originalHTML = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span>Submitting...</span>';
    
    try {
        const formData = new FormData(form);
        
        console.log('Sending to API:', {
            action: formData.get('action'),
            user_id: formData.get('user_id'),
            category: formData.get('category'),
            subject: formData.get('subject'),
            description: formData.get('description'),
            location: formData.get('location')
        }); // Debug log
        
        const response = await fetch('/api/concerns.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status); // Debug log
        
        const result = await response.json();
        
        console.log('Response data:', result); // Debug log
        
        if (result.success) {
            console.log('Success! Showing success message...'); // Debug log
            
            // Show success message without reload - SYNCHRONIZED with TSX
            form.parentElement.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl text-green-900 mb-2">Concern Submitted Successfully!</h3>
                    <p class="text-green-700">Your concern has been forwarded to the barangay officials. You will be notified of updates.</p>
                </div>
            `;
            
            // Reload page after 2 seconds to show the new concern in the list
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            console.error('API returned error:', result.error); // Debug log
            alert('Failed to submit concern: ' + (result.error || 'Unknown error'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    } catch (error) {
        console.error('Fetch error:', error); // Debug log
        alert('Failed to submit concern. Please check console for details.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHTML;
    }
}

// Delete functionality - EXACT MATCH TSX Lines 180-193
function showDeleteConfirm(id) {
    const container = document.querySelector(`[data-concern-id="${id}"] .delete-container`);
    if (container) {
        const deleteBtn = container.querySelector('.delete-btn');
        const confirmBtns = container.querySelector('.confirm-btns');
        const warning = container.closest('[data-concern-id]').querySelector('.delete-warning');
        
        if (deleteBtn) deleteBtn.style.display = 'none';
        if (confirmBtns) confirmBtns.classList.remove('hidden');
        if (warning) warning.style.display = 'none';
    }
}

function hideDeleteConfirm(id) {
    const container = document.querySelector(`[data-concern-id="${id}"] .delete-container`);
    if (container) {
        const deleteBtn = container.querySelector('.delete-btn');
        const confirmBtns = container.querySelector('.confirm-btns');
        const warning = container.closest('[data-concern-id]').querySelector('.delete-warning');
        
        if (deleteBtn) deleteBtn.style.display = 'block';
        if (confirmBtns) confirmBtns.classList.add('hidden');
        if (warning) warning.style.display = 'flex';
    }
}

async function deleteConcern(id) {
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        console.log('Deleting concern:', id); // Debug log
        
        const response = await fetch('api/concerns.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        console.log('Delete result:', result); // Debug log
        
        if (result.success) {
            // Remove the element from DOM with animation
            const element = document.querySelector(`[data-concern-id="${id}"]`);
            if (element) {
                element.style.transition = 'opacity 0.3s';
                element.style.opacity = '0';
                setTimeout(() => {
                    element.remove();
                    // Check if no more concerns
                    const remaining = document.querySelectorAll('[data-concern-id]');
                    if (remaining.length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        } else {
            alert('Failed to delete concern: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error deleting concern:', error);
        alert('Failed to delete concern. Please check console for details.');
    }
}

console.log('Submit concern page loaded successfully'); // Debug log
</script>