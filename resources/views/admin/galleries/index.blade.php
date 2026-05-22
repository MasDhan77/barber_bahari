@extends('layouts.admin')

@section('title', 'Katalog Referensi Model Rambut')

@section('admin_content')

@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<p class="text-gray-400 mb-8">Kelola inspirasi tren gaya rambut global di halaman depan sekaligus tentukan barber mana yang menjadi spesialis rekomendasi untuk model tersebut.</p>

{{-- MODIFIKASI: x-data utama tetap di sini --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ openEditModal: false, editAction: '', editTitle: '', editBarber: '', editDesc: '' }">
    
    {{-- FORM INPUT FOTO BARU --}}
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl h-fit">
        <h4 class="font-bold text-white text-base mb-4 flex items-center gap-2">
            <i data-lucide="image-plus" class="w-5 h-5 text-amber-500"></i> Tambah Model Rambut
        </h4>
        <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase">Nama Model / Gaya Rambut</label>
                <input type="text" name="title" required placeholder="Contoh: Comma Hair, French Crop" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase">Barber Spesialis (Rekomendasi)</label>
                <select name="barber_id" required class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition cursor-pointer">
                    <option value="" disabled selected>-- Pilih Barber Spesialis --</option>
                    @foreach($barbers as $barber)
                        <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase">File Foto Model Rambut</label>
                <input type="file" name="image" required class="w-full bg-gray-950 border border-gray-800 text-gray-400 text-sm rounded-xl px-4 py-2.5 outline-none focus:border-amber-500 transition">
                <span class="text-[10px] text-gray-500 block mt-1">*Format: JPG, PNG, JPEG. Maksimal 2MB.</span>
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase">Keterangan / Karakteristik</label>
                <input type="text" name="description" placeholder="Contoh: Tipe wajah oval" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
            </div>

            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-amber-600/10">
                Publish ke Katalog
            </button>
        </form>
    </div>

    {{-- DAFTAR GRID FOTO --}}
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
        @forelse($galleries as $item)
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-xl group flex flex-col justify-between">
            <div class="relative overflow-hidden h-48 bg-gray-950">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->type }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <span class="absolute top-3 left-3 bg-gray-950/80 backdrop-blur-md text-amber-500 border border-amber-500/20 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                    ⭐ Spesialis: {{ $item->barber->name }}
                </span>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                    <h5 class="font-bold text-white text-base mb-1">{{ $item->type }}</h5>
                    <p class="text-xs text-gray-400 italic mb-4">"{{ $item->caption ?? 'Tidak ada keterangan tambahan.' }}"</p>
                </div>
                <div class="flex justify-between items-center border-t border-gray-800/60 pt-3">
                    
                    {{-- PERBAIKAN 1: Menggunakan @click.stop agar event klik tidak bocor keluar ke fungsi click.away --}}
                    <button type="button" 
                            @click.stop="openEditModal = true; editAction = '{{ route('admin.galleries.update', $item->id) }}'; editTitle = '{{ $item->type }}'; editBarber = '{{ $item->barber_id }}'; editDesc = '{{ $item->caption }}'"
                            class="text-xs font-bold text-amber-500 hover:bg-amber-500/10 px-3 py-1.5 rounded-lg transition">
                        Edit Data
                    </button>

                    <form action="{{ route('admin.galleries.delete', $item->id) }}" method="POST" onsubmit="return confirm('Hapus model rambut ini dari katalog referensi?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-500 hover:bg-red-500/10 px-3 py-1.5 rounded-lg transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 bg-gray-900 border border-gray-800 p-12 rounded-2xl text-center text-gray-500">
            <i data-lucide="image" class="w-12 h-12 mx-auto mb-3 text-gray-700"></i>
            Belum ada referensi model rambut yang diunggah ke katalog.
        </div>
        @endforelse
    </div>

    {{-- POPUP MODAL EDIT --}}
    {{-- PERBAIKAN 2: Menggunakan x-trap untuk mengunci fokus dan transisi x-show agar Alpine merender lebih stabil --}}
    <div x-show="openEditModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm" 
         style="display: none;"
         x-transition>
        
        {{-- PERBAIKAN 3: Menambahkan modifier .window pada @click.away agar deteksi klik luar bekerja dengan akurat secara global --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" 
             @click.away.window="openEditModal = false">
            
            <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-5 h-5 text-amber-500"></i> Edit Informasi Katalog
                </h3>
                <button type="button" @click="openEditModal = false" class="text-gray-500 hover:text-white text-xl">&times;</button>
            </div>
            
            <form :action="editAction" method="POST" class="space-y-4">
                @csrf @method('PUT')
                
                <div>
                    <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase">Nama Model / Gaya Rambut</label>
                    <input type="text" name="title" x-model="editTitle" required class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase">Barber Spesialis</label>
                    <select name="barber_id" x-model="editBarber" required class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500">
                        @foreach($barbers as $barber)
                            <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase">Keterangan / Karakteristik</label>
                    <input type="text" name="description" x-model="editDesc" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500">
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-800 pt-3">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-amber-600/10">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection