FROM php:7.3-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN set -ex \
  	&& apk --no-cache add \
	postgresql-dev \
	$PHPIZE_DEPS \
	libpq-dev \
	&& pecl install xdebug

RUN docker-php-ext-install pdo pdo_pgsql