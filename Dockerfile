# Multi-stage image: build PHP dependencies and Tailwind assets, then run Laravel on PHP.
FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm install --no-audit --no-fund && npm run build

FROM php:8.3-cli-bookworm AS runtime
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev libzip-dev libcurl4-openssl-dev unzip \
    && docker-php-ext-install pdo_sqlite pdo_mysql zip curl \
    && rm -rf /var/lib/apt/lists/*

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
