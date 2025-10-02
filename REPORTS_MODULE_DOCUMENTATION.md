# 📊 Modul Laporan - Al-Ruhamaa' Inventory System

## Overview
Modul Laporan adalah bagian sistem manajemen inventori Al-Ruhamaa' yang menyediakan berbagai jenis laporan untuk membantu dalam pengambilan keputusan dan monitoring aktivitas sistem.

## 🎯 Fitur Utama

### 1. Dashboard Laporan
- **URL**: `/reports`
- **Akses**: Manager dan Admin
- **Fitur**: 
  - Quick statistics
  - Navigasi ke semua jenis laporan
  - Recent activities

### 2. Laporan Nilai Stok
- **URL**: `/reports/stock-value`
- **Fitur**:
  - Total nilai inventori
  - Filter berdasarkan kategori
  - Status stok (rendah/normal/tinggi)
  - Export PDF & Excel

### 3. Laporan Pergerakan
- **URL**: `/reports/movement`
- **Fitur**:
  - Timeline aktivitas sistem
  - Filter berdasarkan tanggal
  - Detail user dan aksi
  - Export PDF & Excel

### 4. Laporan Performa Supplier
- **URL**: `/reports/supplier-performance`
- **Fitur**:
  - Performance metrics supplier
  - Contact information
  - Jumlah produk per supplier
  - Export PDF & Excel

### 5. Laporan Mingguan
- **URL**: `/reports/weekly`
- **Fitur**:
  - Aktivitas per hari dalam seminggu
  - Summary mingguan
  - Export PDF & Excel

### 6. Laporan Bulanan
- **URL**: `/reports/monthly`
- **Fitur**:
  - Executive summary
  - Distribusi aktivitas mingguan
  - Produk baru bulan ini
  - Analisis jenis aktivitas
  - User paling aktif
  - KPI dan metrik performa
  - Export PDF & Excel

## 🛠️ Struktur Kode

### Controller
```php
App\Http\Controllers\ReportController
```
- `index()` - Dashboard laporan
- `stockValue()` - Laporan nilai stok
- `movement()` - Laporan pergerakan
- `supplierPerformance()` - Laporan performa supplier
- `weekly()` - Laporan mingguan
- `monthly()` - Laporan bulanan
- `exportPdf()` - Export ke PDF
- `exportExcel()` - Export ke Excel

### Views
```
resources/views/reports/
├── index.blade.php              # Dashboard
├── stock-value.blade.php        # Laporan nilai stok
├── movement.blade.php           # Laporan pergerakan
├── supplier-performance.blade.php  # Laporan supplier
├── weekly.blade.php             # Laporan mingguan
├── monthly.blade.php            # Laporan bulanan
└── pdf/
    ├── stock-value.blade.php    # PDF template nilai stok
    ├── movement.blade.php       # PDF template pergerakan
    ├── supplier-performance.blade.php  # PDF template supplier
    ├── weekly.blade.php         # PDF template mingguan
    └── monthly.blade.php        # PDF template bulanan
```

### Export Class
```php
App\Exports\ReportExport
```
Handles Excel export untuk semua jenis laporan dengan formatting dan styling.

### Models yang Digunakan
- `App\Models\Produk` - Data produk dan stok
- `App\Models\Activity` - Log aktivitas sistem
- `App\Models\Supplier` - Data supplier
- `App\Models\Kategori` - Kategori produk
- `App\Models\Price` - Multi-price system

## 🔧 Instalasi dan Setup

### 1. Install Dependencies
```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/laravel-excel
```

### 2. Publish Configurations
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

### 3. Generate Test Data
```bash
php artisan db:seed --class=ReportTestDataSeeder
```

### 4. Test All Reports
```bash
php artisan test:reports
```

## 🎨 Design System

