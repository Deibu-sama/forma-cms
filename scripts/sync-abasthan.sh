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

mkdir -p "$APP_ROOT/public/build"
cp -a "$SOURCE_ROOT/public/build/." "$APP_ROOT/public/build/"
cp "$SOURCE_ROOT/public/favicon.svg" "$APP_ROOT/public/favicon.svg"

echo "Clearing Laravel caches..."
php artisan optimize:clear

echo "Applying pending database migrations..."
php artisan migrate --force

echo
echo "Deployment files and migrations are updated."
echo "If no administrator exists yet, create one with:"
echo "  php artisan bookkeeper:make-admin your-admin-email@example.com"
echo "Then sign in at /admin/login and open /admin/settings."
echo
echo "This script deliberately did not overwrite .env, vendor, storage, or your existing database."
