FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json ./
RUN composer install --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

FROM php:8.5-cli-alpine
RUN apk add --no-cache icu-dev libpq-dev $PHPIZE_DEPS \
    && docker-php-ext-install intl pdo_pgsql pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS
WORKDIR /app
COPY --from=vendor /usr/bin/composer /usr/local/bin/composer
COPY --from=vendor /app/vendor ./vendor
COPY . .
RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
USER www-data
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
