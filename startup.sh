#!/bin/bash

# Startup script for BarangayLink PHP on Render.com
echo "🚀 Starting BarangayLink PHP Application..."

# Create data directory if it doesn't exist
if [ ! -d "data" ]; then
    echo "📁 Creating data directory..."
    mkdir -p data
    chmod 755 data
fi

# Initialize JSON files if they don't exist
echo "📋 Initializing JSON data files..."

files=("users.json" "requests.json" "concerns.json" "announcements.json" "notifications.json" "profiles.json" "file_uploads.json")

for file in "${files[@]}"; do
    if [ ! -f "data/$file" ]; then
        echo "Creating data/$file..."
        echo '[]' > "data/$file"
        chmod 666 "data/$file"
    fi
done

echo "✅ Data files initialized"

# Check PHP version
echo "🐘 PHP Version: $(php -v | head -n 1)"

# Check if main files exist
if [ -f "index.php" ]; then
    echo "✅ index.php found"
else
    echo "❌ index.php missing!"
fi

if [ -f "config.php" ]; then
    echo "✅ config.php found"
else
    echo "❌ config.php missing!"
fi

echo "🎯 Startup complete! Ready to serve..."

# Start PHP built-in server
php -S 0.0.0.0:${PORT:-8080} -t .