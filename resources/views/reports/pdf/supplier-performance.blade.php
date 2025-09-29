<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Performa Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1f7c4d;
            padding-bottom: 10px;
        }
        
        .title {
            color: #1f7c4d;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .date {
            font-size: 10px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background-color: #1f7c4d;
            color: #1f7c4d;
            padding: 6px;
            text-align: left;
            font-size: 9px;
            border: 1px solid #1f7c4d;
        }
        
        td {
            padding: 4px 6px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-section {
            background-color: #f3e8ff;
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
            color: #7c3aed;
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
        
        .status-active {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .status-inactive {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .status-high {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .status-medium {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .status-low {
            background-color: #dbeafe;
            color: #2563eb;
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
        
        .top-suppliers {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .top-supplier-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .ranking-badge {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .rank-1 { background-color: #fbbf24; }
        .rank-2 { background-color: #9ca3af; }
        .rank-3 { background-color: #f59e0b; }
        
        .stars {
            color: #fbbf24;
        }
        
        .star-empty {
            color: #d1d5db;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN PERFORMA SUPPLIER</div>
        <div class="date">{{ now()->format('d F Y, H:i') }} WIB</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Supplier</th>
                <th>Kontak</th>
                <th>Jumlah Order</th>
                <th>Total Value</th>
                <th>Avg Delivery</th>
                <th>Rating</th>
                <th>Performance Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $index => $supplier)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $supplier->nama_supplier }}</td>
                    <td>{{ $supplier->kontak }}</td>
                    <td>{{ $supplier->orders_count }}</td>
                    <td>Rp {{ number_format($supplier->total_value, 0, ',', '.') }}</td>
                    <td>{{ number_format($supplier->avg_delivery_time, 1) }} hari</td>
                    <td>{{ number_format($supplier->rating, 1) }}/5</td>
                    <td>{{ number_format($supplier->performance_score, 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
            Laporan ini dibuat secara otomatis pada {{ $generatedAt->format('d F Y, H:i') }} WIB
        </p>
    </div>
</body>
</html>