<?php
/**
 * Admin Emergency Alerts Management Component
 * Allows administrators to create, edit, and manage emergency alerts
 */

function AdminEmergencyAlerts() {
    $alerts = [];
    $selectedAlert = null;
    $searchTerm = $_GET['search'] ?? '';
    $filterType = $_GET['type'] ?? 'all';
    
    // Get alerts from database
    if (USE_DATABASE) {
        require_once __DIR__ . '/../functions/db_utils.php';
        try {
            $db = getDB();
            $sql = "SELECT * FROM emergency_alerts WHERE 1=1";
            $params = [];
            
            if (!empty($searchTerm)) {
                $sql .= " AND (title LIKE ? OR message LIKE ?)";
                $params[] = "%$searchTerm%";
                $params[] = "%$searchTerm%";
            }
            
            if ($filterType !== 'all') {
                $sql .= " AND type = ?";
                $params[] = $filterType;
            }
            
            $sql .= " ORDER BY priority DESC, created_at DESC";
            
            $stmt = $db->query($sql, $params);
            $alerts = $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Error loading emergency alerts: ' . $e->getMessage());
        }
    }
    
    // Get selected alert for editing
    if (isset($_GET['edit'])) {
        $alertId = $_GET['edit'];
        $selectedAlert = array_filter($alerts, function($alert) use ($alertId) {
            return $alert['id'] == $alertId;
        });
        $selectedAlert = reset($selectedAlert);
    }
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-blue-900 text-2xl font-bold">Emergency Alerts Management</h2>
                <p class="text-blue-600">Manage real-time emergency alerts and notifications</p>
            </div>
            <button onclick="showCreateAlertModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Create Alert</span>
            </button>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg p-4 border border-blue-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-1">
                <input
                    type="text"
                    placeholder="Search alerts..."
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                    onkeyup="searchAlerts(this.value)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <div class="flex space-x-2">
                <select onchange="filterAlerts(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" <?php echo $filterType === 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="emergency" <?php echo $filterType === 'emergency' ? 'selected' : ''; ?>>Emergency</option>
                    <option value="warning" <?php echo $filterType === 'warning' ? 'selected' : ''; ?>>Warning</option>
                    <option value="info" <?php echo $filterType === 'info' ? 'selected' : ''; ?>>Info</option>
                    <option value="resolved" <?php echo $filterType === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Alerts List -->
    <div class="bg-white rounded-lg border border-blue-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider">Alert</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($alerts)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No alerts found</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($alerts as $alert): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($alert['title']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($alert['message'], 0, 100)) . (strlen($alert['message']) > 100 ? '...' : ''); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo getTypeBadgeClass($alert['type']); ?>">
                                    <?php echo ucfirst($alert['type']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo ucfirst($alert['source']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo getPriorityBadgeClass($alert['priority']); ?>">
                                    <?php echo ucfirst($alert['priority']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo getStatusBadgeClass($alert['status']); ?>">
                                    <?php echo ucfirst($alert['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo date('M j, Y H:i', strtotime($alert['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editAlert(<?php echo $alert['id']; ?>)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                    <button onclick="deleteAlert(<?php echo $alert['id']; ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Alert Modal -->
<div id="alertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Create Emergency Alert</h3>
                    <button onclick="closeAlertModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form id="alertForm" method="POST" action="">
                    <input type="hidden" name="action" value="save_emergency_alert">
                    <input type="hidden" name="alert_id" id="alertId" value="">
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alert Type</label>
                                <select name="type" id="alertType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="emergency">Emergency</option>
                                    <option value="warning">Warning</option>
                                    <option value="info">Info</option>
                                    <option value="resolved">Resolved</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                                <select name="source" id="alertSource" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="weather">Weather</option>
                                    <option value="news">News</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="community">Community</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <select name="priority" id="alertPriority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="alertStatus" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="resolved">Resolved</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" name="title" id="alertTitle" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter alert title">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea name="message" id="alertMessage" required rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter alert message"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL (Optional)</label>
                            <input type="url" name="url" id="alertUrl" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expires At (Optional)</label>
                            <input type="datetime-local" name="expires_at" id="alertExpires" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAlertModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Save Alert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showCreateAlertModal() {
    document.getElementById('modalTitle').textContent = 'Create Emergency Alert';
    document.getElementById('alertForm').reset();
    document.getElementById('alertId').value = '';
    document.getElementById('alertModal').classList.remove('hidden');
}

function editAlert(alertId) {
    // This would be implemented to populate the form with existing alert data
    document.getElementById('modalTitle').textContent = 'Edit Emergency Alert';
    document.getElementById('alertId').value = alertId;
    document.getElementById('alertModal').classList.remove('hidden');
}

function closeAlertModal() {
    document.getElementById('alertModal').classList.add('hidden');
}

function deleteAlert(alertId) {
    if (confirm('Are you sure you want to delete this alert?')) {
        // Implement delete functionality
        window.location.href = '?action=delete_emergency_alert&id=' + alertId;
    }
}

function searchAlerts(term) {
    window.location.href = '?search=' + encodeURIComponent(term);
}

function filterAlerts(type) {
    window.location.href = '?type=' + type;
}
</script>
<?php
    return ob_get_clean();
}

function getTypeBadgeClass($type) {
    $classes = [
        'emergency' => 'bg-red-100 text-red-800',
        'warning' => 'bg-orange-100 text-orange-800',
        'info' => 'bg-blue-100 text-blue-800',
        'resolved' => 'bg-green-100 text-green-800'
    ];
    return $classes[$type] ?? 'bg-gray-100 text-gray-800';
}

function getPriorityBadgeClass($priority) {
    $classes = [
        'high' => 'bg-red-100 text-red-800',
        'medium' => 'bg-yellow-100 text-yellow-800',
        'low' => 'bg-green-100 text-green-800'
    ];
    return $classes[$priority] ?? 'bg-gray-100 text-gray-800';
}

function getStatusBadgeClass($status) {
    $classes = [
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-gray-100 text-gray-800',
        'resolved' => 'bg-blue-100 text-blue-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}
?>

