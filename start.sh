#!/bin/bash

# Set default port if not set
PORT=${PORT:-80}

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY not set, generating..."
    php artisan key:generate --no-interaction
fi

# Run database migrations
php artisan migrate --force

# Configure Apache to listen on the specified port
sed -i "s/80/$PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf

# Start Apache in foreground
apache2-foreground
