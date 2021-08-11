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

# composer install
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./php.ini /usr/local/etc/php/conf.d/php.ini

# cron
RUN echo '* * * * * cd /var/www/html && php artisan schedule:run >&1 2>&1' > /etc/crontabs/root

WORKDIR /var/www/html

EXPOSE 80

CMD ["php", "artisan", "swoole:http", "start"]
