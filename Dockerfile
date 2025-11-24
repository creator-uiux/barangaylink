FROM composer:latest AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist --ignore-platform-reqs

FROM php:8.1-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip git curl && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer /app/vendor ./vendor

COPY --chown=www-data:www-data . .

RUN chmod +x start.sh && \
    chown -R www-data:www-data /var/www/html && \
    mkdir -p storage bootstrap/cache && \
    chmod -R 755 storage bootstrap/cache

EXPOSE 80

CMD ["./start.sh"]
