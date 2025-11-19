<?php
/**
 * Concern Management - SYNCHRONIZED with components/AdminConcernManagement.tsx
 */

$db = getDB();
$concerns = fetchAll("SELECT c.*, u.first_name, u.last_name, u.email FROM concerns c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");

// Calculate stats
$stats = [
    'total' => count($concerns),
    'pending' => count(array_filter($concerns, fn($c) => $c['status'] === 'pending')),
    'inProgress' => count(array_filter($concerns, fn($c) => str_replace(['_', ' '], '-', $c['status']) === 'in-progress')),
    'resolved' => count(array_filter($concerns, fn($c) => $c['status'] === 'resolved'))
];
?>

<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-gray-900 text-xl font-semibold">Community Concerns</h2>
                <p class="text-gray-600">Manage and resolve community issues efficiently</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Total Concerns</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
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
                <h4 class="text-sm font-semibold text-gray-900">In Progress</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['inProgress']; ?></p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Resolved</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['resolved']; ?></p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search by subject, category, or submitter name..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 placeholder-gray-400"
                    onkeyup="filterConcerns()"
                />
            </div>
            <div class="flex items-center space-x-2">
                <select
                    id="statusFilter"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900"
                    onchange="filterConcerns()"
                >
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select
                    id="categoryFilter"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900"
                    onchange="filterConcerns()"
                >
                    <option value="all">All Categories</option>
                    <option value="infrastructure">Infrastructure</option>
                    <option value="sanitation">Sanitation</option>
                    <option value="security">Security</option>
                    <option value="environment">Environment</option>
                    <option value="health">Health</option>
                    <option value="other">Other</option>
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
                        class="concern-card bg-white rounded-lg p-6 border border-gray-200 hover:border-gray-300 cursor-pointer transition-all duration-200"
                        data-concern-id="<?php echo $concern['id']; ?>"
                        data-status="<?php echo $displayStatus; ?>"
                        data-category="<?php echo $concern['category']; ?>"
                        data-user-id="<?php echo $concern['user_id']; ?>"
                        data-search="<?php echo strtolower($concern['subject'] . ' ' . $concern['category'] . ' ' . $submitterName); ?>"
                        onclick="selectConcern(<?php echo htmlspecialchars(json_encode($concern)); ?>)"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-gray-900 font-semibold flex-1"><?php echo htmlspecialchars($concern['subject']); ?></h3>
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
                            <span class="concern-status-badge px-3 py-1 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                <?php echo str_replace('_', '-', $concern['status']); ?>
                            </span>
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span class="px-3 py-1 bg-gray-100 rounded-lg font-medium text-gray-800"><?php echo htmlspecialchars(ucfirst($concern['category'])); ?></span>
                            <?php if (!empty($concern['location'])): ?>
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($concern['location']); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-700 mb-3 line-clamp-2"><?php echo htmlspecialchars($concern['description']); ?></p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span><?php echo $submitterName; ?></span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><?php echo date('n/j/Y', strtotime($concern['created_at'])); ?></span>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-lg p-12 text-center border border-gray-200 shadow-sm">
                        <!-- AlertCircle Icon -->
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600">No concerns found</p>
                    </div>
                <?php endif; ?>
        </div>

        <!-- Concern Details -->
<div class="lg:sticky lg:top-6">
    <div id="concernDetails" class="bg-white rounded-lg p-6 border border-gray-200">
        <div class="text-center py-6">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                    </path>
                </svg>
            </div>
            <p class="text-gray-600">Select a document to view details</p>
        </div>
    </div>
</div>

    </div>
</div>

<script>
let selectedConcernId = null;

