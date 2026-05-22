<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Barber Bahari</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-950 text-gray-100 font-sans">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col fixed h-full">
            <div class="p-6">
                <span class="text-xl font-bold tracking-widest text-amber-500"  >{{ $globalSettings['app_name'] ?? 'Barber Bahari' }}</span>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-tighter">Admin Control Panel</p>
            </div>

            <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-3 p-3 rounded-xl transition {{ request()->is('admin/dashboard') ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('admin.bookings') }}" 
                   class="flex items-center space-x-3 p-3 rounded-xl transition {{ request()->is('admin/bookings*') ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                    <i data-lucide="calendar-range" class="w-5 h-5"></i>
                    <span class="font-medium">Kelola Booking</span>
                </a>

                <a href="{{ route('admin.galleries') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.galleries*') ? 'bg-amber-600 text-white font-bold shadow-lg shadow-amber-600/10' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                    <i data-lucide="image" class="w-5 h-5"></i>
                    Katalog Model Rambut
                </a>

                @if(Auth::user()->role === 'superadmin')

                    <a href="{{ route('admin.barbers') }}" 
                       class="flex items-center space-x-3 p-3 rounded-xl transition {{ request()->is('admin/barbers*') ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="font-medium">Data Barber</span>
                    </a>

                    <a href="{{ route('admin.schedules') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.schedules*') ? 'bg-amber-600 text-white font-bold shadow-lg shadow-amber-600/10' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                        <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                        Jadwal Kerja Barber
                    </a>

                    <a href="{{ route('admin.absences') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.absences*') ? 'bg-amber-600 text-white font-bold shadow-lg shadow-amber-600/10' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                        <i data-lucide="user-x" class="w-5 h-5"></i>
                        Izin & Cuti Barber
                    </a>

                    <a href="{{ route('admin.services') }}" 
                       class="flex items-center space-x-3 p-3 rounded-xl transition {{ request()->is('admin/services*') ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                        <i data-lucide="scissors" class="w-5 h-5"></i>
                        <span class="font-medium">Layanan & Harga</span>
                    </a>

                    <a href="{{ route('admin.reports') }}" 
                       class="flex items-center space-x-3 p-3 rounded-xl transition {{ request()->is('admin/reports*') ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                        <i data-lucide="line-chart" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Keuangan</span>
                    </a>

                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.users*') ? 'bg-amber-600 text-white font-bold shadow-lg shadow-amber-600/10' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                        <i data-lucide="user-cog" class="w-5 h-5"></i>
                        Manajemen User
                    </a>

                    <a href="{{ route('admin.reviews') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.reviews*') ? 'bg-amber-600 text-white font-bold shadow-lg shadow-amber-600/10' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                        Ulasan & Rating
                    </a>

                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.settings*') ? 'bg-amber-600 text-white font-bold shadow-lg shadow-amber-600/10' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                        <i data-lucide="sliders" class="w-5 h-5"></i>
                        Pengaturan Sistem
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center space-x-3 mb-4 px-2">
                    <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center font-bold text-gray-900">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="truncate">
                        <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-amber-500 uppercase">{{ Auth::user()->role }}</p>
                    </div>
                </div>
                
                <form id="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="w-full flex items-center space-x-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span class="font-medium">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 ml-64 p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-white">@yield('title')</h2>
                    <p class="text-gray-400 text-sm">Selamat bekerja di panel manajemen.</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <p class="text-xs text-gray-500" id="live-clock"></p>
                </div>
            </header>

            @yield('admin_content')
        </main>
    </div>

    <script>
        // Inisialisasi Icon Lucide
        lucide.createIcons();

        // Jam Digital Realtime
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('live-clock').innerText = timeStr + ' WITA';
        }
        setInterval(updateClock, 1000);
        updateClock();
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
    @stack('scripts')
</body>
</html>