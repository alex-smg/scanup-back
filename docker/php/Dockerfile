FROM php:7.3-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk update && pecl install xdebug

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql intl opcache
