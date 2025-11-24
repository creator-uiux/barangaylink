FROM php:8.1-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip nodejs npm default-mysql-client && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
COPY --chown=www-data:www-data . /var/www/html

RUN chmod +x start.sh && composer install --no-dev --optimize-autoloader && chown -R www-data:www-data /var/www/html && mkdir -p storage bootstrap/cache && chmod -R 755 storage bootstrap/cache

RUN echo "Listen \${PORT:-80}" > /etc/apache2/ports.conf && sed -i 's/80/\${PORT:-80}/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["./start.sh"]
