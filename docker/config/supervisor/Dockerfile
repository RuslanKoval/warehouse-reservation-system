FROM php:8.2-cli
ARG uid=1000

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libmagickwand-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get install -y --no-install-recommends \
    zip \
    wget \
    nano \
    unzip \
    curl \
    mc \
    jpegoptim optipng pngquant gifsicle

RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install imagick && docker-php-ext-enable imagick

RUN docker-php-ext-install pcntl bcmath opcache

RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt install -y libxml2-dev libzip-dev zlib1g-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-install xml
RUN docker-php-ext-install iconv
RUN docker-php-ext-install simplexml

RUN groupadd -g ${uid} www
RUN useradd -u ${uid} -ms /bin/bash -g www www

RUN apt-get install -y supervisor

# Changing Workdir
WORKDIR /application
CMD ["/usr/bin/supervisord"]

