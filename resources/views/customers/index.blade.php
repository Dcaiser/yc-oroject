<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 text-green-600 fas fa-users"></i>{{ __('Daftar Pelanggan') }}
            </h2>
        </div>
    </x-slot>

    <div class="px-4 py-8 mx-auto max-w-6xl"
         x-data="customerManager()"
         x-init="init()">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-gray-800">Manajemen Pelanggan</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data pelanggan serta ongkir default untuk transaksi POS.</p>
        </div>

        @if (session('success'))
            <div class="px-4 py-3 mb-6 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-4 bg-white rounded-lg shadow-sm">
            <div class="flex flex-col gap-4 mb-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Pelanggan</h2>
                    <p class="text-sm text-gray-500">Total pelanggan: {{ $customers->count() }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700"
                        @click="customerModalOpen = true; customerMode = 'create'; resetCustomerForm();">
                        <i class="fas fa-user-plus"></i>
                        Tambah Customer
                    </button>
                    <input type="text" x-model="search" placeholder="Cari nama atau telepon"
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg md:w-64 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left divide-y divide-gray-200">
                    <thead class="bg-green-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium text-gray-700">Nama</th>
                            <th class="px-4 py-3 text-xs font-medium text-gray-700">Telepon</th>
                            <th class="px-4 py-3 text-xs font-medium text-gray-700">Alamat</th>
                            <th class="px-4 py-3 text-xs font-medium text-gray-700">Ongkir Default</th>
                            <th class="px-4 py-3 text-xs font-medium text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <template x-if="filteredCustomers().length === 0">
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-sm text-center text-gray-500">Belum ada pelanggan terdaftar.</td>
                            </tr>
                        </template>
                        <template x-for="customer in filteredCustomers()" :key="customer.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900" x-text="customer.customer_name"></td>
                                <td class="px-4 py-3 text-sm text-gray-700" x-text="customer.phone || '-' "></td>
                                <td class="px-4 py-3 text-sm text-gray-700" x-text="customer.address || '-' "></td>
                                <td class="px-4 py-3 text-sm text-gray-700" x-text="formatCurrency(customer.shipping_cost)"></td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button"
                                            class="px-3 py-1 text-white rounded bg-emerald-600 hover:bg-emerald-700"
                                            @click="openForEdit(customer)">
                                            Edit
                                        </button>
                                        <form :action="destroyUrl(customer.id)" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-600">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Customer -->
        <div
            x-show="customerModalOpen"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
            style="display: none;"
            x-cloak>
            <div class="relative w-[90%] md:w-[70%] bg-white rounded-lg shadow-xl p-6"
                 @click.away="closeModal()">
                <button @click="closeModal()" class="absolute text-xl text-gray-500 top-3 right-3 hover:text-gray-700">&times;</button>

                <h2 class="text-2xl font-semibold mb-4" x-text="customerMode === 'create' ? 'Tambah Customer' : 'Edit Customer'"></h2>

                <form method="POST" :action="formAction()">
                    @csrf
                    <template x-if="customerMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 text-sm font-medium">Nama Customer</label>
                            <input type="text" name="customer_name" x-model="customerForm.customer_name" required
                                   class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Telepon</label>
                            <input type="text" name="phone" x-model="customerForm.phone"
                                   class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Ongkir Default</label>
                            <div class="relative">
                                <span class="absolute text-sm font-semibold text-green-600 -translate-y-1/2 left-3 top-1/2">Rp</span>
                                <input type="number" min="0" name="shipping_cost" x-model.number="customerForm.shipping_cost"
                                       class="w-full p-2 pl-8 border border-gray-300 rounded" placeholder="0">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-medium">Alamat</label>
                            <textarea name="address" x-model="customerForm.address" rows="3"
                                      class="w-full p-2 border border-gray-300 rounded"></textarea>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="px-4 py-2 text-white rounded bg-emerald-600">Simpan</button>
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerManager', () => ({
                customerModalOpen: false,
                customerMode: 'create',
                customerForm: {
                    id: '',
                    customer_name: '',
                    phone: '',
                    address: '',
                    shipping_cost: 0,
                },
                customers: @json($customers),
                search: '',

                init() {
                    this.sortCustomers();
                },

                sortCustomers() {
                    this.customers = this.customers.sort((a, b) => a.customer_name.localeCompare(b.customer_name));
                },

                filteredCustomers() {
                    if (!this.search.trim()) {
                        return this.customers;
                    }

                    const keyword = this.search.toLowerCase();
                    return this.customers.filter(customer => {
                        return (
                            (customer.customer_name && customer.customer_name.toLowerCase().includes(keyword)) ||
                            (customer.phone && customer.phone.toLowerCase().includes(keyword))
                        );
                    });
                },

                resetCustomerForm() {
                    this.customerForm = {
                        id: '',
                        customer_name: '',
                        phone: '',
                        address: '',
                        shipping_cost: 0,
                    };
                },

                openForEdit(customer) {
                    this.customerMode = 'edit';
                    this.customerForm = { ...customer };
                    this.customerModalOpen = true;
                },

                closeModal() {
                    this.customerModalOpen = false;
                    this.customerMode = 'create';
                    this.resetCustomerForm();
                },

                formAction() {
                    if (this.customerMode === 'edit') {
                        return '{{ url('/customers') }}/' + this.customerForm.id;
                    }

                    return '{{ route('customers.store') }}';
                },

                destroyUrl(id) {
                    return '{{ url('/customers') }}/' + id;
                },

                formatCurrency(value) {
                    const amount = Number(value) || 0;
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
                },
            }));
        });
    </script>
</x-app-layout>
