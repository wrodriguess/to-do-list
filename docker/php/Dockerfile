FROM php:8.2.12-fpm-alpine3.17

RUN apk add --update --no-cache libintl icu icu-dev postgresql-dev pcre-dev $PHPIZE_DEPS \
    && docker-php-ext-install intl pdo pdo_pgsql \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && sed -i 's/variables_order = "GPCS"/variables_order = "EGPCS"/g' /usr/local/etc/php/php.ini-*

WORKDIR /code
COPY . .

EXPOSE 9000