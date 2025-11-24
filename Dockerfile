# Use the official PHP 8.1 image with Apache
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

# Make start script executable
RUN chmod +x /var/www/html/start.sh

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/storage \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache to listen on the port provided by Render.com
RUN echo "Listen \${PORT:-80}" > /etc/apache2/ports.conf
RUN sed -i 's/80/\${PORT:-80}/g' /etc/apache2/sites-available/000-default.conf

# Expose port (Render.com will set PORT environment variable)
EXPOSE 80

# Health check for Render.com
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:${PORT:-80}/ || exit 1

# Start the application
CMD ["./start.sh"]
