# 🔧 Perbaikan Error PhpSpreadsheet

## ❌ Error yang Muncul:
```
Class "PhpOffice\PhpSpreadsheet\Reader\Csv" not found
```

## ⚠️ PHP Version Compatibility Issue:
```
maatwebsite/excel 3.1 requires php ^7.0 -> your php version (8.4.2) does not satisfy that requirement
```

## ✅ Solusi untuk PHP 8.4:

### **Windows (PHP 8.4):**
1. Buka Command Prompt atau PowerShell di folder project
2. Jalankan script: `install-dependencies-php8.bat`
3. Tunggu hingga selesai

### **Linux/Mac (PHP 8.4):**
1. Buka Terminal di folder project  
2. Jalankan script: `./install-dependencies-php8.sh`
3. Tunggu hingga selesai

### **Alternative (Older PHP versions):**
- Windows: `install-dependencies.bat`
- Linux/Mac: `./install-dependencies.sh`

### **Manual Installation (PHP 8.4):**
Jika script gagal, jalankan perintah ini satu per satu:

```bash
# Remove existing packages (if any)
composer remove maatwebsite/excel --no-interaction
composer remove phpoffice/phpspreadsheet --no-interaction

# Install dependencies
composer install

# Install PHP 8.4 compatible packages
composer require phpoffice/phpspreadsheet:^2.0 --no-interaction
composer require maatwebsite/excel:^3.1.50 --no-interaction

# Publish config
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config --force

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## 🚀 Setelah Instalasi:
- Restart development server: `php artisan serve`
- Error PhpSpreadsheet akan hilang
- Fitur export Excel dapat digunakan

## 📝 Dependencies yang Ditambahkan:
- `maatwebsite/excel:^3.1.50` - Laravel Excel package (PHP 8.4 compatible)
- `phpoffice/phpspreadsheet:^2.0` - Core PhpSpreadsheet library (latest version)

---
*Update: 03 Oktober 2025*