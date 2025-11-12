<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'supplier_code' => 'SUP001',
                'name' => 'PT Maju Bersama',
                'contact_person' => 'Budi Santoso',
                'phone' => '021-5550123',
                'email' => 'info@majubersama.com',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'npwp' => '01.234.567.8-123.000',
            ],
            [
                'supplier_code' => 'SUP002',
                'name' => 'CV Sukses Mandiri',
                'contact_person' => 'Siti Rahma',
                'phone' => '021-5550456',
                'email' => 'contact@suksesmandiri.co.id',
                'address' => 'Jl. Thamrin No. 45, Jakarta Pusat',
                'npwp' => '02.345.678.9-234.000',
            ],
            [
                'supplier_code' => 'SUP003',
                'name' => 'UD Makmur Jaya',
                'contact_person' => 'Ahmad Hidayat',
                'phone' => '021-5550789',
                'email' => 'udmakmurjaya@gmail.com',
                'address' => 'Jl. Gatot Subroto No. 67, Jakarta Selatan',
                'npwp' => null,
            ],
            [
                'supplier_code' => 'SUP004',
                'name' => 'PT Global Teknologi',
                'contact_person' => 'Dewi Sartika',
                'phone' => '021-5550321',
                'email' => 'sales@globalteknologi.com',
                'address' => 'Jl. Kuningan No. 89, Jakarta Selatan',
                'npwp' => '03.456.789.0-345.000',
            ],
            [
                'supplier_code' => 'SUP005',
                'name' => 'CV Berkah Abadi',
                'contact_person' => 'Rudi Hermawan',
                'phone' => '021-5550654',
                'email' => 'berkahabadi@yahoo.com',
                'address' => 'Jl. Rasuna Said No. 12, Jakarta Selatan',
                'npwp' => '04.567.890.1-456.000',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['supplier_code' => $supplier['supplier_code']],
                $supplier
            );
        }
    }
}
