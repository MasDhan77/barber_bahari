@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('admin_content')

{{-- Notifikasi Sukses --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Notifikasi Gagal/Error --}}
@if(session('error'))
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="mb-8">
    <p class="text-gray-400">Pantau dan kelola seluruh hak akses akun pengguna (Aktor) yang terdaftar di sistem Barber Bahari.</p>
</div>

{{-- TABEL DATA USER --}}
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-800 bg-gray-950/50 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Kontak / Email</th>
                    <th class="px-6 py-4">Hak Akses (Role)</th>
                    <th class="px-6 py-4">Tanggal Bergabung</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                @forelse($users as $user)
                <tr class="hover:bg-gray-800/20 transition">
                    {{-- Nama --}}
                    <td class="px-6 py-4 font-bold text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 font-bold uppercase text-xs">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <div>
                                <h5 class="font-bold text-white">{{ $user->name }}</h5>
                                <span class="text-xs text-gray-500">ID: #{{ $user->id }}</span>
                            </div>
                        </div>
                    </td>
                    
                    {{-- Kontak --}}
                    <td class="px-6 py-4">
                        <p class="text-white font-medium">{{ $user->email }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $user->phone_number ?? '-' }}</p>
                    </td>
                    
                    {{-- Badge Role --}}
                    <td class="px-6 py-4">
                        @if($user->role === 'superadmin')
                            <span class="px-2.5 py-1 bg-red-500/10 text-red-500 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Super Admin
                            </span>
                        @elseif($user->role === 'admin')
                            <span class="px-2.5 py-1 bg-amber-500/10 text-amber-500 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Admin / Barber
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-blue-500/10 text-blue-500 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Customer
                            </span>
                        @endif
                    </td>
                    
                    {{-- Tanggal Join --}}
                    <td class="px-6 py-4 text-gray-400">
                        {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                    </td>
                    
                    {{-- Tombol Aksi --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center space-x-2">
                            <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf @method('PUT')
                                <select name="role" onchange="this.form.submit()" 
                                        class="bg-gray-950 border border-gray-800 text-gray-400 text-xs rounded-xl px-2.5 py-1.5 outline-none focus:border-amber-500 transition cursor-pointer">
                                    <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Set Customer</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Set Admin/Barber</option>
                                    <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Set Superadmin</option>
                                </select>
                            </form>

                            <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" 
                                        class="text-gray-400 hover:text-red-500 p-2 hover:bg-gray-800 rounded-xl transition" title="Hapus Pengguna">
                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-700"></i>
                        Belum ada data pengguna terdaftar selain Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            text: `Akun "${name}" beserta seluruh data riwayat booking miliknya akan dihapus permanen dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Hapus Akun!',
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