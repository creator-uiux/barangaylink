#!/bin/bash

# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! php check_db.php; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database is ready!"

# Run Laravel migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear and cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
apache2-foreground
