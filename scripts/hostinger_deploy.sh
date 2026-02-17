#!/usr/bin/env bash
set -e

echo "==> Hostinger Laravel deploy sequence"

if [[ -f public/hot ]]; then
  echo "Removing public/hot"
  rm -f public/hot
fi

if compgen -G "bootstrap/cache/*.php" > /dev/null; then
  echo "Clearing stale bootstrap cache files"
  rm -f bootstrap/cache/*.php
fi

echo "Installing composer dependencies"
composer install --no-dev --optimize-autoloader

echo "Running migrations"
php artisan migrate --force

echo "Linking storage"
php artisan storage:link || true

echo "Building frontend assets"
npm install
npm run build

echo "Clearing and rebuilding Laravel caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running production readiness check"
php artisan deploy:hostinger-ready

echo "✅ Deploy sequence completed"
