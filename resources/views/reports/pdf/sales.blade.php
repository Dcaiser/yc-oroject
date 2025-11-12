<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Penjualan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .period {
            text-align: center;
            font-size: 11px;
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
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            font-size: 9px;
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
    <h1>Laporan Transaksi Penjualan</h1>
    <div class="period">Periode: {{ $start }} - {{ $end }}</div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" width="3%">No.</th>
                <th width="10%">Tanggal</th>
                <th width="15%">Customer</th>
                <th width="10%">Tipe</th>
                <th width="20%">Produk</th>
                <th width="8%">Qty</th>
                <th class="text-right" width="12%">Harga Satuan</th>
                <th class="text-right" width="12%">Total</th>
                <th class="text-right" width="10%">Ongkir</th>
                <th class="text-right" width="12%">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['customer_name'] }}</td>
                    <td>{{ $row['customer_type'] }}</td>
                    <td>{{ $row['product_name'] }}</td>
                    <td>{{ $row['qty'] }}</td>
                    <td class="text-right">{{ $row['price_per_unit'] }}</td>
                    <td class="text-right">{{ $row['total_price'] }}</td>
                    <td class="text-right">{{ $row['shipping_cost'] }}</td>
                    <td class="text-right">{{ $row['grand_total'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
