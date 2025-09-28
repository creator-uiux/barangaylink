<?php
function renderContactForm() {
    $success_message = '';
    $error_message = '';
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));
        $message = htmlspecialchars(trim($_POST['message'] ?? ''));
        
        if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Here you would typically save to database or send email
            // For now, we'll just show a success message
            $success_message = 'Thank you for your message! We will get back to you soon.';
            
            // Clear form data
            $_POST = array();
        } else {
            $error_message = 'Please fill in all fields with valid information.';
        }
    }
?>

<div class="p-6 bg-white rounded-lg shadow-md border">
    <?php if ($success_message): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-md">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="space-y-4" id="contact-form">
        <input type="hidden" name="contact_form" value="1">
        
        <div class="space-y-2">
            <label for="contact-name" class="block font-medium text-slate-900">Name</label>
            <input
                id="contact-name"
                name="name"
                type="text"
                placeholder="Your full name"
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                required
                value="<?php echo isset($_POST['name']) && !$success_message ? htmlspecialchars($_POST['name']) : ''; ?>"
            />
        </div>
        
        <div class="space-y-2">
            <label for="contact-email" class="block font-medium text-slate-900">Email</label>
            <input
                id="contact-email"
                name="email"
                type="email"
                placeholder="Your email address"
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                required
                value="<?php echo isset($_POST['email']) && !$success_message ? htmlspecialchars($_POST['email']) : ''; ?>"
            />
        </div>
        
        <div class="space-y-2">
            <label for="contact-message" class="block font-medium text-slate-900">Message</label>
            <textarea
                id="contact-message"
                name="message"
                placeholder="Your message..."
                rows="5"
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background resize-none"
                required
            ><?php echo isset($_POST['message']) && !$success_message ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors disabled:opacity-50"
            id="submit-btn"
        >
            Send Message
        </button>
    </form>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';
    
    // Re-enable button after form submission (in case of errors)
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Message';
    }, 3000);
});
</script>

<?php
}
?>