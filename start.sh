#!/bin/bash

# Set default port if not set
PORT=${PORT:-80}

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY not set, generating..."
    php artisan key:generate --no-interaction
fi

# Initialize database if needed
if [ ! -f /var/www/html/.db_initialized ]; then
    echo "Initializing database..."
    php init_db.php
    if [ $? -eq 0 ]; then
        touch /var/www/html/.db_initialized
        echo "Database initialized successfully"
    else
        echo "Database initialization failed"
        exit 1
    fi
else
    echo "Database already initialized"
fi

# Configure Apache to listen on the specified port
sed -i "s/80/$PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf

# Set ServerName to suppress AH00558 warning
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Start Apache in foreground
apache2-foreground
