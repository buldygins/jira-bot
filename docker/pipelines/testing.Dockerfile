FROM oshuntica/dev:php-7.4-alpine

WORKDIR /var/www/html
COPY . .

# install composer packages
ARG NOVA_USERNAME
ARG NOVA_PASSWORD
RUN composer config http-basic.nova.laravel.com "${NOVA_USERNAME}" "${NOVA_PASSWORD}" \
    && composer install -o

