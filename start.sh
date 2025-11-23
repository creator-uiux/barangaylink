#!/bin/bash

# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! mysqladmin ping -h"$MYSQLHOST" -P"$MYSQLPORT" -u"$MYSQLUSER" -p"$MYSQLPASSWORD" --silent; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database is ready!"

# Initialize database if needed
echo "Initializing database..."
php init_db.php

# Start Apache
echo "Starting Apache..."
apache2-foreground
