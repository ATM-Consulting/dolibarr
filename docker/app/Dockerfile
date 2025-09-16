FROM php:8.2-fpm-bullseye

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl libzip-dev libpng-dev libfreetype6-dev libssl-dev git \
    libicu-dev libc-client-dev libkrb5-dev libxml2-dev libjpeg62-turbo-dev \
    libonig-dev wget unzip rsync \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
 && docker-php-ext-install calendar intl imap mbstring mysqli pdo pdo_mysql zip xml gd \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
COPY docker/app/php-local.ini /usr/local/etc/php/conf.d/99-local.ini
