@echo off
echo Installing missing PHP dependencies...
echo.

echo [1/4] Installing Composer dependencies...
composer install --no-dev --optimize-autoloader

echo.
echo [2/4] Installing PhpSpreadsheet and Laravel Excel...
composer require maatwebsite/excel:^3.1.50
composer require phpoffice/phpspreadsheet:^2.0

echo.
echo [3/4] Publishing Excel configuration...
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

echo.
echo [4/4] Clearing and caching configuration...
php artisan config:clear
php artisan config:cache
php artisan cache:clear

echo.
echo ✓ All dependencies installed successfully!
echo ✓ PhpSpreadsheet is now available
echo.
pause