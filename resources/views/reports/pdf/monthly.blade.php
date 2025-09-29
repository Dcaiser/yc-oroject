<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan - Al-Ruhamaa' Inventory System</title>
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
            border-bottom: 2px solid #6366f1;
            padding-bottom: 20px;
        }
        
        .logo {
            color: #6366f1;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #4f46e5;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .report-title {
            color: #6366f1;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .report-period {
            background-color: #eef2ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            color: #3730a3;
        }
        
        .summary-section {
            background-color: #f8fafc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #6366f1;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        
        .weekly-chart {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f1f5f9;
            border-radius: 5px;
        }
        
        .weekly-item {
            text-align: center;
        }
        
        .weekly-label {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #374151;
        }
        
        .weekly-bar {
            background-color: #ddd6fe;
            border-radius: 3px;
            margin: 5px auto;
            width: 25px;
            min-height: 8px;
            display: flex;
            align-items: end;
            justify-content: center;
        }
        
        .weekly-count {
            font-size: 9px;
            font-weight: bold;
            color: #3730a3;
            margin-top: 5px;
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
        
        .activity-type-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        
        .activity-type-card {
            background-color: white;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .activity-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .icon-add { background-color: #16a34a; }
        .icon-update { background-color: #2563eb; }
        .icon-delete { background-color: #dc2626; }
        .icon-login { background-color: #7c3aed; }
        .icon-other { background-color: #6b7280; }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .page-break { page-break-before: always; }
        
        .kpi-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .kpi-item {
            text-align: center;
        }
        
        .kpi-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .kpi-label {
            font-size: 12px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">AL-RUHAMAA' INVENTORY SYSTEM</div>
        <div class="subtitle">Yatim Center Management System</div>
        <div class="report-title">LAPORAN BULANAN</div>
    </div>
    
    <!-- Report Period -->
    <div class="report-period">
        Periode: {{ $month->format('F Y') }}
        <br>
        <small style="font-weight: normal; font-size: 10px;">
            Dibuat pada: {{ $generatedAt->format('d F Y, H:i') }} WIB
        </small>
    </div>
    
    <!-- Executive Summary -->
    <div class="kpi-section">
        <h3 style="margin-top: 0; margin-bottom: 15px; text-align: center;">📊 Executive Summary</h3>
        <div class="kpi-grid">
            <div class="kpi-item">
                <div class="kpi-value">{{ number_format($activities->count()) }}</div>
                <div class="kpi-label">Total Aktivitas</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-value">{{ number_format($products->count()) }}</div>
                <div class="kpi-label">Produk Baru</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-value">Rp {{ number_format($products->sum(function($p) { return $p->stock_quantity * $p->getDefaultPrice(); }), 0, ',', '.') }}</div>
                <div class="kpi-label">Nilai Stok Baru</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-value">{{ \App\Models\Produk::where('stock_quantity', '<', 10)->count() }}</div>
                <div class="kpi-label">Stok Rendah</div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Summary -->
    <div class="summary-section">
        <h3 style="margin-top: 0; color: #6366f1; margin-bottom: 15px;">Ringkasan Bulanan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $activities->count() }}</div>
                <div class="summary-label">Total Aktivitas</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $products->count() }}</div>
                <div class="summary-label">Produk Ditambah</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rp {{ number_format($products->sum(function($product) { return $product->stock_quantity * $product->getDefaultPrice(); }), 0, ',', '.') }}</div>
                <div class="summary-label">Nilai Stok Baru</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ \App\Models\Produk::where('stock_quantity', '<', 10)->count() }}</div>
                <div class="summary-label">Alert Stok</div>
            </div>
        </div>
    </div>
    
    <!-- Weekly Breakdown -->
    @php
        $weeklyActivities = $activities->groupBy(function($activity) {
            return $activity->created_at->format('W');
        });
        $maxWeekly = $weeklyActivities->map->count()->max() ?: 1;
    @endphp
    
    <h3 style="color: #6366f1; margin-bottom: 15px;">📅 Distribusi Aktivitas Mingguan</h3>
    <div class="weekly-chart">
        @foreach($weeklyActivities->take(5) as $week => $weekActivities)
            @php
                $count = $weekActivities->count();
                $height = max(($count / $maxWeekly) * 60, 10);
            @endphp
            
            <div class="weekly-item">
                <div class="weekly-label">Minggu {{ $loop->iteration }}</div>
                <div class="weekly-bar" style="height: {{ $height }}px;">
                </div>
                <div class="weekly-count">{{ $count }}</div>
            </div>
        @endforeach
    </div>
    
    <!-- New Products This Month -->
    @if($products->count() > 0)
        <h3 style="color: #6366f1; margin-bottom: 15px;">📦 Produk Baru Bulan Ini</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Nama Produk</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 12%;">SKU</th>
                    <th style="width: 10%;">Stok</th>
                    <th style="width: 13%;">Harga</th>
                    <th style="width: 15%;">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products->take(15) as $index => $product)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->description)
                                <br><small style="color: #666;">{{ Str::limit($product->description, 40) }}</small>
                            @endif
                        </td>
                        <td>{{ $product->category->name ?? 'Tidak ada kategori' }}</td>
                        <td class="text-center">{{ $product->sku }}</td>
                        <td class="text-center">{{ number_format($product->stock_quantity) }} {{ $product->satuan }}</td>
                        <td class="text-right">Rp {{ number_format($product->getDefaultPrice(), 0, ',', '.') }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($product->stock_quantity * $product->getDefaultPrice(), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #6366f1; color: white;">
                    <td colspan="6" class="text-right font-bold">TOTAL NILAI PRODUK BARU:</td>
                    <td class="text-right font-bold">Rp {{ number_format($products->sum(function($product) { return $product->stock_quantity * $product->getDefaultPrice(); }), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        
        @if($products->count() > 15)
            <div style="background-color: #fef3c7; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <p style="margin: 0; color: #92400e; font-size: 10px;">
                    <strong>Catatan:</strong> Menampilkan 15 dari {{ $products->count() }} produk baru bulan ini.
                </p>
            </div>
        @endif
    @endif
    
    <!-- Activity Types Analysis -->
    @php
        $activityTypes = $activities->groupBy(function($activity) {
            if (str_contains(strtolower($activity->action), 'tambah')) return 'Penambahan';
            if (str_contains(strtolower($activity->action), 'update')) return 'Perubahan';
            if (str_contains(strtolower($activity->action), 'hapus')) return 'Penghapusan';
            if (str_contains(strtolower($activity->action), 'login')) return 'Login';
            return 'Lainnya';
        });
    @endphp
    
    <h3 style="color: #6366f1; margin-bottom: 15px;">📈 Analisis Jenis Aktivitas</h3>
    <div class="activity-type-grid">
        @foreach($activityTypes as $type => $typeActivities)
            <div class="activity-type-card">
                @if($type == 'Penambahan')
                    <div class="activity-icon icon-add">+</div>
                @elseif($type == 'Perubahan')
                    <div class="activity-icon icon-update">✓</div>
                @elseif($type == 'Penghapusan')
                    <div class="activity-icon icon-delete">×</div>
                @elseif($type == 'Login')
                    <div class="activity-icon icon-login">👤</div>
                @else
                    <div class="activity-icon icon-other">•</div>
                @endif
                
                <h4 style="margin: 0 0 5px 0; font-size: 10px; color: #374151;">{{ $type }}</h4>
                <div style="font-size: 16px; font-weight: bold; color: #6366f1;">{{ $typeActivities->count() }}</div>
                <div style="font-size: 8px; color: #6b7280;">{{ number_format(($typeActivities->count() / $activities->count()) * 100, 1) }}%</div>
            </div>
        @endforeach
    </div>
    
    <!-- Most Active Users -->
    @php
        $userActivities = $activities->groupBy('user')->map->count()->sortDesc()->take(5);
    @endphp
    
    @if($userActivities->count() > 0)
        <h3 style="color: #6366f1; margin-bottom: 15px;">👥 User Paling Aktif Bulan Ini</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Ranking</th>
                    <th style="width: 40%;">Nama User</th>
                    <th style="width: 20%;">Aktivitas</th>
                    <th style="width: 15%;">Persentase</th>
                    <th style="width: 15%;">Kontribusi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userActivities as $user => $count)
                    <tr>
                        <td class="text-center">
                            @if($loop->iteration == 1)
                                🥇 #1
                            @elseif($loop->iteration == 2)
                                🥈 #2
                            @elseif($loop->iteration == 3)
                                🥉 #3
                            @else
                                #{{ $loop->iteration }}
                            @endif
                        </td>
                        <td><strong>{{ $user }}</strong></td>
                        <td class="text-center font-bold">{{ $count }}</td>
                        <td class="text-center">{{ number_format(($count / $activities->count()) * 100, 1) }}%</td>
                        <td class="text-center">
                            @php $percentage = ($count / $activities->count()) * 100; @endphp
                            @if($percentage > 40)
                                🔥 Sangat Tinggi
                            @elseif($percentage > 25)
                                📈 Tinggi
                            @elseif($percentage > 15)
                                📊 Sedang
                            @else
                                📋 Normal
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <!-- Recent Activities Timeline -->
    <div class="page-break">
        <h3 style="color: #6366f1; margin-bottom: 15px;">⏰ Timeline Aktivitas Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">User</th>
                    <th style="width: 40%;">Aktivitas</th>
                    <th style="width: 12%;">Model</th>
                    <th style="width: 15%;">Waktu</th>
                    <th style="width: 13%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities->take(20) as $index => $activity)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $activity->user }}</strong></td>
                        <td>{{ $activity->action }}</td>
                        <td class="text-center">
                            @if($activity->model)
                                <span class="status-badge" style="background-color: #e5e7eb; color: #374151;">
                                    {{ $activity->model }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <div>{{ $activity->created_at->format('d/m/Y') }}</div>
                            <small style="color: #666;">{{ $activity->created_at->format('H:i') }}</small>
                        </td>
                        <td class="text-center">
                            @if(str_contains(strtolower($activity->action), 'tambah'))
                                <span class="status-badge" style="background-color: #dcfce7; color: #16a34a;">Tambah</span>
                            @elseif(str_contains(strtolower($activity->action), 'update'))
                                <span class="status-badge" style="background-color: #dbeafe; color: #2563eb;">Update</span>
                            @elseif(str_contains(strtolower($activity->action), 'hapus'))
                                <span class="status-badge" style="background-color: #fee2e2; color: #dc2626;">Hapus</span>
                            @else
                                <span class="status-badge" style="background-color: #f3f4f6; color: #374151;">Lainnya</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Monthly Insights & Recommendations -->
    <div style="background-color: #eef2ff; padding: 15px; border-left: 4px solid #6366f1; margin-bottom: 20px;">
        <h4 style="margin: 0 0 10px 0; color: #3730a3;">💡 Insight & Rekomendasi Bulanan:</h4>
        <ul style="margin: 0; padding-left: 20px; color: #3730a3; font-size: 10px;">
            @if($activities->count() > 0)
                <li><strong>Produktivitas:</strong> Rata-rata {{ number_format($activities->count() / $month->daysInMonth, 1) }} aktivitas per hari</li>
                @if($products->count() > 0)
                    <li><strong>Pertumbuhan Produk:</strong> {{ $products->count() }} produk baru dengan nilai total Rp {{ number_format($products->sum(function($p) { return $p->stock_quantity * $p->getDefaultPrice(); }), 0, ',', '.') }}</li>
                @endif
                @if($userActivities->count() > 0)
                    <li><strong>User Terbaik:</strong> {{ $userActivities->keys()->first() }} dengan {{ $userActivities->first() }} aktivitas ({{ number_format(($userActivities->first() / $activities->count()) * 100, 1) }}%)</li>
                @endif
                @php
                    $dominantActivity = $activityTypes->sortByDesc->count()->keys()->first();
                @endphp
                @if($dominantActivity)
                    <li><strong>Aktivitas Dominan:</strong> {{ $dominantActivity }} ({{ $activityTypes[$dominantActivity]->count() }} aktivitas)</li>
                @endif
                <li><strong>Rekomendasi:</strong> Pertahankan konsistensi aktivitas dan evaluasi produk dengan stok rendah</li>
            @else
                <li>Tidak ada aktivitas yang tercatat dalam periode ini - perlu evaluasi sistem</li>
            @endif
        </ul>
    </div>
    
    <!-- Performance Metrics -->
    <div class="summary-section">
        <h3 style="margin-top: 0; color: #6366f1; margin-bottom: 15px;">📊 Metrik Performa</h3>
        <table style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 40%;">Metrik</th>
                    <th style="width: 20%;">Nilai</th>
                    <th style="width: 20%;">Target</th>
                    <th style="width: 20%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Aktivitas Harian Rata-rata</td>
                    <td class="text-center">{{ number_format($activities->count() / $month->daysInMonth, 1) }}</td>
                    <td class="text-center">≥ 5</td>
                    <td class="text-center">
                        @if(($activities->count() / $month->daysInMonth) >= 5)
                            <span style="color: #16a34a;">✅ Tercapai</span>
                        @else
                            <span style="color: #dc2626;">❌ Belum</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Produk Baru per Bulan</td>
                    <td class="text-center">{{ $products->count() }}</td>
                    <td class="text-center">≥ 10</td>
                    <td class="text-center">
                        @if($products->count() >= 10)
                            <span style="color: #16a34a;">✅ Tercapai</span>
                        @else
                            <span style="color: #dc2626;">❌ Belum</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Alert Stok Rendah</td>
                    <td class="text-center">{{ \App\Models\Produk::where('stock_quantity', '<', 10)->count() }}</td>
                    <td class="text-center">≤ 5</td>
                    <td class="text-center">
                        @if(\App\Models\Produk::where('stock_quantity', '<', 10)->count() <= 5)
                            <span style="color: #16a34a;">✅ Baik</span>
                        @else
                            <span style="color: #f59e0b;">⚠️ Perhatian</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>Al-Ruhamaa' Inventory System</strong><br>
            Yatim Center Management System<br>
            Laporan bulanan ini dibuat secara otomatis pada {{ $generatedAt->format('d F Y, H:i') }} WIB
        </p>
        <p style="margin-top: 15px; font-style: italic;">
            "Dan barangsiapa yang menghidupkan satu jiwa, maka seakan-akan dia telah menghidupkan seluruh manusia."
        </p>
    </div>
</body>
</html>