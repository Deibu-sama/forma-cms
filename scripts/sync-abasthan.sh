#!/usr/bin/env bash
set -euo pipefail

# Run this script from the deployed Laravel application root.
# It overlays application code from the public GitHub main branch while
# deliberately preserving .env, vendor, storage, and the existing database.

APP_ROOT="$(pwd)"
REPO_TARBALL="https://api.github.com/repos/Deibu-sama/forma-cms/tarball/main"

if [[ ! -f "$APP_ROOT/artisan" || ! -d "$APP_ROOT/app" || ! -d "$APP_ROOT/public" ]]; then
  echo "Error: run this script from the Laravel application root (the folder containing artisan)." >&2
  exit 1
fi

TMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TMP_DIR"' EXIT

echo "Downloading latest Bookkeeper Pro Portfolio source..."
curl -fLsS "$REPO_TARBALL" -o "$TMP_DIR/source.tar.gz"
tar -xzf "$TMP_DIR/source.tar.gz" -C "$TMP_DIR"
SOURCE_ROOT="$(find "$TMP_DIR" -mindepth 1 -maxdepth 1 -type d -print -quit)"

if [[ -z "$SOURCE_ROOT" || ! -f "$SOURCE_ROOT/resources/views/app.blade.php" || ! -f "$SOURCE_ROOT/public/build/manifest.json" ]]; then
  echo "Error: the downloaded source archive is incomplete." >&2
  exit 1
fi

for directory in app bootstrap config database/migrations resources routes; do
  mkdir -p "$APP_ROOT/$directory"
  cp -a "$SOURCE_ROOT/$directory/." "$APP_ROOT/$directory/"
done

# The REST Firestore adapter uses google/auth and Guzzle rather than the
# gRPC-bound google/cloud-firestore client. Replace the server's stale lockfile
# too; it may still pin google/cloud-firestore and require the missing ext-grpc.
cp "$SOURCE_ROOT/composer.json" "$APP_ROOT/composer.json"

if [[ -f "$SOURCE_ROOT/composer.lock" ]]; then
  cp "$SOURCE_ROOT/composer.lock" "$APP_ROOT/composer.lock"
elif command -v composer >/dev/null 2>&1; then
  echo "No compatible Composer lockfile exists in the repository yet; resolving a fresh lockfile."
  rm -f "$APP_ROOT/composer.lock"
else
  echo "Error: the repository does not contain composer.lock and Composer is unavailable." >&2
  echo "Use the PHP build environment to generate and deploy the updated dependency lockfile first." >&2
  exit 1
fi

if command -v composer >/dev/null 2>&1; then
  echo "Installing the current PHP dependencies..."
  if [[ -f "$SOURCE_ROOT/composer.lock" ]]; then
    composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
  else
    composer update --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
  fi
elif php -r 'require "vendor/autoload.php"; exit(class_exists("Google\\Auth\\Credentials\\ServiceAccountCredentials") && class_exists("GuzzleHttp\\Client") ? 0 : 1);'; then
  echo "Composer is unavailable, but the existing vendor directory already includes the Firestore REST dependencies."
else
  echo "Error: Composer is unavailable and the existing vendor directory lacks google/auth or Guzzle." >&2
  echo "Install Composer dependencies in the PHP build environment, then rerun this script." >&2
  exit 1
fi

mkdir -p "$APP_ROOT/public/build"
cp -a "$SOURCE_ROOT/public/build/." "$APP_ROOT/public/build/"
cp "$SOURCE_ROOT/public/favicon.svg" "$APP_ROOT/public/favicon.svg"

echo "Clearing Laravel caches..."
php artisan optimize:clear

echo "Applying pending database migrations..."
php artisan migrate --force

echo
echo "Deployment files and migrations are updated."
echo "If no administrator exists yet and ADMIN_EMAIL, ADMIN_NAME, and ADMIN_PASSWORD are set:"
echo "  php artisan config:clear"
echo "  php artisan bookkeeper:make-admin"
echo "Otherwise use interactive prompts:"
echo "  php artisan bookkeeper:make-admin your-admin-email@example.com"
echo "Then sign in at /admin/login and open /admin/settings."
echo "For safety, remove ADMIN_PASSWORD from the host environment after account creation, then run php artisan config:clear."
echo
echo "This script preserves .env, storage, and your existing database. It refreshes Composer dependencies when Composer is available; otherwise, it verifies the needed REST dependencies are already installed."
