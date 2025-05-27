# Use the official PHP 8.4 FPM Alpine image
FROM php:8.4-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        linux-headers \
        postgresql-dev \
        mysql-client \
    && docker-php-ext-install pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps \
    && apk add --no-cache \
        libpq

# Copy your PHP config if needed (optional)
# COPY ./php.ini /usr/local/etc/php/

# Set working directory
WORKDIR /var/www/html

# Expose port (optional if you use it)
EXPOSE 9000

# Start PHP-FPM (already default for FPM images)
#CMD ["php-fpm"]