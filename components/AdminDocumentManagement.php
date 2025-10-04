<?php
/**
 * Admin Document Management Component - EXACT MATCH to AdminDocumentManagement.tsx
 * Manage and process barangay document requests
 */

function AdminDocumentManagement() {
    // Load documents from JSON
    $documents = json_decode(file_get_contents(__DIR__ . '/../data/requests.json'), true) ?: [];
    
    // Form processing is now handled in index.php to prevent header errors
    
    // Apply filters
    $filter = $_GET['filter'] ?? 'all';
    $searchTerm = $_GET['search'] ?? '';
    
    $filteredDocuments = array_filter($documents, function($doc) use ($filter, $searchTerm) {
        $statusMatch = $filter === 'all' || $doc['status'] === $filter;
        $searchMatch = empty($searchTerm) || 
                      stripos($doc['documentType'], $searchTerm) !== false ||
                      stripos($doc['requestedBy'], $searchTerm) !== false;
        return $statusMatch && $searchMatch;
    });
    
    // Calculate stats
    $stats = [
        'total' => count($documents),
        'pending' => count(array_filter($documents, fn($d) => $d['status'] === 'pending')),
        'processing' => count(array_filter($documents, fn($d) => $d['status'] === 'processing')),
        'approved' => count(array_filter($documents, fn($d) => $d['status'] === 'approved'))
    ];
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-blue-900 mb-2">Document Request Management</h2>
        <p class="text-blue-600">Review and process barangay document requests</p>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-blue-50 text-blue-700 rounded-lg p-4">
            <p class="text-sm mb-1">Total Requests</p>
            <p class="text-2xl"><?php echo $stats['total']; ?></p>
        </div>
        <div class="bg-yellow-50 text-yellow-700 rounded-lg p-4">
            <p class="text-sm mb-1">Pending</p>
            <p class="text-2xl"><?php echo $stats['pending']; ?></p>
        </div>
        <div class="bg-blue-50 text-blue-700 rounded-lg p-4">
            <p class="text-sm mb-1">Processing</p>
            <p class="text-2xl"><?php echo $stats['processing']; ?></p>
        </div>
        <div class="bg-green-50 text-green-700 rounded-lg p-4">
            <p class="text-sm mb-1">Approved</p>
            <p class="text-2xl"><?php echo $stats['approved']; ?></p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg p-4 border border-blue-100">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="view" value="manage-documents">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    placeholder="Search by document type or requester..."
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
                    <option value="processing" <?php echo $filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="approved" <?php echo $filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Documents List -->
    <div class="bg-white rounded-lg border border-blue-100 overflow-hidden">
        <?php if (count($filteredDocuments) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-blue-50 border-b border-blue-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-blue-900">Document Type</th>
                        <th class="px-6 py-3 text-left text-blue-900">Requested By</th>
                        <th class="px-6 py-3 text-left text-blue-900">Purpose</th>
                        <th class="px-6 py-3 text-left text-blue-900">Date</th>
                        <th class="px-6 py-3 text-left text-blue-900">Status</th>
                        <th class="px-6 py-3 text-left text-blue-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 0; foreach ($filteredDocuments as $doc): ?>
                    <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-blue-50/30'; ?>">
                        <td class="px-6 py-4 text-blue-900"><?php echo htmlspecialchars($doc['documentType']); ?></td>
                        <td class="px-6 py-4 text-blue-700"><?php echo htmlspecialchars($doc['requestedBy']); ?></td>
                        <td class="px-6 py-4 text-blue-700 max-w-xs truncate"><?php echo htmlspecialchars($doc['purpose']); ?></td>
                        <td class="px-6 py-4 text-blue-700 text-sm"><?php echo date('M d, Y', strtotime($doc['createdAt'])); ?></td>
                        <td class="px-6 py-4">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700'
                            ];
                            $colorClass = $statusColors[$doc['status']] ?? $statusColors['pending'];
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs <?php echo $colorClass; ?>">
                                <?php echo htmlspecialchars($doc['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <?php if ($doc['status'] !== 'approved'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>">
                                    <input type="hidden" name="new_status" value="approved">
                                    <button type="submit" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors" title="Approve">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($doc['status'] === 'pending'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>">
                                    <input type="hidden" name="new_status" value="processing">
                                    <button type="submit" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Set to Processing">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($doc['status'] !== 'rejected'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>">
                                    <input type="hidden" name="new_status" value="rejected">
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php $index++; endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-blue-600">No document requests found</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
    return ob_get_clean();
}
?>