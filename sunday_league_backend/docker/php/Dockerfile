FROM php:7.4.3-fpm-alpine3.11


RUN docker-php-ext-install pdo pdo_mysql

COPY . /app/

VOLUME ["/app"]
