FROM php:7.4-alpine

RUN apk add --no-cache \
    autoconf \
    g++ \
    gcc \
    libc-dev \
    make \
    postgresql-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    icu-dev \
    libzip-dev

RUN docker-php-ext-install bcmath \
    && docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip \
    && pecl install swoole && rm -rf /tmp/pear && docker-php-ext-enable swoole \
    && pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
