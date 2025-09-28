<!-- Announcement Modal -->
<div id="announcementModal" class="modal-overlay hidden">
    <div class="modal-content p-0 w-full max-w-lg">
        <div class="bg-white rounded-lg">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-semibold">Create Announcement</h3>
                <button type="button" onclick="closeModal('announcementModal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="announcementForm" action="/api/create-announcement.php" method="POST" class="space-y-4" onsubmit="event.preventDefault(); submitForm(this, handleAnnouncementCreate);">
                    <div>
                        <label for="announcementTitle" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" id="announcementTitle" name="title" required class="input" placeholder="Announcement title">
                    </div>
                    
                    <div>
                        <label for="announcementType" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="announcementType" name="type" required class="input">
                            <option value="">Select type</option>
                            <option value="general">General</option>
                            <option value="event">Event</option>
                            <option value="emergency">Emergency</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="announcementPriority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select id="announcementPriority" name="priority" required class="input">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="announcementContent" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea id="announcementContent" name="content" rows="4" required class="input" placeholder="Announcement content..."></textarea>
                    </div>
                    
                    <div>
                        <label for="announcementStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="announcementStatus" name="status" required class="input">
                            <option value="draft">Save as Draft</option>
                            <option value="published" selected>Publish Immediately</option>
                        </select>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">
                            <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                            Publishing Guidelines
                        </h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Published announcements are immediately visible to all users</li>
                            <li>• Emergency announcements will be highlighted prominently</li>
                            <li>• Drafts can be edited and published later</li>
                            <li>• Use clear, concise language for better understanding</li>
                        </ul>
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="flex-1 btn-primary">Create Announcement</button>
                        <button type="button" onclick="closeModal('announcementModal')" class="flex-1 btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function handleAnnouncementCreate(response) {
        showToast('Announcement created successfully!');
        closeModal('announcementModal');
        
        // Reset form
        document.getElementById('announcementForm').reset();
        document.getElementById('announcementPriority').value = 'medium';
        document.getElementById('announcementStatus').value = 'published';
        
        // Refresh page to show changes
        setTimeout(() => {
            location.reload();
        }, 1500);
    }
</script>