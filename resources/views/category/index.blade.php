<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 text-green-600 fas fa-tags"></i>{{ __('Kategori dan Satuan') }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('addcategory') }}"
                    class="flex items-center px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="mr-2 fas fa-plus"></i>Tambah Kategori
                </a>

                <a href="{{ route('addunit') ?? '#' }}"
                    class="flex items-center px-4 py-2 text-white rounded-lg bg-emerald-500 hover:bg-emerald-600">
                    <i class="mr-2 fas fa-weight"></i>Tambah Satuan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="px-4 py-8 mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-gray-800">Kategori dan Satuan</h1>
            <p class="mt-1 text-sm text-gray-500">Atur kategori produk dan daftar satuan konversi di bawahnya.</p>
        </div>

        @if (session('success'))
            <div class="px-4 py-3 mb-6 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50">
                {{ session('success') }}
            </div>
        @endif

        <div x-data="{
                categoryModalOpen: false,
                categoryForm: { id: '', name: '', description: '' },
                customerModalOpen: false,
                customerMode: 'create',
                customerForm: { id: '', customer_name: '', phone: '', address: '', shipping_cost: 0 },
                normalize(value) {
                    if (value === null || value === undefined || value === 'null') {
                        return '';
                    }
                    return value;
                },
                openCategoryModal(dataset) {
                    this.categoryForm.id = this.normalize(dataset.id);
                    this.categoryForm.name = this.normalize(dataset.name);
                    this.categoryForm.description = this.normalize(dataset.description);
                    this.categoryModalOpen = true;
                },
                closeCategoryModal() {
                    this.categoryModalOpen = false;
                    this.resetCategoryForm();
                },
                resetCategoryForm() {
                    this.categoryForm = { id: '', name: '', description: '' };
                },
                openCustomerModal(mode, dataset = null) {
                    this.customerMode = mode;
                    if (mode === 'edit' && dataset) {
                        this.customerForm = {
                            id: this.normalize(dataset.id),
                            customer_name: this.normalize(dataset.name),
                            phone: this.normalize(dataset.phone),
                            address: this.normalize(dataset.address),
                            shipping_cost: Number(this.normalize(dataset.shippingCost)) || 0,
                        };
                    } else {
                        this.resetCustomerForm();
                    }
                    this.customerModalOpen = true;
                },
                closeCustomerModal() {
                    this.customerModalOpen = false;
                    this.resetCustomerForm();
                },
                resetCustomerForm() {
                    this.customerForm = { id: '', customer_name: '', phone: '', address: '', shipping_cost: 0 };
                }
            }"
            class="space-y-6">

            <!-- Categories Card -->
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Kategori</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-medium text-gray-700">ID</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-700">Nama</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-700">Deskripsi</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($category as $cate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $cate->id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $cate->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $cate->description }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="openCategoryModal($el.dataset)"
                                            data-id="{{ $cate->id }}"
                                            data-name="{{ $cate->name }}"
                                            data-description="{{ $cate->description }}"
                                            class="px-3 py-1 text-white rounded bg-emerald-600 hover:bg-emerald-700">Edit</button>

                                        <form action="{{ route('deletecategory', $cate->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-600">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Pelanggan</h2>
                    <button type="button"
                        @click="openCustomerModal('create')"
                        class="inline-flex items-center p-2 text-sm font-medium text-white rounded bg-sky-600 hover:bg-sky-700">
                        <i class="mr-2 fas fa-user-plus"></i>Tambah Customer
                    </button>
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
                            @forelse($customers ?? collect() as $customer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $customer->customer_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $customer->phone ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $customer->address ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">Rp {{ number_format($customer->shipping_cost ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <button type="button"
                                                @click="openCustomerModal('edit', $el.dataset)"
                                                data-id="{{ $customer->id }}"
                                                data-name="{{ $customer->customer_name }}"
                                                data-phone="{{ $customer->phone }}"
                                                data-address="{{ $customer->address }}"
                                                data-shipping-cost="{{ $customer->shipping_cost }}"
                                                class="px-3 py-1 text-white rounded bg-emerald-600 hover:bg-emerald-700">
                                                Edit
                                            </button>
                                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-sm text-center text-gray-500">Belum ada pelanggan terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Units Card -->
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Satuan</h2>
                    <a href="{{ route('addunit') ?? '#' }}" class="inline-flex items-center p-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700">
                        <i class="mr-2 fas fa-plus"></i>Tambah Satuan
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-medium text-gray-700">ID</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-700">Nama</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-700">Konversi ke dasar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($units ?? collect() as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $u->id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $u->conversion_to_base }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Kategori -->
            <div
                x-show="categoryModalOpen"
                x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                style="display: none;"
                x-cloak>

                <div class="bg-white w-[90%] md:w-[72%] rounded-lg shadow-lg p-6 relative" @click.away="closeCategoryModal()">
                    <!-- Tombol Close -->
                    <button @click="closeCategoryModal()" class="absolute text-xl text-gray-600 top-3 right-3 hover:text-black">&times;</button>

                    <h2 class="mb-4 text-2xl font-semibold">Edit Data Kategori</h2>

                    <form method="POST" :action="'{{ url('/categories') }}/' + categoryForm.id">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="block mb-1 text-sm font-medium">Nama</label>
                            <input type="text" name="name" x-model="categoryForm.name" class="w-full p-2 border border-gray-300 rounded">
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 text-sm font-medium">Deskripsi</label>
                            <textarea name="description" x-model="categoryForm.description" rows="3" class="w-full p-2 border border-gray-300 rounded"></textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 text-white rounded bg-emerald-600">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Customer -->
            <div
                x-show="customerModalOpen"
                x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                style="display: none;"
                x-cloak>

                <div class="bg-white w-[90%] md:w-[72%] rounded-lg shadow-lg p-6 relative" @click.away="closeCustomerModal()">
                    <button @click="closeCustomerModal()" class="absolute text-xl text-gray-600 top-3 right-3 hover:text-black">&times;</button>

                    <h2 class="mb-4 text-2xl font-semibold" x-text="customerMode === 'create' ? 'Tambah Customer' : 'Edit Customer'"></h2>

                    <form method="POST" :action="customerMode === 'create' ? '{{ route('customers.store') }}' : '{{ url('/customers') }}/' + customerForm.id">
                        @csrf
                        <template x-if="customerMode === 'edit'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium">Nama Customer</label>
                                <input type="text" name="customer_name" x-model="customerForm.customer_name" required class="w-full p-2 border border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium">Telepon</label>
                                <input type="text" name="phone" x-model="customerForm.phone" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium">Ongkir Default</label>
                                <div class="relative">
                                    <span class="absolute text-sm font-semibold text-green-600 -translate-y-1/2 left-3 top-1/2">Rp</span>
                                    <input type="number" min="0" name="shipping_cost" x-model.number="customerForm.shipping_cost" class="w-full p-2 pl-8 border border-gray-300 rounded" placeholder="0">
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-1 text-sm font-medium">Alamat</label>
                                <textarea name="address" x-model="customerForm.address" rows="3" class="w-full p-2 border border-gray-300 rounded"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button type="submit" class="px-4 py-2 text-white rounded bg-emerald-600">Simpan</button>
                            <button type="button" @click="closeCustomerModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded">Batal</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
