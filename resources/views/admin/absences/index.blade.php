@extends('layouts.admin')

@section('title', 'Izin & Cuti Barber')

@section('admin_content')

{{-- Notifikasi Sukses --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Notifikasi Gagal --}}
@if(session('error'))
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif

<p class="text-gray-400 mb-8">Catat riwayat ketidakhadiran kondisional barber (seperti sakit, cuti, atau urusan keluarga) pada tanggal tertentu agar sistem otomatis memblokir pesanan.</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- KOLOM SISI KIRI: FORM INPUT IZIN --}}
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl h-fit">
        <h4 class="font-bold text-white text-base mb-4 flex items-center gap-2">
            <i data-lucide="calendar-plus" class="w-5 h-5 text-amber-500"></i> Input Izin / Cuti Baru
        </h4>
        
        <form action="{{ route('admin.absences.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Pilih Barber</label>
                <select name="barber_id" required class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition cursor-pointer">
                    <option value="" disabled selected>-- Pilih Personel --</option>
                    @foreach($barbers as $barber)
                        <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Tanggal Tidak Masuk</label>
                <input type="date" name="date" required min="{{ date('Y-m-d') }}" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Alasan Izin</label>
                <input type="text" name="reason" required placeholder="Contoh: Sakit Demam, Cuti Tahunan" class="w-full bg-gray-950 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition">
            </div>

            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-amber-600/10">
                Simpan Data Absen
            </button>
        </form>
    </div>

    {{-- KOLOM SISI KANAN: TABEL RIWAYAT ABSEN --}}
    <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-800 bg-gray-950/50 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4">Nama Barber</th>
                        <th class="px-6 py-4">Tanggal Absen</th>
                        <th class="px-6 py-4">Alasan / Keterangan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                    @forelse($absences as $absence)
                    <tr class="hover:bg-gray-800/20 transition">
                        <td class="px-6 py-4 font-bold text-white">{{ $absence->barber->name }}</td>
                        <td class="px-6 py-4 text-amber-500 font-mono">{{ \Carbon\Carbon::parse($absence->date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-gray-400 italic">"{{ $absence->reason }}"</td>
                        <td class="px-6 py-4 text-right">
                            <form id="delete-form-{{ $absence->id }}" action="{{ route('admin.absences.delete', $absence->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $absence->id }})" class="text-gray-500 hover:text-red-500 p-2 hover:bg-gray-800 rounded-xl transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="calendar-check" class="w-12 h-12 mx-auto mb-3 text-gray-700"></i>
                            Seluruh kru barber hadir penuh. Belum ada data izin cuti.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Batalkan Izin Barber?',
            text: "Data absen akan dihapus, barber dianggap masuk kembali pada tanggal tersebut.",
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