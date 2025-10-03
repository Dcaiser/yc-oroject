# 🔧 Perbaikan Error PhpSpreadsheet

## ❌ Error yang Muncul:
```
Class "PhpOffice\PhpSpreadsheet\Reader\Csv" not found
```

## ✅ Solusi:

### **Windows:**
1. Buka Command Prompt atau PowerShell di folder project
2. Jalankan script: `install-dependencies.bat`
3. Tunggu hingga selesai

### **Linux/Mac:**
1. Buka Terminal di folder project  
2. Jalankan script: `./install-dependencies.sh`
3. Tunggu hingga selesai

### **Manual Installation:**
Jika script gagal, jalankan perintah ini satu per satu:

```bash
# Install dependencies
composer install

# Install PhpSpreadsheet & Laravel Excel
composer require maatwebsite/excel:^3.1
composer require phpoffice/phpspreadsheet:^1.29

# Publish config
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## 🚀 Setelah Instalasi:
- Restart development server: `php artisan serve`
- Error PhpSpreadsheet akan hilang
- Fitur export Excel dapat digunakan

## 📝 Dependencies yang Ditambahkan:
- `maatwebsite/excel:^3.1` - Laravel Excel package
- `phpoffice/phpspreadsheet:^1.29` - Core PhpSpreadsheet library

---
*Update: 03 Oktober 2025*