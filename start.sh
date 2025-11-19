#!/bin/bash

# Wait for MySQL to be ready using PHP
echo "Waiting for MySQL to be ready..."
while ! php -r "
try {
    \$pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=3306;charset=utf8mb4', getenv('DB_USER'), getenv('DB_PASS'));
    echo 'MySQL is ready' . PHP_EOL;
    exit(0);
} catch (Exception \$e) {
    echo 'MySQL is unavailable - sleeping' . PHP_EOL;
    exit(1);
}
"; do
    sleep 2
done

echo "MySQL is up - executing command"

# Initialize database
php init_db.php

# Start PHP built-in server
php -S 0.0.0.0:8080 -t .
