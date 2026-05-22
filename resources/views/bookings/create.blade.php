@extends('layouts.app')

@section('content')
<section class="py-12 px-6">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-bold text-amber-500 mb-6 text-center">Formulir Booking</h2>

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500 text-red-400 rounded-xl flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        
        <form action="{{ route('booking.store') }}" method="POST" class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-xl">
            @csrf

            <div class="mb-8">
                <label class="block text-gray-400 text-sm font-semibold mb-3">PILIH BARBER</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($barbers as $barber)
                    <label class="cursor-pointer">
                        <input type="radio" name="barber_id" value="{{ $barber->id }}" class="peer hidden" required>
                        <div class="peer-checked:border-amber-500 peer-checked:bg-amber-500/10 border border-gray-700 p-4 rounded-xl text-center transition">
                            <p class="font-bold text-white">{{ $barber->name }}</p>
                            <p class="text-xs text-gray-500 italic">Professional</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
                <h4 class="text-blue-400 text-xs font-bold mb-2 uppercase tracking-widest">
                    Jadwal Terisi Tanggal <span id="display-date">{{ request('booking_date', date('Y-m-d')) }}</span>:
                </h4>
                <div id="booked-slots" class="flex flex-wrap gap-2 text-sm">
                    @forelse($existingBookings as $eb)
                        <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-md border border-gray-600">
                            Barber: {{ $eb->barber->name ?? $eb->barber_id }} | {{ $eb->start_time }} - {{ $eb->end_time }}
                        </span>
                    @empty
                        <span class="text-gray-500 italic text-xs">Semua jam masih tersedia.</span>
                    @endforelse
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-gray-400 text-sm font-semibold mb-3">PILIH LAYANAN</label>
                <select name="service_id" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 outline-none transition" required>
                    <option value="" disabled selected>Pilih Layanan...</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">
                        {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-gray-400 text-sm font-semibold mb-3">TANGGAL</label>
                    <input type="date" name="booking_date" id="booking_date" min="{{ date('Y-m-d') }}" value="{{ $selectedDate }}" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm font-semibold mb-3 uppercase">Pilih Jam Tersedia</label>
                    <input type="hidden" name="start_time" id="selected_time" required>

                    <div id="time-slots-container" class="grid grid-cols-4 gap-2">
                        @foreach($slots as $slot)
                            <button type="button" 
                                    data-time="{{ $slot }}"
                                    onclick="selectTime(this, '{{ $slot }}')"
                                    class="slot-btn py-2 px-1 text-xs font-semibold rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-amber-500 transition-all">
                                {{ $slot }}
                            </button>
                        @endforeach
                    </div>
                    <p id="error-msg" class="text-red-500 text-[10px] mt-2 hidden italic font-bold uppercase tracking-wider">Jam tidak tersedia atau sudah lewat.</p>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-gray-400 text-sm font-semibold mb-3">CATATAN (OPSIONAL)</label>
                <textarea name="notes" placeholder="Contoh: Tolong buat potongan agak tipis di samping." class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 outline-none transition h-24"></textarea>
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-4 rounded-xl transition transform hover:scale-[1.02] shadow-lg shadow-amber-600/20">
                KONFIRMASI BOOKING
            </button>
        </form>
    </div>
</section>

<script>
    const existingBookings = @json($existingBookings);

    function checkAvailability() {
        const selectedBarberRadio = document.querySelector('input[name="barber_id"]:checked');
        const selectedBarber = selectedBarberRadio ? selectedBarberRadio.value : null;
        const selectedDate = document.getElementById('booking_date').value;
        const buttons = document.querySelectorAll('.slot-btn');
        
        // Ambil waktu sekarang untuk perbandingan
        const now = new Date();
        const todayStr = now.toISOString().split('T')[0]; // Format YYYY-MM-DD

        buttons.forEach(btn => {
            const time = btn.getAttribute('data-time');
            
            // Logika 1: Cek apakah jam sudah lewat (khusus untuk tanggal hari ini)
            let isPast = false;
            if (selectedDate === todayStr) {
                const [hours, minutes] = time.split(':');
                const slotTime = new Date();
                slotTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                
                if (slotTime < now) {
                    isPast = true;
                }
            }

            // Logika 2: Cek Overlap dengan booking yang sudah ada
            const isBooked = existingBookings.some(b => {
                const startTime = b.start_time.substring(0, 5);
                const endTime = b.end_time.substring(0, 5);
                return b.barber_id == selectedBarber && 
                       b.booking_date == selectedDate &&
                       (time >= startTime && time < endTime); 
            });

            // Terapkan Status Tombol
            if (isPast || isBooked) {
                btn.disabled = true;
                btn.classList.add('bg-gray-700/50', 'text-gray-600', 'border-gray-800', 'cursor-not-allowed', 'opacity-50');
                btn.classList.remove('bg-gray-800', 'text-gray-300', 'hover:border-amber-500', 'bg-amber-600', 'text-white');
                
                // Beri warna merah tipis jika sudah di-booking orang
                if(isBooked && !isPast) {
                    btn.classList.add('bg-red-500/10', 'text-red-500/50', 'border-red-500/20');
                }
            } else {
                btn.disabled = false;
                btn.classList.remove('bg-gray-700/50', 'text-gray-600', 'border-gray-800', 'cursor-not-allowed', 'opacity-50', 'bg-red-500/10', 'text-red-500/50', 'border-red-500/20');
                btn.classList.add('bg-gray-800', 'text-gray-300');
            }
        });
    }

    function selectTime(element, time) {
        document.getElementById('selected_time').value = time;
        
        // Reset semua tombol yang aktif
        document.querySelectorAll('.slot-btn').forEach(btn => {
            if(!btn.disabled) {
                btn.classList.remove('bg-amber-600', 'text-white', 'border-amber-500');
                btn.classList.add('bg-gray-800', 'text-gray-300', 'border-gray-700');
            }
        });

        // Highlight tombol terpilih
        element.classList.remove('bg-gray-800', 'text-gray-300');
        element.classList.add('bg-amber-600', 'text-white', 'border-amber-500');
    }

    // Event Listeners
    document.querySelectorAll('input[name="barber_id"]').forEach(radio => {
        radio.addEventListener('change', checkAvailability);
    });

    // Cari event listener booking_date yang sudah ada, lalu ganti isinya:
    document.getElementById('booking_date').addEventListener('change', function() {
        const selectedDate = this.value;
        
        // Redirect ke halaman yang sama dengan parameter tanggal baru
        // agar Controller bisa memfilter $existingBookings dengan benar
        window.location.href = "{{ route('booking.create') }}?booking_date=" + selectedDate;
    });

    // Inisialisasi saat halaman dimuat
    window.onload = checkAvailability;
</script>
@endsection