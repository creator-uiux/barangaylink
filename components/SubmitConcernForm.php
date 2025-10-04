<?php
/**
 * Submit Concern Form Component - EXACT MATCH to SubmitConcernForm.tsx
 * Allows users to report community issues
 */

function SubmitConcernForm($user) {
    // Form processing is now handled in index.php to prevent header errors
    
    $submitted = isset($_SESSION['concern_submitted']) ? $_SESSION['concern_submitted'] : false;
    unset($_SESSION['concern_submitted']);
    
    $categories = [
        'Infrastructure',
        'Public Safety',
        'Health & Sanitation',
        'Environmental',
        'Social Services',
        'Other'
    ];
    
    ob_start();
?>
<div class="max-w-3xl">
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-blue-900">Submit a Concern</h2>
                <p class="text-sm text-blue-600">Report community issues for resolution</p>
            </div>
        </div>

        <?php if ($submitted): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center" id="success-message">
            <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-green-900 mb-2">Concern Submitted Successfully!</h3>
            <p class="text-green-700">Your concern has been forwarded to the barangay officials. You will be notified of updates.</p>
        </div>
        <?php else: ?>
        <form method="POST" action="" class="space-y-6">
            <input type="hidden" name="action" value="submit_concern">
            <input type="hidden" name="submittedBy" value="<?php echo htmlspecialchars($user['name']); ?>">
            <input type="hidden" name="submittedByEmail" value="<?php echo htmlspecialchars($user['email']); ?>">
            
            <div>
                <label class="block text-blue-900 mb-2">Category *</label>
                <select
                    name="category"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                    required
                >
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-blue-900 mb-2">Subject *</label>
                <input
                    type="text"
                    name="subject"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Brief title of your concern"
                    required
                />
            </div>

            <div>
                <label class="block text-blue-900 mb-2">Location</label>
                <input
                    type="text"
                    name="location"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Specific location (street, landmark, etc.)"
                />
            </div>

            <div>
                <label class="block text-blue-900 mb-2">Description *</label>
                <textarea
                    name="description"
                    class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-32"
                    placeholder="Provide detailed information about your concern"
                    required
                ></textarea>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg transition-colors flex items-center justify-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <span>Submit Concern</span>
            </button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Recent Concerns -->
    <div class="mt-6 bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-blue-900 mb-4">My Recent Concerns</h3>
        <?php echo RecentConcerns($user['email']); ?>
    </div>
</div>

<script>
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

function RecentConcerns($userEmail) {
    $concernsData = json_decode(file_get_contents(__DIR__ . '/../data/concerns.json'), true) ?: [];
    $userConcerns = array_filter($concernsData, fn($c) => $c['submittedByEmail'] === $userEmail);
    $userConcerns = array_slice(array_reverse($userConcerns), 0, 5);
    
    ob_start();
    
    if (empty($userConcerns)):
?>
    <p class="text-blue-600 text-center py-6">No concerns submitted yet.</p>
<?php
    else:
?>
    <div class="space-y-3">
        <?php foreach ($userConcerns as $concern): ?>
        <div class="border border-blue-100 rounded-lg p-4">
            <div class="flex items-start justify-between mb-2">
                <h4 class="text-blue-900"><?php echo htmlspecialchars($concern['subject']); ?></h4>
                <?php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'in-progress' => 'bg-blue-100 text-blue-700',
                    'resolved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700'
                ];
                $status = $concern['status'] ?? 'pending';
                $colorClass = $statusColors[$status] ?? $statusColors['pending'];
                ?>
                <span class="px-3 py-1 rounded-full text-xs <?php echo $colorClass; ?>">
                    <?php echo htmlspecialchars($status); ?>
                </span>
            </div>
            <p class="text-sm text-blue-600 mb-2"><?php echo htmlspecialchars($concern['category']); ?></p>
            <p class="text-sm text-blue-700"><?php echo htmlspecialchars($concern['description']); ?></p>
            <?php if (!empty($concern['location'])): ?>
            <p class="text-xs text-blue-500 mt-2">📍 <?php echo htmlspecialchars($concern['location']); ?></p>
            <?php endif; ?>
            <p class="text-xs text-blue-500 mt-2">
                <?php echo date('F d, Y', strtotime($concern['createdAt'])); ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>
<?php
    endif;
    
    return ob_get_clean();
}
?>