<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pergerakan Stok - Al-Ruhamaa' Inventory System</title>
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
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .logo {
            color: #2563eb;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #1e40af;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .report-title {
            color: #2563eb;
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
            background-color: #eff6ff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
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
        
        .status-add {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .status-update {
            background-color: #dbeafe;
            color: #2563eb;
        }
        
        .status-delete {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .status-login {
            background-color: #f3e8ff;
            color: #7c3aed;
        }
        
        .status-other {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
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
        
        .timeline-section {
            margin-top: 30px;
        }
        
        .timeline-item {
            margin-bottom: 15px;
            padding: 10px;
            border-left: 3px solid #2563eb;
            background-color: #f8fafc;
        }
        
        .timeline-time {
            font-size: 9px;
            color: #666;
            font-weight: bold;
        }
        
        .timeline-content {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">AL-RUHAMAA' INVENTORY SYSTEM</div>
        <div class="subtitle">Yatim Center Management System</div>
        <div class="report-title">LAPORAN PERGERAKAN STOK</div>
    </div>
    
    <!-- Report Info -->
    <div class="report-info">
        <div>
            <strong>Tanggal Cetak:</strong> {{ $generatedAt->format('d F Y, H:i') }} WIB
        </div>
        <div>
            <strong>Total Aktivitas:</strong> {{ $activities->count() }} kegiatan
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="margin-top: 0; color: #2563eb; margin-bottom: 15px;">Ringkasan Aktivitas</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $activities->count() }}</div>
                <div class="summary-label">Total Aktivitas</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activities->where('model', 'Produk')->count() }}</div>
                <div class="summary-label">Aktivitas Produk</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activities->where('model', 'User')->count() }}</div>
                <div class="summary-label">Aktivitas User</div>
            </div>
        </div>
    </div>
    
    <!-- Activities Table -->
    <h3 style="color: #2563eb; margin-bottom: 15px;">Riwayat Aktivitas Sistem</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">User</th>
                <th style="width: 35%;">Aktivitas</th>
                <th style="width: 12%;">Model</th>
                <th style="width: 8%;">ID Record</th>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 10%;">Jenis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities->take(50) as $index => $activity)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $activity->user }}</strong>
                    </td>
                    <td>{{ $activity->action }}</td>
                    <td class="text-center">
                        @if($activity->model)
                            <span class="status-badge 
                                @if($activity->model == 'Produk') status-add
                                @elseif($activity->model == 'User') status-update
                                @elseif($activity->model == 'Supplier') status-login
                                @else status-other @endif">
                                {{ $activity->model }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $activity->record_id ?? '-' }}</td>
                    <td class="text-center">
                        <div>{{ $activity->created_at->format('d/m/Y') }}</div>
                        <small style="color: #666;">{{ $activity->created_at->format('H:i:s') }}</small>
                    </td>
                    <td class="text-center">
                        @if(str_contains(strtolower($activity->action), 'tambah') || str_contains(strtolower($activity->action), 'create'))
                            <span class="status-badge status-add">Tambah</span>
                        @elseif(str_contains(strtolower($activity->action), 'update') || str_contains(strtolower($activity->action), 'edit'))
                            <span class="status-badge status-update">Update</span>
                        @elseif(str_contains(strtolower($activity->action), 'hapus') || str_contains(strtolower($activity->action), 'delete'))
                            <span class="status-badge status-delete">Hapus</span>
                        @elseif(str_contains(strtolower($activity->action), 'login'))
                            <span class="status-badge status-login">Login</span>
                        @else
                            <span class="status-badge status-other">Lainnya</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($activities->count() > 50)
        <div style="background-color: #fef3c7; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <p style="margin: 0; color: #92400e; font-size: 10px;">
                <strong>Catatan:</strong> Laporan ini menampilkan 50 aktivitas terbaru dari total {{ $activities->count() }} aktivitas. 
                Untuk melihat semua aktivitas, gunakan filter tanggal yang lebih spesifik.
            </p>
        </div>
    @endif
    
    <!-- Activity Type Analysis -->
    @php
        $activityTypes = $activities->groupBy(function($activity) {
            if (str_contains(strtolower($activity->action), 'tambah')) return 'Penambahan';
            if (str_contains(strtolower($activity->action), 'update')) return 'Perubahan';
            if (str_contains(strtolower($activity->action), 'hapus')) return 'Penghapusan';
            if (str_contains(strtolower($activity->action), 'login')) return 'Login';
            return 'Lainnya';
        });
    @endphp
    
    <div class="summary-section">
        <h3 style="margin-top: 0; color: #2563eb; margin-bottom: 15px;">Analisis Jenis Aktivitas</h3>
        <table style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 30%;">Jenis Aktivitas</th>
                    <th style="width: 20%;">Jumlah</th>
                    <th style="width: 20%;">Persentase</th>
                    <th style="width: 30%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activityTypes as $type => $typeActivities)
                    <tr>
                        <td>
                            @if($type == 'Penambahan')
                                <span class="status-badge status-add">{{ $type }}</span>
                            @elseif($type == 'Perubahan')
                                <span class="status-badge status-update">{{ $type }}</span>
                            @elseif($type == 'Penghapusan')
                                <span class="status-badge status-delete">{{ $type }}</span>
                            @elseif($type == 'Login')
                                <span class="status-badge status-login">{{ $type }}</span>
                            @else
                                <span class="status-badge status-other">{{ $type }}</span>
                            @endif
                        </td>
                        <td class="text-center font-bold">{{ $typeActivities->count() }}</td>
                        <td class="text-center">{{ number_format(($typeActivities->count() / $activities->count()) * 100, 1) }}%</td>
                        <td>
                            @if($type == 'Penambahan')
                                Aktivitas penambahan data baru
                            @elseif($type == 'Perubahan')
                                Aktivitas perubahan/update data
                            @elseif($type == 'Penghapusan')
                                Aktivitas penghapusan data
                            @elseif($type == 'Login')
                                Aktivitas login user
                            @else
                                Aktivitas lainnya
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Most Active Users -->
    @php
        $mostActiveUsers = $activities->groupBy('user')->map->count()->sortDesc()->take(5);
    @endphp
    
    @if($mostActiveUsers->count() > 0)
        <div class="summary-section">
            <h3 style="margin-top: 0; color: #2563eb; margin-bottom: 15px;">User Paling Aktif</h3>
            <table style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 10%;">Ranking</th>
                        <th style="width: 40%;">Nama User</th>
                        <th style="width: 25%;">Jumlah Aktivitas</th>
                        <th style="width: 25%;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mostActiveUsers as $user => $count)
                        <tr>
                            <td class="text-center">
                                @if($loop->iteration == 1)
                                    🥇
                                @elseif($loop->iteration == 2)
                                    🥈
                                @elseif($loop->iteration == 3)
                                    🥉
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>
                            <td><strong>{{ $user }}</strong></td>
                            <td class="text-center font-bold">{{ $count }}</td>
                            <td class="text-center">{{ number_format(($count / $activities->count()) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <!-- Timeline Section (if activities are from today) -->
    @php
        $todayActivities = $activities->where('created_at', '>=', now()->startOfDay())->take(10);
    @endphp
    
    @if($todayActivities->count() > 0)
        <div class="timeline-section">
            <h3 style="color: #2563eb; margin-bottom: 15px;">Timeline Aktivitas Hari Ini</h3>
            @foreach($todayActivities as $activity)
                <div class="timeline-item">
                    <div class="timeline-time">{{ $activity->created_at->format('H:i:s') }}</div>
                    <div class="timeline-content">
                        <strong>{{ $activity->user }}</strong> {{ $activity->action }}
                        @if($activity->model)
                            <span style="color: #666;">({{ $activity->model }})</span>
                        @endif
                    </div>
                </div>
            @endforeach
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