<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
    $validated = $this->validateCustomer($request);
    $validated['shipping_cost'] = $validated['shipping_cost'] ?? 0;

    Customer::create($validated);

        return redirect()
            ->route('category')
            ->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
    $validated = $this->validateCustomer($request);
    $validated['shipping_cost'] = $validated['shipping_cost'] ?? 0;

    $customer->update($validated);

        return redirect()
            ->route('category')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('category')
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
