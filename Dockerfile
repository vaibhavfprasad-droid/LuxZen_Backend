FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    libpng-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip gd

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Remove default nginx config (CRITICAL)
RUN rm -f /etc/nginx/conf.d/default.conf

# Ensure PHP-FPM listens on TCP 9000
RUN sed -i 's|^listen = .*|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf

# Fix Laravel permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Expose HTTP port
EXPOSE 80

# Start PHP-FPM and Nginx
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
