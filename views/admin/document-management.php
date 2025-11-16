<?php
/**
 * Document Management - SYNCHRONIZED with components/AdminDocumentManagement.tsx
 */

$conn = getDBConnection();
$documents = $conn->query("SELECT d.*, u.first_name, u.last_name, u.email FROM documents d JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Calculate stats
$stats = [
    'total' => count($documents),
    'pending' => count(array_filter($documents, fn($d) => $d['status'] === 'pending')),
    'processing' => count(array_filter($documents, fn($d) => $d['status'] === 'processing')),
    'approved' => count(array_filter($documents, fn($d) => $d['status'] === 'approved'))
];
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
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <!-- Search Icon -->
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search by document type or requester..."
                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onkeyup="filterDocuments()"
                />
            </div>
            <div class="flex items-center space-x-2">
                <!-- Filter Icon -->
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <select
                    id="statusFilter"
                    class="px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                    onchange="filterDocuments()"
                >
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="bg-white rounded-lg border border-blue-100 overflow-hidden">
        <?php if (count($documents) > 0): ?>
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
                    <tbody id="documentsTableBody">
                        <?php foreach ($documents as $index => $doc): 
                            $requesterName = htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']);
                            $rowClass = $index % 2 === 0 ? 'bg-white' : 'bg-blue-50/30';
                        ?>
                            <tr class="document-row <?php echo $rowClass; ?>" 
                                data-status="<?php echo $doc['status']; ?>"
                                data-search="<?php echo strtolower($doc['document_type'] . ' ' . $requesterName); ?>">
                                <td class="px-6 py-4 text-blue-900"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $doc['document_type']))); ?></td>
                                <td class="px-6 py-4 text-blue-700"><?php echo $requesterName; ?></td>
                                <td class="px-6 py-4 text-blue-700 max-w-xs truncate"><?php echo htmlspecialchars($doc['purpose']); ?></td>
                                <td class="px-6 py-4 text-blue-700 text-sm">
                                    <?php echo date('n/j/Y', strtotime($doc['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700'
                                    ];
                                    $statusColor = $statusColors[$doc['status']] ?? $statusColors['pending'];
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs <?php echo $statusColor; ?>">
                                        <?php echo $doc['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <?php if ($doc['status'] !== 'approved'): ?>
                                            <button
                                                onclick="updateDocumentStatus(<?php echo $doc['id']; ?>, 'approved')"
                                                class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                                title="Approve"
                                            >
                                                <!-- CheckCircle Icon -->
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($doc['status'] === 'pending'): ?>
                                            <button
                                                onclick="updateDocumentStatus(<?php echo $doc['id']; ?>, 'processing')"
                                                class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                                title="Set to Processing"
                                            >
                                                <!-- Clock Icon -->
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($doc['status'] !== 'rejected'): ?>
                                            <button
                                                onclick="updateDocumentStatus(<?php echo $doc['id']; ?>, 'rejected')"
                                                class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                                title="Reject"
                                            >
                                                <!-- XCircle Icon -->
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <!-- FileText Icon -->
                <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-blue-600">No document requests found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterDocuments() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.document-row');
    
    rows.forEach(row => {
        const searchData = row.getAttribute('data-search');
        const status = row.getAttribute('data-status');
        
        const matchesSearch = searchData.includes(searchTerm);
        const matchesStatus = statusFilter === 'all' || status === statusFilter;
        
        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function updateDocumentStatus(id, newStatus) {
    // Show loading state
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    fetch('/api/documents.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update',
            id: id,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const statusText = newStatus === 'approved' ? 'approved' : newStatus === 'rejected' ? 'rejected' : 'updated to processing';
            showToast('success', `Document ${statusText} successfully`, `The document request has been ${statusText}.`);
            
            // Update the UI without page reload - SYNCHRONIZED with TSX
            const row = button.closest('tr');
            const statusCell = row.querySelector('td:nth-child(5)'); // Status column
            const actionsCell = row.querySelector('td:nth-child(6)'); // Actions column
            
            // Update status badge
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-700',
                'processing': 'bg-blue-100 text-blue-700',
                'approved': 'bg-green-100 text-green-700',
                'rejected': 'bg-red-100 text-red-700'
            };
            statusCell.innerHTML = `<span class="px-3 py-1 rounded-full text-xs ${statusColors[newStatus]}">${newStatus}</span>`;
            
            // Update data attribute for filtering
            row.setAttribute('data-status', newStatus);
            
            // Update action buttons based on new status
            let newButtons = '<div class="flex items-center space-x-2">';
            
            // Approve button (show if not already approved)
            if (newStatus !== 'approved') {
                newButtons += `
                    <button
                        onclick="updateDocumentStatus(${id}, 'approved')"
                        class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                        title="Approve"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                `;
            }
            
            // Processing button (show only if pending)
            if (newStatus === 'pending') {
                newButtons += `
                    <button
                        onclick="updateDocumentStatus(${id}, 'processing')"
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                        title="Set to Processing"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                `;
            }
            
            // Reject button (show if not already rejected)
            if (newStatus !== 'rejected') {
                newButtons += `
                    <button
                        onclick="updateDocumentStatus(${id}, 'rejected')"
                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                        title="Reject"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                `;
            }
            
            newButtons += '</div>';
            actionsCell.innerHTML = newButtons;
            
            // Update stats without reload
            updateStats();
        } else {
            showToast('error', 'Failed to update status', data.error || 'Please try again.');
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to update status', 'An error occurred. Please try again.');
        button.disabled = false;
        button.innerHTML = originalHTML;
    });
}

// Update statistics without page reload
function updateStats() {
    const rows = document.querySelectorAll('.document-row');
    const stats = {
        total: rows.length,
        pending: 0,
        processing: 0,
        approved: 0
    };
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        if (status === 'pending') stats.pending++;
        if (status === 'processing') stats.processing++;
        if (status === 'approved') stats.approved++;
    });
    
    // Update stat cards
    const statCards = document.querySelectorAll('.grid.md\\:grid-cols-4 > div');
    if (statCards.length >= 4) {
        statCards[0].querySelector('.text-2xl').textContent = stats.total;
        statCards[1].querySelector('.text-2xl').textContent = stats.pending;
        statCards[2].querySelector('.text-2xl').textContent = stats.processing;
        statCards[3].querySelector('.text-2xl').textContent = stats.approved;
    }
}

// Toast notification function
function showToast(type, title, message) {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50 transform transition-all duration-300 ease-in-out ${type === 'success' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'}`;
    
    toast.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${type === 'success' 
                        ? '<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        : '<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    }
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="font-medium text-gray-900">${title}</p>
                    <p class="mt-1 text-sm text-gray-500">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="this.closest('.fixed').remove()" class="inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}
</script>