# Build Composer dependencies and frontend assets, then run Laravel on PHP.
# Firestore's PHP SDK requires the native gRPC extension.
FROM php:8.3-cli-bookworm AS php-base
RUN apt-get update \
    && apt-get install -y --no-install-recommends $PHPIZE_DEPS libsqlite3-dev libzip-dev libcurl4-openssl-dev unzip \
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    && docker-php-ext-install pdo_sqlite pdo_mysql zip curl \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

FROM php-base AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm install --no-audit --no-fund && npm run build

FROM php-base AS runtime
WORKDIR /var/www/html
ENV APP_ENV=production
ENV APP_DEBUG=false

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["./docker/entrypoint.sh"]
