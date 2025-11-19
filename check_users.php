<?php
require_once 'init.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT id, email, password, role, first_name, last_name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Current users in database:\n";
    echo "========================\n";

    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Email: {$user['email']}\n";
        echo "Name: {$user['first_name']} {$user['last_name']}\n";
        echo "Role: {$user['role']}\n";
        echo "Password Hash: {$user['password']}\n";
        echo "---\n";
    }

    // Test password verification
    echo "\nTesting password verification:\n";
    echo "==============================\n";

    $testPasswords = [
        'admin@email.com' => 'admin@password.com',
        'resident@email.com' => 'resident@password.com'
    ];

    foreach ($testPasswords as $email => $password) {
        $stmt = $db->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $isValid = password_verify($password, $user['password']);
            echo "Email: $email\n";
            echo "Password: $password\n";
            echo "Valid: " . ($isValid ? 'YES' : 'NO') . "\n";
            echo "---\n";
        } else {
            echo "User not found: $email\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
