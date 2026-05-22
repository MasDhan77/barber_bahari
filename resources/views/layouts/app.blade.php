<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Bahari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-white font-sans">

    <nav class="sticky top-0 z-50 flex justify-between items-center p-6 border-b border-gray-800 bg-gray-900/95 backdrop-blur-sm">
        <span class="text-2xl font-bold tracking-widest text-amber-500">{{ $globalSettings['app_name'] ?? 'Barber Bahari' }}</span>
        <div class="space-x-6 flex items-center text-white">
            <a href="{{ url('/') }}#services" class="hover:text-amber-500 transition">Layanan</a>
            <a href="{{ url('/') }}#barbers" class="hover:text-amber-500 transition">Barber</a>
            <a href="{{ url('/') }}#katalog" class="text-gray-300 hover:text-amber-500 transition">Katalog</a>
            
            @auth
                <a href="{{ url('/dashboard') }}" class="text-gray-300 hover:text-white {{ request()->is('dashboard') ? 'text-amber-500 font-bold' : '' }}">Dashboard</a>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <button type="button" onclick="confirmLogout()" 
                        class="flex items-center px-4 py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 rounded-xl text-sm font-bold transition duration-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            @else
                <a href="{{ route('login') }}" class="text-gray-300 hover:text-white">Masuk</a>
                <a href="{{ route('register') }}" class="bg-gray-700 px-5 py-2 rounded-lg font-semibold hover:bg-gray-800 transition">Daftar</a>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="p-10 text-center border-t border-gray-800 mt-20">
        <a href="https://wa.me/{{ $globalSettings['admin_phone'] ?? '' }}" target="_blank" class="text-amber-500">
            Hubungi Kami ({{ $globalSettings['admin_phone'] ?? '-' }})
        </a>
        <p class="text-gray-500">&copy; 2026 Barber Bahari.</p>
        <p class="text-gray-400">{{ $globalSettings['app_address'] ?? 'Alamat Belum Diatur' }}</p>
    </footer>

</body>
</html>
<script>
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