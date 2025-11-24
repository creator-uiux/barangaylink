#!/bin/bash

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY not set, generating..."
    php artisan key:generate --no-interaction
fi

# Run database migrations
php artisan migrate --force

# Start Apache in foreground
apache2-foreground
