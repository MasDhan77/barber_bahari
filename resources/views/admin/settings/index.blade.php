@extends('layouts.admin')

@section('title', 'Pengaturan Aplikasi (Master Settings)')

@section('admin_content')

@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<p class="text-gray-400 mb-8">Kelola variabel global dan informasi operasional outlet Barber Bahari secara dinamis tanpa perlu mengubah baris kode program.</p>

<div class="max-w-2xl bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl">
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5">
        @csrf
        
        <div>
            <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Nama Aplikasi / Barbershop</label>
            <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'Barber Bahari' }}" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
        </div>

        <div>
            <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Nomor WhatsApp Admin Hotline</label>
            <input type="text" name="admin_phone" value="{{ $settings['admin_phone'] ?? '08123456789' }}" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
        </div>

        <div>
            <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Alamat Lengkap Outlet</label>
            <textarea name="app_address" rows="3" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition resize-none">{{ $settings['app_address'] ?? 'Jl. Kampus UIN Datokarama Palu, Sulawesi Tengah' }}</textarea>
        </div>

        <div>
            <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Status Operasional Global</label>
            <select name="shop_status" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition cursor-pointer">
                <option value="Buka" {{ ($settings['shop_status'] ?? '') == 'Buka' ? 'selected' : '' }}>🟢 Buka (Menerima Pesanan)</option>
                <option value="Tutup" {{ ($settings['shop_status'] ?? '') == 'Tutup' ? 'selected' : '' }}>🔴 Tutup (Menerima Libur/Renovasi)</option>
            </select>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-amber-600/10">
                Simpan Perubahan Konfigurasi
            </button>
        </div>
    </form>
</div>

@endsection