<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mingguan - Al-Ruhamaa' Inventory System</title>
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
            border-bottom: 2px solid #16a34a;
            padding-bottom: 20px;
        }
        
        .logo {
            color: #16a34a;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #15803d;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .report-title {
            color: #16a34a;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .report-period {
            background-color: #dcfce7;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            color: #166534;
        }
        
        .summary-section {
            background-color: #f0fdf4;
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
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        
        .daily-chart {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 5px;
        }
        
        .daily-item {
            text-align: center;
        }
        
        .daily-label {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #374151;
        }
        
        .daily-bar {
            background-color: #dcfce7;
            border-radius: 3px;
            margin: 5px auto;
            width: 20px;
            min-height: 5px;
            display: flex;
            align-items: end;
            justify-content: center;
        }
        
        .daily-count {
            font-size: 8px;
            font-weight: bold;
            color: #166534;
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
        
        .activity-icon {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 8px;
            margin-right: 5px;
        }
        
        .icon-add { background-color: #16a34a; }
        .icon-update { background-color: #2563eb; }
        .icon-delete { background-color: #dc2626; }
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
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">AL-RUHAMAA' INVENTORY SYSTEM</div>
        <div class="subtitle">Yatim Center Management System</div>
        <div class="report-title">LAPORAN MINGGUAN</div>
    </div>
    
    <!-- Report Period -->
    <div class="report-period">
        Periode: {{ $startOfWeek->format('d F Y') }} - {{ $endOfWeek->format('d F Y') }}
        <br>
        <small style="font-weight: normal; font-size: 10px;">
            Dibuat pada: {{ $generatedAt->format('d F Y, H:i') }} WIB
        </small>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="margin-top: 0; color: #16a34a; margin-bottom: 15px;">Ringkasan Mingguan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $activities->count() }}</div>
                <div class="summary-label">Total Aktivitas</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activities->where('action', 'like', '%tambah%')->where('model', 'Produk')->count() }}</div>
                <div class="summary-label">Produk Ditambah</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activities->where('action', 'like', '%update%')->count() }}</div>
                <div class="summary-label">Update Stok</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($activities->count() / 7, 1) }}</div>
                <div class="summary-label">Rata-rata Harian</div>
            </div>
        </div>
    </div>
    
    <!-- Daily Activity Chart -->
    <h3 style="color: #16a34a; margin-bottom: 15px;">📊 Distribusi Aktivitas Harian</h3>
    <div class="daily-chart">
        @php
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $currentWeek = $startOfWeek->copy();
            $dailyActivities = $activities->groupBy(function($activity) {
                return $activity->created_at->format('Y-m-d');
            });
            $maxDaily = $dailyActivities->map->count()->max() ?: 1;
        @endphp
        
        @for($i = 0; $i < 7; $i++)
            @php
                $dayDate = $currentWeek->copy()->addDays($i);
                $dayActivities = $dailyActivities->get($dayDate->format('Y-m-d'), collect());
                $count = $dayActivities->count();
                $height = $maxDaily > 0 ? max(($count / $maxDaily) * 50, 5) : 5;
            @endphp
            
            <div class="daily-item">
                <div class="daily-label">{{ $days[$i] }}</div>
                <div class="daily-label">{{ $dayDate->format('d/m') }}</div>
                <div class="daily-bar" style="height: {{ $height }}px; background-color: {{ $count > 0 ? '#dcfce7' : '#f3f4f6' }};">
                </div>
                <div class="daily-count">{{ $count }}</div>
            </div>
        @endfor
    </div>
    
    <!-- Weekly Activities Summary -->
    <h3 style="color: #16a34a; margin-bottom: 15px;">📋 Ringkasan Aktivitas</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Hari</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 55%;">Aktivitas Utama</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i < 7; $i++)
                @php
                    $dayDate = $currentWeek->copy()->addDays($i);
                    $dayActivities = $dailyActivities->get($dayDate->format('Y-m-d'), collect());
                @endphp
                
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $days[$i] }}</td>
                    <td>{{ $dayDate->format('d M Y') }}</td>
                    <td class="text-center font-bold">{{ $dayActivities->count() }}</td>
                    <td>
                        @if($dayActivities->count() > 0)
                            @foreach($dayActivities->take(3) as $activity)
                                <div style="margin-bottom: 3px; font-size: 9px;">
                                    @if(str_contains(strtolower($activity->action), 'tambah'))
                                        <span class="activity-icon icon-add">+</span>
                                    @elseif(str_contains(strtolower($activity->action), 'update'))
                                        <span class="activity-icon icon-update">✓</span>
                                    @elseif(str_contains(strtolower($activity->action), 'hapus'))
                                        <span class="activity-icon icon-delete">×</span>
                                    @else
                                        <span class="activity-icon icon-other">•</span>
                                    @endif
                                    <strong>{{ $activity->user }}</strong> {{ Str::limit($activity->action, 40) }}
                                    <span style="color: #6b7280;">({{ $activity->created_at->format('H:i') }})</span>
                                </div>
                            @endforeach
                            @if($dayActivities->count() > 3)
                                <div style="font-size: 8px; color: #6b7280; font-style: italic;">
                                    ... dan {{ $dayActivities->count() - 3 }} aktivitas lainnya
                                </div>
                            @endif
                        @else
                            <span style="color: #9ca3af; font-style: italic;">Tidak ada aktivitas</span>
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
    
    <!-- Most Active Users This Week -->
    @php
        $userActivities = $activities->groupBy('user')->map->count()->sortDesc()->take(5);
    @endphp
    
    @if($userActivities->count() > 0)
        <h3 style="color: #16a34a; margin-bottom: 15px;">👑 User Paling Aktif Minggu Ini</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Ranking</th>
                    <th style="width: 40%;">Nama User</th>
                    <th style="width: 25%;">Jumlah Aktivitas</th>
                    <th style="width: 25%;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userActivities as $user => $count)
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
    @endif
    
    <!-- Activity Types Breakdown -->
    @php
        $activityTypes = $activities->groupBy(function($activity) {
            if (str_contains(strtolower($activity->action), 'tambah')) return 'Penambahan';
            if (str_contains(strtolower($activity->action), 'update')) return 'Perubahan';
            if (str_contains(strtolower($activity->action), 'hapus')) return 'Penghapusan';
            if (str_contains(strtolower($activity->action), 'login')) return 'Login';
            return 'Lainnya';
        });
    @endphp
    
    <h3 style="color: #16a34a; margin-bottom: 15px;">📈 Breakdown Jenis Aktivitas</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Jenis Aktivitas</th>
                <th style="width: 20%;">Jumlah</th>
                <th style="width: 20%;">Persentase</th>
                <th style="width: 30%;">Trend</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activityTypes as $type => $typeActivities)
                <tr>
                    <td>
                        @if($type == 'Penambahan')
                            <span class="activity-icon icon-add">+</span>
                        @elseif($type == 'Perubahan')
                            <span class="activity-icon icon-update">✓</span>
                        @elseif($type == 'Penghapusan')
                            <span class="activity-icon icon-delete">×</span>
                        @else
                            <span class="activity-icon icon-other">•</span>
                        @endif
                        {{ $type }}
                    </td>
                    <td class="text-center font-bold">{{ $typeActivities->count() }}</td>
                    <td class="text-center">{{ number_format(($typeActivities->count() / $activities->count()) * 100, 1) }}%</td>
                    <td>
                        @php
                            $percentage = ($typeActivities->count() / $activities->count()) * 100;
                        @endphp
                        @if($percentage > 40)
                            📈 Sangat Tinggi
                        @elseif($percentage > 25)
                            📊 Tinggi
                        @elseif($percentage > 10)
                            📉 Sedang
                        @else
                            📋 Rendah
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Key Insights -->
    <div style="background-color: #f0fdf4; padding: 15px; border-left: 4px solid #16a34a; margin-bottom: 20px;">
        <h4 style="margin: 0 0 10px 0; color: #166534;">💡 Insight Mingguan:</h4>
        <ul style="margin: 0; padding-left: 20px; color: #166534; font-size: 10px;">
            @if($activities->count() > 0)
                <li>Rata-rata {{ number_format($activities->count() / 7, 1) }} aktivitas per hari selama minggu ini</li>
                @php
                    $mostActiveDay = $dailyActivities->map->count()->keys()->first();
                    $mostActiveDayName = $days[Carbon\Carbon::parse($mostActiveDay)->dayOfWeek - 1] ?? 'N/A';
                @endphp
                @if($mostActiveDay)
                    <li>Hari paling aktif: {{ $mostActiveDayName }} ({{ $dailyActivities->get($mostActiveDay)->count() }} aktivitas)</li>
                @endif
                @if($userActivities->count() > 0)
                    <li>User paling aktif: {{ $userActivities->keys()->first() }} ({{ $userActivities->first() }} aktivitas)</li>
                @endif
                @php
                    $dominantActivity = $activityTypes->sortByDesc->count()->keys()->first();
                @endphp
                @if($dominantActivity)
                    <li>Jenis aktivitas dominan: {{ $dominantActivity }} ({{ $activityTypes[$dominantActivity]->count() }} aktivitas)</li>
                @endif
            @else
                <li>Tidak ada aktivitas yang tercatat dalam periode ini</li>
            @endif
        </ul>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>Al-Ruhamaa' Inventory System</strong><br>
            Yatim Center Management System<br>
            Laporan mingguan ini dibuat secara otomatis pada {{ $generatedAt->format('d F Y, H:i') }} WIB
        </p>
        <p style="margin-top: 15px; font-style: italic;">
            "Dan barangsiapa yang menghidupkan satu jiwa, maka seakan-akan dia telah menghidupkan seluruh manusia."
        </p>
    </div>
</body>
</html>