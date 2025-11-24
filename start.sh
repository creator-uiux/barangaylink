# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! php check_db.php; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database is ready!"

# Run Laravel migrations (skip if database not available)
echo "Running database migrations..."
if php artisan migrate --force; then
    echo "Migrations completed successfully."
else
    echo "Migrations failed. Continuing without migrations..."
fi

# Clear and cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
apache2-foreground
=======
#!/bin/bash

# Set default port if not provided by Render.com
export PORT=${PORT:-80}

# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! php check_db.php; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database is ready!"

# Run Laravel migrations (skip if database not available)
echo "Running database migrations..."
if php artisan migrate --force; then
    echo "Migrations completed successfully."
else
    echo "Migrations failed. Continuing without migrations..."
fi

# Clear and cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache on port $PORT..."
apache2-foreground
