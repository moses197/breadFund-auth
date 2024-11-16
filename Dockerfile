# Use PHP 8.2 FPM Alpine as base image
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql \
    bcmath \
    gd \
    xml \
    soap \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add user for Laravel application
RUN addgroup -g 1000 laravel && \
    adduser -G laravel -g laravel -s /bin/sh -D laravel

# Copy existing application directory contents
COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Copy existing application directory permissions
COPY --chown=laravel:laravel . .

# Change current user to laravel
USER laravel

# Install composer dependencies
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Generate application key
# RUN php artisan key:generate

# Cache config and routes
# RUN php artisan config:cache && \
#     php artisan route:cache && \
#     php artisan view:cache

# Set permissions for storage and bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port 9000
EXPOSE 9000

CMD ["start.sh"]

# Start PHP-FPM
# CMD ["php-fpm"]