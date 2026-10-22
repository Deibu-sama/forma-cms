#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
  echo "ERROR: APP_KEY is required. Generate one with 'php artisan key:generate --show' and set it in your host environment." >&2
  exit 1
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
  DB_FILE="${DB_DATABASE:-database/database.sqlite}"
  case "$DB_FILE" in
    /*) ;;
    *) DB_FILE="/var/www/html/$DB_FILE" ;;
  esac
  mkdir -p "$(dirname "$DB_FILE")"
  touch "$DB_FILE"
fi

php artisan config:clear
php artisan migrate --force

# Provision/synchronize the admin from private host environment variables.
# The command validates all three values and enforces a 12-character minimum.
if [ -n "${ADMIN_EMAIL:-}" ] || [ -n "${ADMIN_NAME:-}" ] || [ -n "${ADMIN_PASSWORD:-}" ]; then
  php artisan bookkeeper:make-admin
fi

PORT="${PORT:-8080}"
exec php artisan serve --host 0.0.0.0 --port "$PORT"
