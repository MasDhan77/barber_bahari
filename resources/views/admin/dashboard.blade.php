@extends('layouts.admin')

@section('title', 'Ringkasan Bisnis')

@section('admin_content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-900 p-6 rounded-2xl border border-gray-800">
            <p class="text-gray-400 text-sm">Booking Hari Ini</p>
            <h3 class="text-3xl font-bold text-amber-500">{{ $totalBookingHariIni }}</h3>
        </div>
        </div>

    <div class="bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden">
        <div class="p-6 border-b border-gray-800">
            <h4 class="font-bold">Antrean Terbaru</h4>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-800/50 text-gray-400 text-xs uppercase">
                <tr>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Barber</th>
                    <th class="p-4">Jam</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($recentBookings as $booking)
                <tr class="hover:bg-gray-800/30 transition">
                    <td class="p-4 font-medium">{{ $booking->user->name }}</td>
                    <td class="p-4 text-gray-400">{{ $booking->barber->name }}</td>
                    <td class="p-4">{{ $booking->start_time }}</td>
                    <td class="p-4">
                        @php
                            // Logika penentuan warna berdasarkan status
                            $statusClasses = [
                                'pending'   => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                'confirmed' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                'completed' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                'cancelled' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                'failed'    => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                            ];

                            // Default jika status tidak dikenal
                            $currentClass = $statusClasses[$booking->status] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                        @endphp

                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border {{ $currentClass }}">
                            {{ $booking->status }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center space-x-2">
                            @if($booking->status == 'pending')
                                <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg transition" title="Konfirmasi Kedatangan">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            
                            @if($booking->status == 'confirmed' || $booking->status == 'pending')
                                <form action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition" title="Selesai Cukur">
                                        <i data-lucide="badge-check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif

                            @if($booking->status == 'pending' || $booking->status == 'confirmed')
                                <form id="reject-form-{{ $booking->id }}" action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmReject({{ $booking->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg transition" title="Tolak/Batalkan">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif

                            @if($booking->status == 'confirmed' || $booking->status == 'pending')
                                <form id="noshow-form-{{ $booking->id }}" action="{{ route('admin.bookings.noshow', $booking->id) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmNoShow({{ $booking->id }})" 
                                            class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded-lg transition" title="Pelanggan Tidak Datang">
                                        <i data-lucide="user-x" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
<script>
// Konfirmasi Tolak
function confirmReject(id) {
    Swal.fire({
        title: 'Tolak Booking?',
        text: "Pesanan akan dibatalkan sepenuhnya.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626', // Red-600
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal',
        background: '#111827',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('reject-form-' + id).submit();
        }
    })
}

// Konfirmasi Tidak Datang (No Show)
function confirmNoShow(id) {
    Swal.fire({
        title: 'Pelanggan Tidak Datang?',
        text: "Status akan diubah menjadi Gagal/Expired.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4b5563', // Gray-600
        confirmButtonText: 'Ya, Tandai No-Show',
        cancelButtonText: 'Batal',
        background: '#111827',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('noshow-form-' + id).submit();
        }
    })
}
// Fungsi Logout (SweetAlert2)
function confirmLogout() {
    Swal.fire({
        title: 'Mau keluar?',
        text: "Anda harus login kembali untuk masuk ke dashboard.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal',
        background: '#1f2937',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    })
}
</script>