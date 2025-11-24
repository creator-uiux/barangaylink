<?php

// Check if DATABASE_URL is set (common on platforms like Render.com)
$databaseUrl = getenv('DATABASE_URL');
if ($databaseUrl) {
    $parsedUrl = parse_url($databaseUrl);
    $host = $parsedUrl['host'] ?? '127.0.0.1';
    $port = $parsedUrl['port'] ?? '3306';
    $database = ltrim($parsedUrl['path'], '/');
    $username = $parsedUrl['user'] ?? 'forge';
    $password = $parsedUrl['pass'] ?? '';
} else {
    // Fallback to individual environment variables
    $host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
    $database = getenv('DB_DATABASE') ?: getenv('MYSQLDATABASE') ?: 'laravel';
    $username = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'forge';
    $password = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '';
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "Database connection successful\n";
    exit(0);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
