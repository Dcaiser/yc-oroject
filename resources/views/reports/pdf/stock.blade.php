<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pergerakan Stok</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .period {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Laporan Pergerakan Stok</h1>
    <div class="period">Periode: {{ $start }} - {{ $end }}</div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No.</th>
                <th width="25%">Periode</th>
                <th class="text-right" width="17%">Total Aktivitas</th>
                <th class="text-right" width="17%">Stok Masuk</th>
                <th class="text-right" width="17%">Stok Keluar</th>
                <th class="text-right" width="17%">Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['label'] }}</td>
                    <td class="text-right">{{ number_format($row['total']) }}</td>
                    <td class="text-right">{{ number_format($row['stock_in']) }}</td>
                    <td class="text-right">{{ number_format($row['stock_out']) }}</td>
                    <td class="text-right">{{ number_format($row['ending_stock']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