function filterConcerns() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const cards = document.querySelectorAll('.concern-card');

    cards.forEach(card => {
        const searchData = card.getAttribute('data-search');
        const status = card.getAttribute('data-status');
        const category = card.getAttribute('data-category') || 'other';

        const matchesSearch = searchData.includes(searchTerm);
        const matchesStatus = statusFilter === 'all' || status === statusFilter;
        const matchesCategory = categoryFilter === 'all' || category === categoryFilter;

        if (matchesSearch && matchesStatus && matchesCategory) {
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
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Concern Details</h3>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-9 0V1m10 3V1m0 3l1 1v16a2 2 0 01-2 2H6a2 2 0 01-2-2V5l1-1z"></path>
                        </svg>
                        <span>Subject</span>
                    </label>
                    <p class="text-gray-900">${concern.subject}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Category</span>
                    </label>
                    <p class="text-gray-900">${concern.category.charAt(0).toUpperCase() + concern.category.slice(1)}</p>
                </div>

                ${concern.location ? `
                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Location</span>
                    </label>
                    <p class="text-gray-900">${concern.location}</p>
                </div>
                ` : ''}

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span>Description</span>
                    </label>
                    <p class="text-gray-900">${concern.description}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Submitted By</span>
                    </label>
                    <p class="text-gray-900">${submitterName}</p>
                    <p class="text-sm text-gray-700">${concern.email}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Date Submitted</span>
                    </label>
                    <p class="text-gray-900">${new Date(concern.created_at).toLocaleString()}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 mb-1">Current Status</label>
                    <p class="text-gray-900 capitalize">${displayStatus}</p>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <label class="text-sm font-semibold text-gray-700">Update Status</label>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
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

                // Create notification for the user
                const userId = concernCard.getAttribute('data-user-id');
                fetch('/api/notifications.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_id: parseInt(userId),
                        type: 'info',
                        title: 'Concern Status Updated',
                        message: `Your concern has been ${newStatus.replace('_', ' ')}.`,
                        related_type: 'concern',
                        related_id: id
                    })
                }).catch(error => console.error('Error creating notification:', error));

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

                    // Update the action buttons in the details panel
                    const actionButtonsContainer = document.querySelector('#concernDetails .grid.grid-cols-2');
                    if (actionButtonsContainer) {
                        let newButtons = '';

                        // In Progress button
                        if (newStatus !== 'in_progress' && newStatus !== 'in-progress') {
                            newButtons += `
                            <button
                                onclick="updateConcernStatus(${id}, 'in-progress')"
                                class="flex items-center justify-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Start Progress</span>
                            </button>
                            `;
                        }

                        // Resolved button
                        if (newStatus !== 'resolved') {
                            newButtons += `
                            <button
                                onclick="updateConcernStatus(${id}, 'resolved')"
                                class="flex items-center justify-center space-x-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Resolve</span>
                            </button>
                            `;
                        }

                        // Reject button
                        if (newStatus !== 'rejected') {
                            newButtons += `
                            <button
                                onclick="updateConcernStatus(${id}, 'rejected')"
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
                                onclick="updateConcernStatus(${id}, 'pending')"
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

// Toast notification function
function showToast(type, title, message) {
    // Ensure DOM is ready
    if (!document.body) {
        console.warn('DOM not ready, retrying toast in 100ms');
        setTimeout(() => showToast(type, title, message), 100);
        return;
    }

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
        if (toast && toast.parentNode) {
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
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
        statCards[0].querySelector('.text-3xl').textContent = stats.total;
        statCards[1].querySelector('.text-3xl').textContent = stats.pending;
        statCards[2].querySelector('.text-3xl').textContent = stats.inProgress;
        statCards[3].querySelector('.text-3xl').textContent = stats.resolved;
    }
}

// Auto-refresh stats every 10 seconds
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
                // Add visual feedback for stat changes
                currentStats[index].textContent = newStat.textContent;
                currentStats[index].classList.add('text-green-600', 'font-bold');
                setTimeout(() => {
                    currentStats[index].classList.remove('text-green-600', 'font-bold');
                }, 2000);
            }
        });
    })
    .catch(error => console.log('Stats refresh failed:', error));
}, 10000);

// Auto-refresh concern list every 15 seconds
let lastConcernCount = <?php echo count($concerns); ?>;
setInterval(() => {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Check if there are new concerns
        const newConcernCards = doc.querySelectorAll('.concern-card');
        const currentConcernCards = document.querySelectorAll('.concern-card');

        if (newConcernCards.length > currentConcernCards.length) {
            // New concerns added - refresh the entire list
            const newConcernList = doc.querySelector('.space-y-4');
            if (newConcernList) {
                document.querySelector('.space-y-4').innerHTML = newConcernList.innerHTML;

                // Show notification for new concerns
                const newCount = newConcernCards.length - currentConcernCards.length;
                showToast('info', 'New Concerns Added', `${newCount} new concern${newCount > 1 ? 's' : ''} submitted and ready for review.`);

                // Highlight new cards briefly
                setTimeout(() => {
                    const newCards = document.querySelectorAll('.concern-card');
                    for (let i = 0; i < newCount; i++) {
                        if (newCards[i]) {
                            newCards[i].classList.add('border-green-500', 'bg-green-50');
                            setTimeout(() => {
                                newCards[i].classList.remove('border-green-500', 'bg-green-50');
                            }, 3000);
                        }
                    }
                }, 500);
            }
        }
    })
    .catch(error => console.log('Concern list refresh failed:', error));
}, 15000);
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.status-badge {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
