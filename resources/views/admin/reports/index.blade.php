@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Statistik')

@section('admin_content')
<p class="text-gray-400 mb-8">Pantau ringkasan pendapatan, statistik transaksi, dan performa kru barber Anda.</p>

{{-- KARTU STATISTIK UTAMA --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl flex items-center gap-5">
        <div class="p-4 bg-green-500/10 text-green-500 rounded-xl">
            <i data-lucide="dollar-sign" class="w-8 h-8"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Total Pendapatan</p>
            <h3 class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl flex items-center gap-5">
        <div class="p-4 bg-amber-500/10 text-amber-500 rounded-xl">
            <i data-lucide="shopping-bag" class="w-8 h-8"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Transaksi Sukses</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $totalTransactions }} Transaksi</h3>
        </div>
    </div>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h4 class="font-bold text-white text-lg flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-5 h-5 text-amber-500"></i> Tren Pendapatan Outlet
            </h4>
            <p class="text-xs text-gray-500 mt-1">Grafik omset pendapatan kotor berdasarkan riwayat transaksi sukses.</p>
        </div>
        <select id="periodFilter" onchange="filterPeriod(this.value)"
                class="bg-gray-950 border border-gray-800 text-gray-400 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500 transition cursor-pointer font-medium">
            <option value="6_months" {{ $period == '6_months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
        </select>
    </div>
    
    <div class="h-72 w-full">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<div class="space-y-8">
    {{-- 1. RIWAYAT PENDAPATAN TERBARU (Membentang Penuh) --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl w-full">
        <h4 class="font-bold text-white text-lg mb-4 flex items-center gap-2">
            <i data-lucide="history" class="w-5 h-5 text-amber-500"></i> 10 Transaksi Selesai Terbaru
        </h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-800 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                        <th class="px-4 pb-3">Pelanggan</th>
                        <th class="px-4 pb-3">Layanan</th>
                        <th class="px-4 pb-3 text-right">Tarif</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                    @forelse($recentEarnings as $booking)
                    <tr class="hover:bg-gray-800/20 transition">
                        <td class="px-4 py-4 font-medium text-white">{{ $booking->user->name }}</td>
                        <td class="px-4 py-4 text-gray-400">
                            {{ $booking->services->pluck('name')->implode(', ') }}
                        </td>
                        <td class="px-4 py-4 text-right text-green-400 font-bold">
                            Rp {{ number_format($booking->services->sum('price'), 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-12 text-center text-gray-600 italic">
                            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-gray-700"></i>
                            Belum ada pemasukan yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. PERFORMA BARBER (Diubah Menjadi Grid Horizontal) --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl w-full">
        <h4 class="font-bold text-white text-lg mb-6 flex items-center gap-2">
            <i data-lucide="award" class="w-5 h-5 text-amber-500"></i> Performa Kru Barber
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($barberPerformance as $barber)
            <div class="p-4 bg-gray-950/40 border border-gray-800/60 rounded-xl flex justify-between items-center hover:border-amber-500/30 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-600/10 border border-amber-600/20 text-amber-500 rounded-xl flex items-center justify-center font-bold text-sm uppercase">
                        {{ substr($barber->name, 0, 2) }}
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-white">{{ $barber->name }}</h5>
                        <p class="text-[11px] text-gray-500 flex items-center gap-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> {{ ucfirst($barber->status) }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold text-amber-500 bg-amber-500/10 px-2.5 py-1 rounded-lg">
                        {{ $barber->bookings_count }} Sesi
                    </span>
                    <p class="text-[10px] text-gray-500 mt-1">Dicukur</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // --- SKRIP GRAFIK REVENUE CHART ---
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Menerima data array dari Controller menggunakan Blade echo JSON
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartData = {!! json_encode($chartData) !!};

    new Chart(ctx, {
        type: 'bar', // Tipe grafik batang (bisa diganti 'line' jika ingin grafik garis)
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: chartData,
                backgroundColor: 'rgba(217, 119, 6, 0.2)', // Warna Amber dengan transparansi
                borderColor: '#d97706', // Warna Border Amber solid (#d97706 = Tailwind amber-600)
                borderWidth: 2,
                borderRadius: 8, // Membuat sudut batang menjadi tumpul/rounded modern
                borderSkipped: false,
                hoverBackgroundColor: '#d97706', // Saat di-hover batang berubah warna solid
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Sembunyikan label kotak atas agar minimalis
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            // Format angka di dalam tooltip agar memunculkan "Rp" dan titik ribuan
                            return ' ' + context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false // Sembunyikan garis grid vertikal
                    },
                    ticks: {
                        color: '#9ca3af', // Warna teks bulan (gray-400)
                        font: { size: 11 }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(31, 41, 55, 0.5)' // Warna garis horizontal samar (gray-800)
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: function(value) {
                            // Format mata uang di sisi kiri grafik
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });

    // --- SKRIP DELETE CONFIRMATION (Data yang sudah ada) ---
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

    // Tambahkan fungsi ini di bagian bawah script Anda
    function filterPeriod(value) {
        // Alihkan halaman dengan membawa parameter ?period= sesuai pilihan dropdown
        window.location.href = "{{ route('admin.reports') }}?period=" + value;
    }
</script>
@endpush