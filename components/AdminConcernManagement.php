<?php
/**
 * Admin Concern Management Component - EXACT MATCH to AdminConcernManagement.tsx
 * Review and manage community concerns
 */

function AdminConcernManagement() {
    // Load concerns from JSON
    $concerns = json_decode(file_get_contents(__DIR__ . '/../data/concerns.json'), true) ?: [];
    
    // Form processing is now handled in index.php to prevent header errors
    
    // Apply filters
    $filter = $_GET['filter'] ?? 'all';
    $searchTerm = $_GET['search'] ?? '';
    $selectedId = $_GET['selected'] ?? '';
    
    $filteredConcerns = array_filter($concerns, function($concern) use ($filter, $searchTerm) {
        $statusMatch = $filter === 'all' || $concern['status'] === $filter;
        $searchMatch = empty($searchTerm) || 
                      stripos($concern['subject'], $searchTerm) !== false ||
                      stripos($concern['category'], $searchTerm) !== false ||
                      stripos($concern['submittedBy'], $searchTerm) !== false;
        return $statusMatch && $searchMatch;
    });
    
    // Get selected concern
    $selectedConcern = null;
    if ($selectedId) {
        foreach ($concerns as $c) {
            if ($c['id'] === $selectedId) {
                $selectedConcern = $c;
                break;
            }
        }
    }
    
    // Calculate stats
    $stats = [
        'total' => count($concerns),
        'pending' => count(array_filter($concerns, fn($c) => $c['status'] === 'pending')),
        'inProgress' => count(array_filter($concerns, fn($c) => $c['status'] === 'in-progress')),
        'resolved' => count(array_filter($concerns, fn($c) => $c['status'] === 'resolved'))
    ];
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-blue-900 mb-2">Community Concern Management</h2>
        <p class="text-blue-600">Review and address community concerns</p>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-blue-50 text-blue-700 rounded-lg p-4">
            <p class="text-sm mb-1">Total Concerns</p>
            <p class="text-2xl"><?php echo $stats['total']; ?></p>
        </div>
        <div class="bg-yellow-50 text-yellow-700 rounded-lg p-4">
            <p class="text-sm mb-1">Pending</p>
            <p class="text-2xl"><?php echo $stats['pending']; ?></p>
        </div>
        <div class="bg-blue-50 text-blue-700 rounded-lg p-4">
            <p class="text-sm mb-1">In Progress</p>
            <p class="text-2xl"><?php echo $stats['inProgress']; ?></p>
        </div>
        <div class="bg-green-50 text-green-700 rounded-lg p-4">
            <p class="text-sm mb-1">Resolved</p>
            <p class="text-2xl"><?php echo $stats['resolved']; ?></p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg p-4 border border-blue-100">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="view" value="manage-concerns">
            <?php if ($selectedId): ?>
            <input type="hidden" name="selected" value="<?php echo htmlspecialchars($selectedId); ?>">
            <?php endif; ?>
            
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    placeholder="Search by subject, category, or submitter..."
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <select
                    name="filter"
                    onchange="this.form.submit()"
                    class="px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in-progress" <?php echo $filter === 'in-progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo $filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="rejected" <?php echo $filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Concerns Grid -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Concerns List -->
        <div class="space-y-4">
            <?php if (count($filteredConcerns) > 0): ?>
                <?php foreach ($filteredConcerns as $concern): ?>
                <a href="?view=manage-concerns&selected=<?php echo $concern['id']; ?>&filter=<?php echo urlencode($filter); ?>&search=<?php echo urlencode($searchTerm); ?>"
                   class="block bg-white rounded-lg p-4 border cursor-pointer transition-all <?php echo $selectedConcern && $selectedConcern['id'] === $concern['id'] ? 'border-blue-500 shadow-lg' : 'border-blue-100 hover:border-blue-300'; ?>">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-blue-900 flex-1"><?php echo htmlspecialchars($concern['subject']); ?></h3>
                        <?php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'in-progress' => 'bg-blue-100 text-blue-700',
                            'resolved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700'
                        ];
                        $colorClass = $statusColors[$concern['status']] ?? $statusColors['pending'];
                        ?>
                        <span class="px-3 py-1 rounded-full text-xs <?php echo $colorClass; ?>">
                            <?php echo htmlspecialchars($concern['status']); ?>
                        </span>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-blue-600 mb-2">
                        <span class="px-2 py-1 bg-blue-50 rounded"><?php echo htmlspecialchars($concern['category']); ?></span>
                        <?php if (!empty($concern['location'])): ?>
                        <span>📍 <?php echo htmlspecialchars($concern['location']); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm text-blue-700 mb-2 line-clamp-2"><?php echo htmlspecialchars(substr($concern['description'], 0, 100)) . (strlen($concern['description']) > 100 ? '...' : ''); ?></p>
                    <div class="flex items-center justify-between text-xs text-blue-500">
                        <span>By: <?php echo htmlspecialchars($concern['submittedBy']); ?></span>
                        <span><?php echo date('M d, Y', strtotime($concern['createdAt'])); ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg p-12 text-center border border-blue-100">
                    <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-blue-600">No concerns found</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Concern Details -->
        <div class="lg:sticky lg:top-6">
            <?php if ($selectedConcern): ?>
            <div class="bg-white rounded-lg p-6 border border-blue-100">
                <h3 class="text-blue-900 mb-4">Concern Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-blue-600">Subject</label>
                        <p class="text-blue-900"><?php echo htmlspecialchars($selectedConcern['subject']); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600">Category</label>
                        <p class="text-blue-900"><?php echo htmlspecialchars($selectedConcern['category']); ?></p>
                    </div>

                    <?php if (!empty($selectedConcern['location'])): ?>
                    <div>
                        <label class="text-sm text-blue-600">Location</label>
                        <p class="text-blue-900">📍 <?php echo htmlspecialchars($selectedConcern['location']); ?></p>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="text-sm text-blue-600">Description</label>
                        <p class="text-blue-900"><?php echo htmlspecialchars($selectedConcern['description']); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600">Submitted By</label>
                        <p class="text-blue-900"><?php echo htmlspecialchars($selectedConcern['submittedBy']); ?></p>
                        <p class="text-sm text-blue-700"><?php echo htmlspecialchars($selectedConcern['submittedByEmail']); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600">Date Submitted</label>
                        <p class="text-blue-900">
                            <?php echo date('M d, Y h:i A', strtotime($selectedConcern['createdAt'])); ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600 mb-2 block">Current Status</label>
                        <?php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'in-progress' => 'bg-blue-100 text-blue-700',
                            'resolved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700'
                        ];
                        $colorClass = $statusColors[$selectedConcern['status']] ?? $statusColors['pending'];
                        ?>
                        <span class="px-3 py-1 rounded-full text-xs <?php echo $colorClass; ?>">
                            <?php echo htmlspecialchars($selectedConcern['status']); ?>
                        </span>
                    </div>

                    <div class="pt-4 border-t border-blue-100">
                        <label class="text-sm text-blue-600 mb-3 block">Update Status</label>
                        <div class="grid grid-cols-2 gap-2">
                            <?php if ($selectedConcern['status'] !== 'in-progress'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="update_concern_status" value="1">
                                <input type="hidden" name="concern_id" value="<?php echo $selectedConcern['id']; ?>">
                                <input type="hidden" name="new_status" value="in-progress">
                                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Start Progress</span>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($selectedConcern['status'] !== 'resolved'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="update_concern_status" value="1">
                                <input type="hidden" name="concern_id" value="<?php echo $selectedConcern['id']; ?>">
                                <input type="hidden" name="new_status" value="resolved">
                                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Resolve</span>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($selectedConcern['status'] !== 'rejected'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="update_concern_status" value="1">
                                <input type="hidden" name="concern_id" value="<?php echo $selectedConcern['id']; ?>">
                                <input type="hidden" name="new_status" value="rejected">
                                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Reject</span>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($selectedConcern['status'] !== 'pending'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="update_concern_status" value="1">
                                <input type="hidden" name="concern_id" value="<?php echo $selectedConcern['id']; ?>">
                                <input type="hidden" name="new_status" value="pending">
                                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Reset to Pending</span>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-lg p-12 text-center border border-blue-100">
                <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-blue-600">Select a concern to view details</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    return ob_get_clean();
}
?>