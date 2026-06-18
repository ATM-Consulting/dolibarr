FROM php:8.2-fpm

ARG PUID=1000
ARG PGID=1000

RUN usermod -u ${PUID} www-data && groupmod -g ${PGID} www-data

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    openssh-client \
    libssl-dev \
    git \
    xvfb \
    libxml2-dev \
    wget \
    vim \
    rsync \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

USER root

RUN mkdir -p /var/www/documents
RUN chown -R www-data:www-data ./documents
COPY --chown=www-data:www-data ./htdocs ./htdocs
COPY --chown=www-data:www-data ./doc ./doc
COPY --chown=www-data:www-data ./dev ./dev
COPY --chown=www-data:www-data ./scripts ./scripts
COPY --chown=www-data:www-data ./test ./test
COPY --chown=www-data:www-data ./build ./build

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer config --global repo.packagist composer https://packagist.org
COPY --chown=www-data:www-data composer.json ./

# Share the same config between root and www-data
RUN cp -r /root/.composer /var/www && chown -R www-data:www-data /var/www/.composer
RUN git config --global --add safe.directory /var/www

USER www-data
