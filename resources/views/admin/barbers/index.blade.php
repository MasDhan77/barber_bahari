@extends('layouts.admin')

@section('title', 'Manajemen Barber')

@section('admin_content')

{{-- Notifikasi Sukses --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Pesan Error Validasi Global (Penting untuk melihat jika submit ditolak Laravel) --}}
@if($errors->any())
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-8 flex justify-between items-center">
    <p class="text-gray-400">Kelola kru barber yang bekerja di outlet Anda.</p>
    <button onclick="openModal('create')" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-amber-600/10">
        <i data-lucide="plus-circle" class="w-5 h-5"></i>
        Tambah Barber
    </button>
</div>

{{-- GRID CARDS (READ) --}}
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @forelse($barbers as $barber)
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden group hover:border-amber-500/50 transition duration-300">
        <div class="aspect-square bg-gray-800 relative overflow-hidden">
            @if($barber->photo)
                <img src="{{ asset('storage/' . $barber->photo) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-700">
                    <i data-lucide="user" class="w-16 h-12"></i>
                </div>
            @endif
        </div>
        <div class="p-5 flex justify-between items-center">
            <div>
                <h5 class="font-bold text-white text-lg truncate w-40">{{ $barber->name }}</h5>
                <p class="text-xs text-gray-500 uppercase tracking-widest mt-0.5">Professional Barber</p>
            </div>
            <div class="flex items-center space-x-2">
                {{-- FIX: Ditambahkan parameter $barber->user_id ke dalam fungsi JS --}}
                <button type="button" 
                        onclick="openModal('edit', {{ $barber->id }}, '{{ $barber->name }}', {{ $barber->user_id }})" 
                        class="text-gray-500 hover:text-amber-500 p-1.5 hover:bg-gray-800 rounded-lg transition" 
                        title="Edit Data">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </button>

                {{-- Tombol Trigger Delete --}}
                <form id="delete-form-{{ $barber->id }}" action="{{ route('admin.barbers.delete', $barber->id) }}" method="POST" class="inline">
                    @csrf 
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete({{ $barber->id }})" class="text-gray-500 hover:text-red-500 p-1.5 hover:bg-gray-800 rounded-lg transition" title="Hapus">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-gray-900 border border-gray-800 rounded-2xl p-12 text-center text-gray-500">
        <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-700"></i>
        Belum ada data kru barber. silakan tambahkan baru.
    </div>
    @endforelse
</div>

{{-- MODAL FORM (CREATE & UPDATE) --}}
<div id="barberModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
    
    <div class="bg-gray-900 border border-gray-800 w-full max-w-md p-6 rounded-2xl relative z-10 mx-4 shadow-2xl">
        <h3 id="modalTitle" class="text-xl font-bold text-white mb-4">Tambah Barber Baru</h3>
        
        <form id="modalForm" action="{{ route('admin.barbers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>

            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Hubungkan dengan Akun User</label>
                <select name="user_id" id="barberUserIdInput" required
                        class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    <option value="">-- Pilih Akun User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Nama Barber</label>
                <input type="text" id="barberNameInput" name="name" required
                       class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
            </div>

            <div class="mb-6">
                <label class="block text-gray-400 text-sm font-medium mb-2">Foto Profil (Opsional)</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full bg-gray-950 border border-gray-800 text-gray-400 text-sm rounded-xl file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-600/10 file:text-amber-500 hover:file:bg-amber-600/20 file:cursor-pointer transition">
                <p class="text-[11px] text-gray-500 mt-1.5">Format berkas: JPG, JPEG, PNG (Maksimal 2MB)</p>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2.5 bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white rounded-xl text-sm font-medium transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-sm transition">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const modal = document.getElementById('barberModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalForm = document.getElementById('modalForm');
    const barberNameInput = document.getElementById('barberNameInput');
    const methodField = document.getElementById('methodField');

    function openModal(mode, id = null, name = '', userId = '') {
        modal.classList.remove('hidden');
        
        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Barber Baru';
            modalForm.action = "{{ route('admin.barbers.store') }}";
            barberNameInput.value = '';
            document.getElementById('barberUserIdInput').value = ''; 
            methodField.innerHTML = '';
        } else if (mode === 'edit') {
            modalTitle.innerText = 'Edit Data Barber';
            let updateUrl = "{{ route('admin.barbers.update', ':id') }}";
            modalForm.action = updateUrl.replace(':id', id);
            barberNameInput.value = name;
            document.getElementById('barberUserIdInput').value = userId; 
            methodField.innerHTML = '@method("PUT")';
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Barber?',
            text: "Data barber dan riwayat kinerjanya akan hilang permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#111827',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush