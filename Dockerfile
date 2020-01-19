# multi stage build starts with composer
FROM composer:latest as composer

FROM php:7.3-cli

# then the composer binary is copied from the composer img to our app img
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update \
&& apt-get install -y unzip \
&& pecl install xdebug \
&& docker-php-ext-enable xdebug

WORKDIR /app
