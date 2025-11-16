<?php
/**
 * Concern Management - SYNCHRONIZED with components/AdminConcernManagement.tsx
 */

$conn = getDBConnection();
$concerns = $conn->query("SELECT c.*, u.first_name, u.last_name, u.email FROM concerns c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Calculate stats
$stats = [
    'total' => count($concerns),
    'pending' => count(array_filter($concerns, fn($c) => $c['status'] === 'pending')),
    'inProgress' => count(array_filter($concerns, fn($c) => $c['status'] === 'in_progress' || $c['status'] === 'in-progress')),
    'resolved' => count(array_filter($concerns, fn($c) => $c['status'] === 'resolved'))
];
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
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <!-- Search Icon -->
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search by subject, category, or submitter..."
                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onkeyup="filterConcerns()"
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
                    onchange="filterConcerns()"
                >
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Concerns Grid -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Concerns List -->
        <div class="space-y-4">
            <?php if (count($concerns) > 0): ?>
                <?php foreach ($concerns as $concern): 
                    $submitterName = htmlspecialchars($concern['first_name'] . ' ' . $concern['last_name']);
                    $displayStatus = str_replace('_', '-', $concern['status']);
                ?>
                    <div
                        class="concern-card bg-white rounded-lg p-4 border border-blue-100 hover:border-blue-300 cursor-pointer transition-all"
                        data-concern-id="<?php echo $concern['id']; ?>"
                        data-status="<?php echo $displayStatus; ?>"
                        data-search="<?php echo strtolower($concern['subject'] . ' ' . $concern['category'] . ' ' . $submitterName); ?>"
                        onclick="selectConcern(<?php echo htmlspecialchars(json_encode($concern)); ?>)"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-blue-900 flex-1"><?php echo htmlspecialchars($concern['subject']); ?></h3>
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in-progress' => 'bg-blue-100 text-blue-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'resolved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700'
                            ];
                            $statusColor = $statusColors[$concern['status']] ?? $statusColors['pending'];
                            ?>
                            <span class="concern-status-badge px-3 py-1 rounded-full text-xs <?php echo $statusColor; ?>">
                                <?php echo str_replace('_', '-', $concern['status']); ?>
                            </span>
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-blue-600 mb-2">
                            <span class="px-2 py-1 bg-blue-50 rounded"><?php echo htmlspecialchars(ucfirst($concern['category'])); ?></span>
                            <?php if (!empty($concern['location'])): ?>
                                <span>📍 <?php echo htmlspecialchars($concern['location']); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-blue-700 mb-2 line-clamp-2"><?php echo htmlspecialchars($concern['description']); ?></p>
                        <div class="flex items-center justify-between text-xs text-blue-500">
                            <span>By: <?php echo $submitterName; ?></span>
                            <span><?php echo date('n/j/Y', strtotime($concern['created_at'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg p-12 text-center border border-blue-100">
                    <!-- AlertCircle Icon -->
                    <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-blue-600">No concerns found</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Concern Details -->
        <div class="lg:sticky lg:top-6">
            <div id="concernDetails" class="bg-white rounded-lg p-12 text-center border border-blue-100">
                <!-- AlertCircle Icon -->
                <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-blue-600">Select a concern to view details</p>
            </div>
        </div>
    </div>
</div>

<script>
let selectedConcernId = null;

function filterConcerns() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('.concern-card');
    
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

function selectConcern(concern) {
    selectedConcernId = concern.id;
    
    // Highlight selected card
    document.querySelectorAll('.concern-card').forEach(card => {
        if (card.getAttribute('data-concern-id') == concern.id) {
            card.classList.add('border-blue-500', 'shadow-lg');
            card.classList.remove('border-blue-100');
        } else {
            card.classList.remove('border-blue-500', 'shadow-lg');
            card.classList.add('border-blue-100');
        }
    });
    
    const submitterName = concern.first_name + ' ' + concern.last_name;
    const displayStatus = concern.status.replace('_', '-');
    
    const statusColors = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'in-progress': 'bg-blue-100 text-blue-700',
        'resolved': 'bg-green-100 text-green-700',
        'rejected': 'bg-red-100 text-red-700'
    };
    const statusColor = statusColors[displayStatus] || statusColors['pending'];
    
    const detailsHTML = `
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <h3 class="text-blue-900 mb-4">Concern Details</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-blue-600">Subject</label>
                    <p class="text-blue-900">${concern.subject}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600">Category</label>
                    <p class="text-blue-900">${concern.category.charAt(0).toUpperCase() + concern.category.slice(1)}</p>
                </div>

                ${concern.location ? `
                <div>
                    <label class="text-sm text-blue-600">Location</label>
                    <p class="text-blue-900">📍 ${concern.location}</p>
                </div>
                ` : ''}

                <div>
                    <label class="text-sm text-blue-600">Description</label>
                    <p class="text-blue-900">${concern.description}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600">Submitted By</label>
                    <p class="text-blue-900">${submitterName}</p>
                    <p class="text-sm text-blue-700">${concern.email}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600">Date Submitted</label>
                    <p class="text-blue-900">${new Date(concern.created_at).toLocaleString()}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600 mb-2 block">Current Status</label>
                    <span class="px-3 py-1 rounded-full text-xs ${statusColor}">
                        ${displayStatus}
                    </span>
                </div>

                <div class="pt-4 border-t border-blue-100">
                    <label class="text-sm text-blue-600 mb-3 block">Update Status</label>
                    <div class="grid grid-cols-2 gap-2">
                        ${concern.status !== 'in_progress' && concern.status !== 'in-progress' ? `
                        <button
                            onclick="updateConcernStatus(${concern.id}, 'in-progress')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Start Progress</span>
                        </button>
                        ` : ''}
                        ${concern.status !== 'resolved' ? `
                        <button
                            onclick="updateConcernStatus(${concern.id}, 'resolved')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Resolve</span>
                        </button>
                        ` : ''}
                        ${concern.status !== 'rejected' ? `
                        <button
                            onclick="updateConcernStatus(${concern.id}, 'rejected')"
                            class="flex items-center justify-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Reject</span>
                        </button>
                        ` : ''}
                        ${concern.status !== 'pending' ? `
                        <button
                            onclick="updateConcernStatus(${concern.id}, 'pending')"
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
    
    document.getElementById('concernDetails').innerHTML = detailsHTML;
}

function updateConcernStatus(id, newStatus) {
    // Show loading state
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    fetch('/api/concerns.php', {
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
            const statusMessages = {
                'in-progress': 'Concern marked as in progress',
                'resolved': 'Concern marked as resolved',
                'rejected': 'Concern has been rejected',
                'pending': 'Concern reset to pending'
            };
            showToast('success', 'Status Updated', statusMessages[newStatus] || 'Concern status updated successfully.');
            
            // Update the UI without page reload - SYNCHRONIZED with TSX
            const concernCard = document.querySelector(`[data-concern-id="${id}"]`);
            if (concernCard) {
                // Update status badge
                const statusBadge = concernCard.querySelector('.concern-status-badge');
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-700',
                    'in-progress': 'bg-blue-100 text-blue-700',
                    'resolved': 'bg-green-100 text-green-700',
                    'rejected': 'bg-red-100 text-red-700'
                };
                const displayStatus = newStatus.replace('_', '-');
                statusBadge.className = `concern-status-badge px-3 py-1 rounded-full text-xs ${statusColors[displayStatus]}`;
                statusBadge.textContent = displayStatus;
                
                // Update data attribute for filtering
                concernCard.setAttribute('data-status', newStatus);
                
                // Update action buttons
                const actionsDiv = concernCard.querySelector('.concern-actions');
                if (actionsDiv) {
                    let newButtons = '';
                    
                    // In Progress button
                    if (newStatus !== 'in-progress') {
                        newButtons += `
                            <button onclick="updateConcernStatus(${id}, 'in-progress')" 
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" 
                                    title="Mark as In Progress">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                        `;
                    }
                    
                    // Resolved button
                    if (newStatus !== 'resolved') {
                        newButtons += `
                            <button onclick="updateConcernStatus(${id}, 'resolved')" 
                                    class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors" 
                                    title="Mark as Resolved">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                        `;
                    }
                    
                    // Reject button
                    if (newStatus !== 'rejected') {
                        newButtons += `
                            <button onclick="updateConcernStatus(${id}, 'rejected')" 
                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" 
                                    title="Reject">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                        `;
                    }
                    
                    actionsDiv.innerHTML = newButtons;
                }
                
                // Update stats
                updateConcernStats();
                
                // If concern details are showing, update them too
                if (selectedConcernId === id) {
                    const detailsStatusBadge = document.querySelector('#concernDetails .concern-status-badge');
                    if (detailsStatusBadge) {
                        const statusColors = {
                            'pending': 'bg-yellow-100 text-yellow-700',
                            'in-progress': 'bg-blue-100 text-blue-700',
                            'resolved': 'bg-green-100 text-green-700',
                            'rejected': 'bg-red-100 text-red-700'
                        };
                        const displayStatus = newStatus.replace('_', '-');
                        detailsStatusBadge.className = `concern-status-badge px-3 py-1 rounded-full text-xs ${statusColors[displayStatus]}`;
                        detailsStatusBadge.textContent = displayStatus;
                    }
                }
            }
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

function updateConcernStats() {
    const concerns = document.querySelectorAll('.concern-card');
    const stats = {
        total: concerns.length,
        pending: 0,
        inProgress: 0,
        resolved: 0
    };
    
    concerns.forEach(card => {
        const status = card.getAttribute('data-status');
        if (status === 'pending') {
            stats.pending++;
        } else if (status === 'in-progress' || status === 'in_progress') {
            stats.inProgress++;
        } else if (status === 'resolved') {
            stats.resolved++;
        }
    });
    
    // Update stat cards
    const statCards = document.querySelectorAll('.grid.md\\:grid-cols-4 > div');
    if (statCards.length >= 4) {
        statCards[0].querySelector('.text-2xl').textContent = stats.total;
        statCards[1].querySelector('.text-2xl').textContent = stats.pending;
        statCards[2].querySelector('.text-2xl').textContent = stats.inProgress;
        statCards[3].querySelector('.text-2xl').textContent = stats.resolved;
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>