@extends('layouts.admin')

@section('title', 'Kelola Semua Booking')

@section('admin_content')
    <form action="{{ route('admin.bookings') }}" method="GET" class="flex flex-col lg:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Cari nama pelanggan..." 
                class="w-full bg-gray-900 border border-gray-800 text-white text-sm rounded-xl px-11 py-3 focus:ring-amber-500 focus:border-amber-500 transition">
            <div class="absolute left-4 top-3.5 text-gray-500">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
        </div>

        <select name="filter" onchange="this.form.submit()" 
                class="bg-gray-900 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 focus:ring-amber-500 outline-none cursor-pointer hover:bg-gray-800 transition">
            <option value="">Semua Waktu</option>
            <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="last_week" {{ request('filter') == 'last_week' ? 'selected' : '' }}>Minggu Lalu</option>
            <option value="last_month" {{ request('filter') == 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
        </select>

        <select name="status" onchange="this.form.submit()" 
                class="bg-gray-900 border border-gray-800 text-white text-sm rounded-xl px-4 py-3 focus:ring-amber-500 outline-none cursor-pointer hover:bg-gray-800 transition">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
        </select>

        @if(request('search') || request('filter') || request('status'))
            <a href="{{ route('admin.bookings') }}" 
            class="px-5 py-3 bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl text-sm font-medium hover:bg-red-500 hover:text-white transition text-center">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
            <h4 class="font-bold">Database Seluruh Booking</h4>
            <span class="text-xs text-gray-500">Total: {{ $bookings->total() }} Data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-800/50 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Barber</th>
                        <th class="p-4">Tanggal & Jam</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-800/30 transition">
                        <td class="p-4">
                            <div class="font-medium text-white">{{ $booking->user->name }}</div>
                            <div class="text-[10px] text-gray-500 uppercase">{{ $booking->booking_code }}</div>
                        </td>
                        <td class="p-4 text-gray-400 text-sm">{{ $booking->barber->name }}</td>
                        <td class="p-4 text-sm">
                            <div class="text-white">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
                            <div class="text-gray-500 text-xs">{{ $booking->start_time }}</div>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $statusClasses = [
                                    'pending'   => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                    'confirmed' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                    'completed' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                    'cancelled' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                    'failed'    => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                                ];
                                $currentClass = $statusClasses[$booking->status] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border {{ $currentClass }}">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-center space-x-2">
                                {{-- Tombol Aksi (Gunakan Kode yang Sama Seperti Dashboard) --}}
                                @if($booking->status == 'pending')
                                    <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST">
                                        @csrf
                                        <button class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg transition" title="Konfirmasi">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if($booking->status == 'confirmed')
                                    <button type="button" onclick="confirmComplete({{ $booking->id }})" 
                                            class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition" title="Selesai">
                                        <i data-lucide="badge-check" class="w-4 h-4"></i>
                                    </button>

                                    <form id="complete-form-{{ $booking->id }}" action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST" class="hidden">
                                        @csrf
                                    </form>
                                @endif

                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <button type="button" onclick="confirmReject({{ $booking->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg transition">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <button type="button" onclick="confirmNoShow({{ $booking->id }})" 
                                            class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded-lg transition">
                                        <i data-lucide="user-x" class="w-4 h-4"></i>
                                    </button>

                                    {{-- Form Hidden untuk JS --}}
                                    <form id="reject-form-{{ $booking->id }}" action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST" class="hidden">@csrf</form>
                                    <form id="noshow-form-{{ $booking->id }}" action="{{ route('admin.bookings.noshow', $booking->id) }}" method="POST" class="hidden">@csrf</form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-500">Data booking tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-6 border-t border-gray-800 bg-gray-900/50">
            {{ $bookings->appends(['search' => request('search')])->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Inisialisasi ulang icon jika diperlukan
    lucide.createIcons();

    function confirmComplete(id) {
        Swal.fire({
            title: 'Selesaikan Pesanan?',
            text: "Pastikan kustomer sudah selesai dicukur dan melakukan pembayaran.",
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#16a34a', // Warna hijau sukses Tailwind
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal',
            background: '#111827',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('complete-form-' + id).submit();
            }
        })
    }

    function confirmReject(id) {
        Swal.fire({
            title: 'Tolak Booking?',
            text: "Pesanan akan dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            background: '#111827',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('reject-form-' + id).submit();
        })
    }

    function confirmNoShow(id) {
        Swal.fire({
            title: 'Tidak Datang?',
            text: "Status akan diubah menjadi Gagal.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4b5563',
            confirmButtonText: 'Ya, No-Show',
            cancelButtonText: 'Batal',
            background: '#111827',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('noshow-form-' + id).submit();
        })
    }
</script>
@endpush