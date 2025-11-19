#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"$DB_HOST" -P3306 -u"$DB_USER" -p"$DB_PASS" --silent; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "MySQL is up - executing command"

# Initialize database
php init_db.php

# Start PHP built-in server
php -S 0.0.0.0:8080 -t .
