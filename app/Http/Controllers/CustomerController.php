<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PosTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');
        $hasActiveFilter = (bool) ($search || $filter !== 'all');

        // Base query
        $query = Customer::query();

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Get customer names that have transactions
        $customersWithTransactions = PosTransaction::distinct()->pluck('customer_name')->toArray();

        // Apply filter
        switch ($filter) {
            case 'recent':
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
                break;
            case 'with-transactions':
                $query->whereIn('customer_name', $customersWithTransactions);
                break;
            case 'no-transactions':
                $query->whereNotIn('customer_name', $customersWithTransactions);
                break;
        }

        $customers = $query->orderBy('customer_name')->paginate(10)->withQueryString();

        // Stats
        $stats = [
            'total' => Customer::count(),
            'newThisMonth' => Customer::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'withTransactions' => Customer::whereIn('customer_name', $customersWithTransactions)->count(),
            'noPhone' => Customer::where(function($q) {
                $q->whereNull('phone')->orWhere('phone', '');
            })->count(),
        ];

        return view('customers.index', [
            'customers' => $customers,
            'stats' => $stats,
            'search' => $search,
            'filter' => $filter,
            'hasActiveFilter' => $hasActiveFilter,
            'customersWithTransactions' => $customersWithTransactions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
    $validated = $this->validateCustomer($request);
    $validated['shipping_cost'] = $validated['shipping_cost'] ?? 0;

    Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
    $validated = $this->validateCustomer($request);
    $validated['shipping_cost'] = $validated['shipping_cost'] ?? 0;

    $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    private function validateCustomer(Request $request): array
    {
        return $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
