<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="fa-solid fa-note-sticky"></i> {{ __('inventory') }}
            </h2>

        </div>
    </x-slot>




<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="mb-4 text-xl font-bold">Aktivitas</h2>

    @if(session('success'))
        <div class="p-3 mb-4 text-green-800 bg-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('activities.clear') }}" method="POST" class="mb-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-3 py-2 text-white bg-red-600 rounded hover:bg-red-700">
            Hapus Semua Aktivitas
        </button>
    </form>

    <table class="w-full border border-gray-300 rounded-lg">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-3 py-2">User</th>
                <th class="px-3 py-2 ">aksi</th>
                <th class="px-3 py-2">Model</th>
                <th class="px-3 py-2">ID</th>
                <th class="px-3 py-2">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $a)
            <tr class="text-center border-t">
                <td class="px-3 py-2">{{ $a->user }}</td>
                <td class="px-3 py-2
                        @if(Str::contains(strtolower($a->action), 'menambah')) text-green-500
                        @elseif(Str::contains(strtolower($a->action), 'menghapus')) text-red-500
                        @elseif(Str::contains(strtolower($a->action), 'mengedit')) text-yellow-500
                        @elseif(Str::contains(strtolower($a->action), 'memperbarui inventori')) text-blue-500
                        @else bg-gray-500
                        @endif">{{ $a->action }}</td>
                <td class="px-3 py-2">{{ $a->model }}</td>
                <td class="px-3 py-2">{{ $a->record_id }}</td>
                <td class="px-3 py-2">{{ $a->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-4 text-center">Belum ada aktivitas</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>
</x-app-layout>
