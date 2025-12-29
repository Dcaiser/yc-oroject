<?php

namespace Database\Seeders;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaAsepExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            ['2025-12-01', 'Fee Pa Asep', 97500, 'Fee'],
            ['2025-12-05', 'Fee Pa Asep', 139500, 'Fee'],
            ['2025-12-05', 'Kasbon Pa Asep', 200000, 'Kasbon'],
            ['2025-12-07', 'Fee Pa Asep', 65000, 'Fee'],
            ['2025-12-07', 'Kasbon Pa Asep', 60500, 'Kasbon'],
        ];

        $startDate = '2025-12-01';
        $endDate = '2025-12-31';

        DB::transaction(function () use ($expenses, $startDate, $endDate) {
            Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('description', 'Jasa Pengantaran Pa Asep')
                ->delete();

            foreach ($expenses as [$date, $description, $amount, $category]) {
                Expense::updateOrCreate(
                    [
                        'expense_date' => Carbon::parse($date)->format('Y-m-d'),
                        'description' => $description,
                    ],
                    [
                        'amount' => $amount,
                        'category' => $category,
                    ]
                );
            }
        });
    }
}
