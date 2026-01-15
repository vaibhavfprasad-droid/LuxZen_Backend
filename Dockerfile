FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    libpng-dev \
    libzip-dev

RUN docker-php-ext-install pdo pdo_mysql zip gd

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
