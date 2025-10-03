#!/bin/bash

echo "Installing PHP 8.4 compatible dependencies..."
echo

echo "[INFO] Checking PHP version..."
php -v | grep "PHP 8"
echo

echo "[1/5] Removing any existing problematic packages..."
composer remove maatwebsite/excel --no-interaction 2>/dev/null || true
composer remove phpoffice/phpspreadsheet --no-interaction 2>/dev/null || true

echo
echo "[2/5] Installing base composer dependencies..."
composer install --no-dev --optimize-autoloader

echo
echo "[3/5] Installing PHP 8.4 compatible PhpSpreadsheet..."
composer require phpoffice/phpspreadsheet:^2.0 --no-interaction

echo
echo "[4/5] Installing PHP 8.4 compatible Laravel Excel..."
composer require maatwebsite/excel:^3.1.50 --no-interaction

echo
echo "[5/5] Publishing configuration and clearing cache..."
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config --force
php artisan config:clear
php artisan config:cache
php artisan cache:clear

echo
echo "✓ Installation completed successfully!"
echo "✓ PhpSpreadsheet ^2.0 installed (PHP 8.4 compatible)"
echo "✓ Laravel Excel ^3.1.50 installed (PHP 8.4 compatible)"
echo
echo "[NEXT STEPS]"
echo "1. Restart your development server"
echo "2. Test Excel export functionality"
echo