@extends('layouts.admin')

@section('title', 'Manajemen Layanan & Harga')

@section('admin_content')

{{-- Notifikasi Sukses --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="mb-8 flex justify-between items-center">
    <p class="text-gray-400">Kelola daftar jasa, durasi, dan tarif harga yang ditawarkan outlet Anda.</p>
    <button onclick="openModal('create')" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-amber-600/10">
        <i data-lucide="plus-circle" class="w-5 h-5"></i>
        Tambah Layanan
    </button>
</div>

{{-- TABEL DATA LAYANAN --}}
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-800 bg-gray-950/50 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <th class="px-6 py-4">Nama Jasa</th>
                    <th class="px-6 py-4">Tarif Harga</th>
                    <th class="px-6 py-4">Estimasi Durasi</th>
                    <th class="px-6 py-4">Deskripsi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                @forelse($services as $service)
                <tr class="hover:bg-gray-800/30 transition">
                    <td class="px-6 py-4 font-bold text-white">{{ $service->name }}</td>
                    <td class="px-6 py-4 text-amber-500 font-semibold">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-gray-800 text-gray-400 rounded-lg text-xs font-medium flex items-center w-max gap-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ $service->duration_minutes }} Menit
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $service->description ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <button onclick="openModal('edit', {{ $service->id }}, '{{ $service->name }}', {{ $service->price }}, {{ $service->duration_minutes }}, '{{ $service->description }}')" 
                                    class="text-gray-400 hover:text-amber-500 p-1.5 hover:bg-gray-800 rounded-lg transition">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <form id="delete-form-{{ $service->id }}" action="{{ route('admin.services.delete', $service->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $service->id }})" class="text-gray-400 hover:text-red-500 p-1.5 hover:bg-gray-800 rounded-lg transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="scissors" class="w-12 h-12 mx-auto mb-3 text-gray-700"></i>
                        Belum ada data layanan jasa. Silakan tambahkan baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL POPUP (CREATE / UPDATE) --}}
<div id="serviceModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="bg-gray-900 border border-gray-800 w-full max-w-md p-6 rounded-2xl relative z-10 mx-4 shadow-2xl">
        <h3 id="modalTitle" class="text-xl font-bold text-white mb-4">Tambah Layanan Baru</h3>
        
        <form id="modalForm" action="" method="POST">
            @csrf
            <div id="methodField"></div>

            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Nama Jasa / Layanan</label>
                <input type="text" id="serviceName" name="name" required class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm font-medium mb-2">Tarif (Rp)</label>
                    <input type="number" id="servicePrice" name="price" required class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm font-medium mb-2">Durasi (Menit)</label>
                    <input type="number" id="serviceDuration" name="duration_minutes" required class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-400 text-sm font-medium mb-2">Deskripsi (Opsional)</label>
                <textarea id="serviceDescription" name="description" rows="3" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition resize-none"></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2.5 bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white rounded-xl text-sm transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const modal = document.getElementById('serviceModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalForm = document.getElementById('modalForm');
    const methodField = document.getElementById('methodField');

    function openModal(mode, id = null, name = '', price = '', duration_minutes = '', description = '') {
        modal.classList.remove('hidden');
        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Layanan Baru';
            modalForm.action = "{{ route('admin.services.store') }}";
            document.getElementById('serviceName').value = '';
            document.getElementById('servicePrice').value = '';
            document.getElementById('serviceDuration').value = '';
            document.getElementById('serviceDescription').value = '';
            methodField.innerHTML = '';
        } else {
            modalTitle.innerText = 'Edit Layanan';
            let updateUrl = "{{ route('admin.services.update', ':id') }}";
            modalForm.action = updateUrl.replace(':id', id);
            document.getElementById('serviceName').value = name;
            document.getElementById('servicePrice').value = price;
            document.getElementById('serviceDuration').value = duration_minutes;
            document.getElementById('serviceDescription').value = description;
            methodField.innerHTML = '@method("PUT")';
        }
    }

    function closeModal() { modal.classList.add('hidden'); }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Layanan?',
            text: "Layanan ini tidak akan tersedia lagi untuk booking.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#111827', color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('delete-form-' + id).submit(); }
        })
    }
</script>
@endpush