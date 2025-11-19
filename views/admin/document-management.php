<?php
/**
 * Document Management - SYNCHRONIZED with components/AdminDocumentManagement.tsx
 */

$db = getDB();
$documents = fetchAll("SELECT d.*, u.first_name, u.last_name, u.email FROM documents d JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC");

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
    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
        <h2 class="text-gray-900 text-xl font-semibold">Document Request Management</h2>
        <p class="text-gray-600">Review and process barangay document requests</p>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Total Requests</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Pending</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['pending']; ?></p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Processing</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['processing']; ?></p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Approved</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['approved']; ?></p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <!-- Search Icon -->
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search by document type or requester..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 placeholder-gray-400"
                    onkeyup="filterDocuments()"
                />
            </div>
            <div class="flex items-center space-x-2">
                <!-- Filter Icon -->
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <select
                    id="statusFilter"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900"
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

    <!-- Documents Grid -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Documents List -->
        <div class="space-y-4">
            <?php if (count($documents) > 0): ?>
                <?php foreach ($documents as $doc):
                    $requesterName = htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']);
                ?>
                    <div
                        class="document-card bg-white rounded-lg p-6 border border-gray-200 hover:border-gray-300 cursor-pointer transition-all duration-200"
                        data-status="<?php echo $doc['status']; ?>"
                        data-user-id="<?php echo $doc['user_id']; ?>"
                        data-search="<?php echo strtolower($doc['document_type'] . ' ' . $requesterName); ?>"
                        onclick="selectDocument(<?php echo htmlspecialchars(json_encode($doc)); ?>)"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-black font-semibold flex-1"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $doc['document_type']))); ?></h3>
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700'
                            ];
                            $statusColor = $statusColors[$doc['status']] ?? $statusColors['pending'];
                            ?>
                            <span class="document-status-badge px-3 py-1 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                <?php echo $doc['status']; ?>
                            </span>
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span class="px-3 py-1 bg-gray-100 rounded-lg font-medium text-gray-800"><?php echo htmlspecialchars($doc['purpose']); ?></span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Requested by: <?php echo $requesterName; ?></p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><?php echo date('n/j/Y', strtotime($doc['created_at'])); ?></span>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-lg p-12 text-center border border-gray-200">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600">No document requests found</p>
                    </div>
                <?php endif; ?>
        </div>

        <!-- Document Details -->
        <div class="lg:sticky lg:top-6">
            <div id="documentDetails" class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="text-center py-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600">Select a document to view details</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterDocuments() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('.document-card');

    cards.forEach(card => {
        const searchData = card.getAttribute('data-search');
        const status = card.getAttribute('data-status');

        const matchesSearch = searchData.includes(searchTerm);
        const matchesStatus = statusFilter === 'all' || status === statusFilter;

        if (matchesSearch && matchesStatus) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function selectDocument(doc) {
    // Highlight selected card
    document.querySelectorAll('.document-card').forEach(card => {
        if (card.getAttribute('data-user-id') == doc.user_id && card.getAttribute('data-status') === doc.status) {
            card.classList.add('border-blue-500', 'shadow-lg');
            card.classList.remove('border-gray-200');
        } else {
            card.classList.remove('border-blue-500', 'shadow-lg');
            card.classList.add('border-gray-200');
        }
    });

    const requesterName = doc.first_name + ' ' + doc.last_name;
    const displayStatus = doc.status;

    const statusColors = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'processing': 'bg-blue-100 text-blue-700',
        'approved': 'bg-green-100 text-green-700',
        'rejected': 'bg-red-100 text-red-700'
    };
    const statusColor = statusColors[displayStatus] || statusColors['pending'];

    const detailsHTML = `
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Document Details</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Document Type</span>
                    </label>
                    <p class="text-gray-900">${doc.document_type.charAt(0).toUpperCase() + doc.document_type.slice(1).replace(/_/g, ' ')}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Purpose</span>
                    </label>
                    <p class="text-gray-900">${doc.purpose}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Requested By</span>
                    </label>
                    <p class="text-gray-900">${requesterName}</p>
                    <p class="text-sm text-gray-700">${doc.email}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Date Requested</span>
                    </label>
                    <p class="text-gray-900">${new Date(doc.created_at).toLocaleString()}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 mb-1">Current Status</label>
                    <p class="text-gray-900 capitalize">${displayStatus}</p>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <label class="text-sm font-semibold text-gray-700">Update Status</label>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        ${doc.status !== 'approved' ? `
                        <button
                            onclick="updateDocumentStatus(${doc.id}, 'approved')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Approve</span>
                        </button>
                        ` : ''}
                        ${doc.status === 'pending' ? `
                        <button
                            onclick="updateDocumentStatus(${doc.id}, 'processing')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Start Processing</span>
                        </button>
                        ` : ''}
                        ${doc.status !== 'rejected' ? `
                        <button
                            onclick="updateDocumentStatus(${doc.id}, 'rejected')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Reject</span>
                        </button>
                        ` : ''}
                        ${doc.status !== 'pending' ? `
                        <button
                            onclick="updateDocumentStatus(${doc.id}, 'pending')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Reset to Pending</span>
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('documentDetails').innerHTML = detailsHTML;
}

function updateDocumentStatus(id, newStatus) {
    // Show loading state
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

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
            const card = document.querySelector('.document-card.border-blue-500');
            const statusBadge = card.querySelector('.document-status-badge');

            // Create notification for the user
            const documentId = id;
            fetch('/api/notifications.php?action=create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: parseInt(card.getAttribute('data-user-id') || '0'),
                    type: 'info',
                    title: 'Document Status Updated',
                    message: `Your document request has been ${newStatus}.`,
                    related_type: 'document',
                    related_id: documentId
                })
            }).catch(error => console.error('Error creating notification:', error));

            // Update status badge
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-700',
                'processing': 'bg-blue-100 text-blue-700',
                'approved': 'bg-green-100 text-green-700',
                'rejected': 'bg-red-100 text-red-700'
            };
            statusBadge.className = `document-status-badge px-3 py-1 rounded-full text-xs font-medium ${statusColors[newStatus]}`;
            statusBadge.textContent = newStatus;

            // Update data attribute for filtering
            card.setAttribute('data-status', newStatus);

            // Update stats without reload
            updateStats();

            // If document details are showing, update them too
            if (document.getElementById('documentDetails').querySelector('.status-badge')) {
                const detailsStatusBadge = document.querySelector('#documentDetails .status-badge');
                if (detailsStatusBadge) {
                    const statusColors = {
                        'pending': 'bg-yellow-100 text-yellow-700',
                        'processing': 'bg-blue-100 text-blue-700',
                        'approved': 'bg-green-100 text-green-700',
                        'rejected': 'bg-red-100 text-red-700'
                    };
                    detailsStatusBadge.className = `status-badge px-4 py-2 rounded-lg text-sm font-semibold ${statusColors[newStatus]}`;
                    detailsStatusBadge.textContent = newStatus;
                }

                // Update the action buttons in the details panel
                const actionButtonsContainer = document.querySelector('#documentDetails .grid.grid-cols-2');
                if (actionButtonsContainer) {
                    let newButtons = '';

                    // Approve button
                    if (newStatus !== 'approved') {
                        newButtons += `
                        <button
                            onclick="updateDocumentStatus(${id}, 'approved')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Approve</span>
                        </button>
                        `;
                    }

                    // Processing button
                    if (newStatus === 'pending') {
                        newButtons += `
                        <button
                            onclick="updateDocumentStatus(${id}, 'processing')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Start Processing</span>
                        </button>
                        `;
                    }

                    // Reject button
                    if (newStatus !== 'rejected') {
                        newButtons += `
                        <button
                            onclick="updateDocumentStatus(${id}, 'rejected')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Reject</span>
                        </button>
                        `;
                    }

                    // Reset to Pending button
                    if (newStatus !== 'pending') {
                        newButtons += `
                        <button
                            onclick="updateDocumentStatus(${id}, 'pending')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Reset to Pending</span>
                        </button>
                        `;
                    }

                    actionButtonsContainer.innerHTML = newButtons;
                }
            }

            // Re-enable button and restore original content after 1 second
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalHTML;
            }, 1000);
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
        statCards[0].querySelector('.text-3xl').textContent = stats.total;
        statCards[1].querySelector('.text-3xl').textContent = stats.pending;
        statCards[2].querySelector('.text-3xl').textContent = stats.processing;
        statCards[3].querySelector('.text-3xl').textContent = stats.approved;
    }
}

// Auto-refresh stats every 30 seconds without page reload
setInterval(() => {
    // Refresh stats via AJAX
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Extract and update stats from the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newStats = doc.querySelectorAll('.grid.md\\:grid-cols-4 > div .text-3xl');
        const currentStats = document.querySelectorAll('.grid.md\\:grid-cols-4 > div .text-3xl');

        newStats.forEach((newStat, index) => {
            if (currentStats[index] && currentStats[index].textContent !== newStat.textContent) {
                currentStats[index].textContent = newStat.textContent;
            }
        });
    })
    .catch(error => console.log('Stats refresh failed:', error));
}, 30000);

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