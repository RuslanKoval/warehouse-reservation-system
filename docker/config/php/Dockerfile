FROM php:8.4-fpm
#ARG uid=1000
ARG phpize_deps="libfreetype6-dev libjpeg62-turbo-dev libpng-dev libxml2-dev libzip-dev zlib1g-dev zip wget nano unzip curl mc jpegoptim optipng pngquant gifsicle libpq-dev libmagickwand-dev"

ENV COMPOSER_URL="https://getcomposer.org/installer" \
    PATH="/opt/cprocsp/bin/amd64:${PATH}"

ARG uid=1000

RUN sed -i 's/kinetic/jammy/g' /etc/apt/sources.list.d/debian.sources && apt-get update && \
    apt-get install -y --no-install-recommends $phpize_deps && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pcntl bcmath opcache zip pdo pdo_pgsql pgsql && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS ${COMPOSER_URL} | php -- --install-dir=/usr/local/bin --filename=composer


RUN groupadd -g ${uid} www
RUN useradd -u ${uid} -ms /bin/bash -g www www

USER www-data
WORKDIR /application