FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    libpng-dev \
    libzip-dev

RUN docker-php-ext-install pdo pdo_mysql zip gd

WORKDIR /var/www/html

COPY . .

COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN sed -i 's|^listen = .*|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf

RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"
