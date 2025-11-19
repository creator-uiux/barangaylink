<?php
// Generate bcrypt hash for the passwords
$password1 = 'admin@password.com';
$password2 = 'resident@password.com';

echo "Password: $password1\n";
echo "Hash: " . password_hash($password1, PASSWORD_BCRYPT) . "\n\n";

echo "Password: $password2\n";
echo "Hash: " . password_hash($password2, PASSWORD_BCRYPT) . "\n\n";

// Test verification
$hash1 = password_hash($password1, PASSWORD_BCRYPT);
$hash2 = password_hash($password2, PASSWORD_BCRYPT);

echo "Verification test:\n";
echo "admin@password.com with hash1: " . (password_verify($password1, $hash1) ? 'YES' : 'NO') . "\n";
echo "resident@password.com with hash2: " . (password_verify($password2, $hash2) ? 'YES' : 'NO') . "\n";
?>
