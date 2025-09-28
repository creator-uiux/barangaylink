<!-- Profile Modal -->
<div id="profileModal" class="modal-overlay hidden">
    <div class="modal-content p-0 w-full max-w-md">
        <div class="bg-white rounded-lg">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-semibold">Admin Profile Settings</h3>
                <button type="button" onclick="closeModal('profileModal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="profileForm" action="/api/update-profile.php" method="POST" class="space-y-4" onsubmit="event.preventDefault(); submitForm(this, handleProfileUpdate);">
                    <div>
                        <label for="profileFullName" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="profileFullName" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required class="input">
                    </div>
                    
                    <div>
                        <label for="profileEmail" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="profileEmail" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="input">
                    </div>
                    
                    <div>
                        <label for="profileAddress" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="profileAddress" name="address" rows="2" class="input"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label for="profilePhone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="profilePhone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="input">
                    </div>
                    
                    <div>
                        <label for="profileBirthDate" class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                        <input type="date" id="profileBirthDate" name="birth_date" value="<?php echo $user['birth_date'] ?? ''; ?>" class="input">
                    </div>
                    
                    <hr class="my-4">
                    
                    <h4 class="font-medium text-gray-900 mb-2">Change Password</h4>
                    <p class="text-sm text-gray-600 mb-4">Leave blank to keep current password</p>
                    
                    <div>
                        <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="currentPassword" name="current_password" class="input">
                    </div>
                    
                    <div>
                        <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="newPassword" name="new_password" minlength="6" class="input">
                    </div>
                    
                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" minlength="6" class="input">
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="flex-1 btn-primary">Update Profile</button>
                        <button type="button" onclick="closeModal('profileModal')" class="flex-1 btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function handleProfileUpdate(response) {
        showToast(response.message);
        closeModal('profileModal');
        
        // Update the displayed name if it changed
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
    
    // Password confirmation validation
    document.getElementById('confirmPassword').addEventListener('input', function() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = this.value;
        
        if (newPassword && confirmPassword && newPassword !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validate password fields
    document.getElementById('newPassword').addEventListener('input', function() {
        const currentPassword = document.getElementById('currentPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        
        if (this.value) {
            currentPassword.required = true;
            confirmPassword.required = true;
        } else {
            currentPassword.required = false;
            confirmPassword.required = false;
            confirmPassword.setCustomValidity('');
        }
    });
</script>