### Color Palette
- **Primary**: `#6366f1` (Indigo)
- **Secondary**: `#4f46e5` (Purple)
- **Success**: `#16a34a` (Green)
- **Warning**: `#f59e0b` (Amber)
- **Danger**: `#dc2626` (Red)
- **Gray**: `#6b7280` (Cool Gray)

### Typography
- **Font Family**: Inter, system-ui, sans-serif
- **Headings**: Bold, spacing optimized
- **Body**: Regular weight, high readability

### Layout
- **Grid System**: CSS Grid dan Flexbox
- **Responsive**: Mobile-first approach
- **Spacing**: Consistent padding dan margin
- **Cards**: Shadow dan border radius modern

## 📈 Export Features

### PDF Export
- **Library**: `barryvdh/laravel-dompdf`
- **Features**:
  - Professional formatting
  - Company branding
  - Charts dan graphics
  - Print-optimized layout
  - Multi-page support

### Excel Export
- **Library**: `maatwebsite/laravel-excel`
- **Features**:
  - Formatted spreadsheets
  - Multiple sheets
  - Styling dan colors
  - Column auto-sizing
  - Data validation

## 🔐 Security & Permissions

### Middleware
- `ManagerMiddleware` - Hanya Manager dan Admin yang bisa akses
- `auth` - User harus login

### Routes Protection
```php
Route::middleware(['auth', ManagerMiddleware::class])->group(function () {
    // All report routes
});
```

## 📊 Data Sources

### Multi-Price System
Sistem menggunakan tabel `prices` terpisah dengan customer types:
- `pelanggan` - Harga normal
- `reseller` - Diskon 15%
- `agent` - Diskon 25%

### Activity Logging
Semua aktivitas sistem dicatat di tabel `activities`:
- User yang melakukan
- Aksi yang dilakukan
- Model/table yang terkait
- Timestamp

### Stock Management
Stok tracking dengan:
- Quantity tracking
- Low stock alerts (< 10)
- Supplier relationship
- Category classification

## 🚀 Performance Tips

### 1. Database Optimization
- Index pada kolom yang sering di-query
- Eager loading untuk relationships
- Chunking untuk large datasets

### 2. Caching
```php
// Cache report data untuk performa
Cache::remember('monthly-report-' . $month, 3600, function() {
    return $this->generateMonthlyData();
});
```

### 3. Export Optimization
- Limit jumlah records untuk export besar
- Background processing untuk export berat
- Pagination untuk view

## 🧪 Testing

### Automated Testing
```bash
# Test semua laporan
php artisan test:reports

# Test laporan spesifik
php artisan test:reports --type=monthly
```

### Manual Testing Checklist
- [ ] Dashboard load dengan benar
- [ ] Semua filter berfungsi
- [ ] Export PDF berhasil
- [ ] Export Excel berhasil
- [ ] Responsive di mobile
- [ ] Permission enforcement
- [ ] Data accuracy

## 📝 Changelog

### v1.0.0 (2025-09-23)
- ✅ Initial release
- ✅ 5 jenis laporan utama
- ✅ PDF & Excel export
- ✅ Responsive design
- ✅ Multi-price system integration
- ✅ Activity logging
- ✅ Permission system
- ✅ Test automation

## 🤝 Contributing

### Code Standards
- Follow PSR-12 coding standards
- Use meaningful variable names
- Add comments untuk complex logic
- Test sebelum commit

### Commit Convention
```
feat: add new report type
fix: resolve PDF export issue
docs: update documentation
style: improve UI/UX
refactor: optimize query performance
test: add unit tests
```

## 📞 Support

Untuk dukungan teknis atau pertanyaan:
- **Email**: tech@yatimcenter.org
- **Documentation**: /docs/reports
- **Issue Tracker**: Internal tracking system

---

> **"Dan barangsiapa yang menghidupkan satu jiwa, maka seakan-akan dia telah menghidupkan seluruh manusia."**

*Al-Ruhamaa' Inventory System - Yatim Center Management*