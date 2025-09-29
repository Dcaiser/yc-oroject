<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Nilai Stok - Al-Ruhamaa' Inventory System</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #04904C;
            padding-bottom: 20px;
        }
        
        .logo {
            color: #04904C;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #036A3C;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .report-title {
            color: #04904C;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 10px;
            color: #666;
        }
        
        .summary-section {
            background-color: #E6F4EA;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #04904C;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        th {
            background-color: #1f7c4d;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }
        
        .status-low {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .status-warning {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .status-good {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">AL-RUHAMAA' INVENTORY SYSTEM</div>
        <div class="subtitle">Yatim Center Management System</div>
        <div class="report-title">LAPORAN NILAI STOK</div>
    </div>
    
    <!-- Report Info -->
    <div class="report-info">
        <div>
            <strong>Tanggal Cetak:</strong> {{ $generatedAt->format('d F Y, H:i') }} WIB
        </div>
        <div>
            <strong>Total Produk:</strong> {{ $products->count() }} item
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="margin-top: 0; color: #04904C; margin-bottom: 15px;">Ringkasan Nilai Stok</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($products->count()) }}</div>
                <div class="summary-label">Total Produk</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($products->sum('stock_quantity')) }}</div>
                <div class="summary-label">Total Stok</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
                <div class="summary-label">Total Nilai Inventori</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $products->where('stock_quantity', '<', 10)->count() }}</div>
                <div class="summary-label">Produk Stok Rendah</div>
            </div>
        </div>
    </div>
    
    <!-- Products Table -->
    <h3 style="color: #04904C; margin-bottom: 15px;">Detail Produk & Nilai Stok</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Produk</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 12%;">SKU</th>
                <th style="width: 10%;">Stok</th>
                <th style="width: 13%;">Harga Satuan</th>
                <th style="width: 15%;">Total Nilai</th>
                <th style="width: 5%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->description)
                            <br><small style="color: #666;">{{ Str::limit($product->description, 50) }}</small>
                        @endif
                    </td>
                    <td>{{ $product->category->name ?? 'Tidak ada kategori' }}</td>
                    <td class="text-center">{{ $product->sku }}</td>
                    <td class="text-center">{{ number_format($product->stock_quantity) }} {{ $product->satuan }}</td>
                    <td class="text-right">Rp {{ number_format($product->getDefaultPrice(), 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($product->stock_quantity * $product->getDefaultPrice(), 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($product->stock_quantity < 10)
                            <span class="status-badge status-low">Rendah</span>
                        @elseif($product->stock_quantity < 20)
                            <span class="status-badge status-warning">Perhatian</span>
                        @else
                            <span class="status-badge status-good">Aman</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #04904C; color: white;">
                <td colspan="6" class="text-right font-bold">TOTAL NILAI INVENTORI:</td>
                <td class="text-right font-bold">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Low Stock Alert Section -->
    @php
        $lowStockProducts = $products->where('stock_quantity', '<', 10);
    @endphp
    
    @if($lowStockProducts->count() > 0)
        <div class="page-break">
            <h3 style="color: #dc2626; margin-bottom: 15px;">⚠️ PERINGATAN STOK RENDAH</h3>
            <div style="background-color: #fef2f2; padding: 15px; border-left: 4px solid #dc2626; margin-bottom: 20px;">
                <p style="margin: 0; color: #7f1d1d;">
                    <strong>Perhatian:</strong> Terdapat {{ $lowStockProducts->count() }} produk dengan stok di bawah 10 unit. 
                    Segera lakukan restok untuk menghindari kehabisan stok.
                </p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Nama Produk</th>
                        <th style="width: 20%;">Kategori</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 15%;">Stok Tersisa</th>
                        <th style="width: 10%;">Urgency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts->sortBy('stock_quantity') as $index => $product)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'Tidak ada kategori' }}</td>
                            <td class="text-center">{{ $product->sku }}</td>
                            <td class="text-center">
                                <span style="color: #dc2626; font-weight: bold;">
                                    {{ $product->stock_quantity }} {{ $product->satuan }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($product->stock_quantity <= 3)
                                    <span class="status-badge" style="background-color: #dc2626; color: white;">Kritis</span>
                                @elseif($product->stock_quantity <= 7)
                                    <span class="status-badge" style="background-color: #f59e0b; color: white;">Tinggi</span>
                                @else
                                    <span class="status-badge status-warning">Sedang</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>Al-Ruhamaa' Inventory System</strong><br>
            Yatim Center Management System<br>
            Laporan ini dibuat secara otomatis pada {{ $generatedAt->format('d F Y, H:i') }} WIB
        </p>
        <p style="margin-top: 15px; font-style: italic;">
            "Dan barangsiapa yang menghidupkan satu jiwa, maka seakan-akan dia telah menghidupkan seluruh manusia."
        </p>
    </div>
</body>
</html>