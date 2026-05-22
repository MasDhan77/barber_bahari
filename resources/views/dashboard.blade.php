@extends('layouts.app')

@section('content')
<div class="py-12 px-6 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                background: '#1f2937',
                color: '#ffffff',
                confirmButtonColor: '#d97706'
            });
        </script>
        @endif

        {{-- TAMBAHKAN INI: Menampilkan Alert Gagal/Tutup jika dialihkan dari Controller --}}
        @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Mohon Maaf!',
                text: "{{ session('error') }}",
                background: '#1f2937', // Menyesuaikan tema dark mode kamu
                color: '#ffffff',
                confirmButtonColor: '#ef4444' // Warna merah untuk error
            });
        </script>
        @endif

        <div class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-xl">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Halo, {{ Auth::user()->name }}!</h2>
                    <p class="text-gray-400">Selamat datang kembali di panel pelanggan Barber Bahari.</p>
                </div>
                

            </div>
            
            <hr class="my-6 border-gray-700">

            <div class="mt-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-amber-500">Riwayat Booking Anda</h3>
                    <a href="{{ route('booking.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold py-2 px-4 rounded-lg transition shadow-lg shadow-amber-600/20">
                        + Booking Baru
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($myBookings as $booking)
                        @php
                            $bookingTime = \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
                            $isExpired = now()->greaterThan($bookingTime);
                        @endphp

                        <div class="bg-gray-900 border border-gray-700 p-6 rounded-2xl relative overflow-hidden group hover:border-amber-500/50 transition duration-300">
                            
                            <div class="absolute top-4 right-4">
                                @if($isExpired && $booking->status == 'pending')
                                    <span class="px-3 py-1 bg-gray-500/10 text-gray-400 text-[10px] font-bold rounded-full border border-gray-500/20 uppercase font-mono">Expired</span>
                                @elseif($booking->status == 'pending')
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[10px] font-bold rounded-full border border-amber-500/20 uppercase font-mono">Pending</span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="px-3 py-1 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-full border border-red-500/20 uppercase font-mono">Cancelled</span>
                                @elseif($booking->status == 'confirmed')
                                    {{-- UBAH INI: Warna Biru untuk Confirmed --}}
                                    <span class="px-3 py-1 bg-blue-500/10 text-blue-500 text-[10px] font-bold rounded-full border border-blue-500/20 uppercase font-mono">Confirmed</span>
                                @elseif($booking->status == 'completed')
                                    {{-- TAMBAHKAN INI: Warna Hijau untuk Selesai --}}
                                    <span class="px-3 py-1 bg-green-500/10 text-green-500 text-[10px] font-bold rounded-full border border-green-500/20 uppercase font-mono">Completed</span>
                                @elseif($booking->status == 'failed')
                                    <span class="px-3 py-1 bg-gray-500/10 text-gray-400 text-[10px] font-bold rounded-full border border-gray-500/20 uppercase font-mono">Failed</span>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h4 class="text-white font-bold text-lg mb-1">{{ $booking->booking_code }}</h4>
                                <p class="text-gray-500 text-sm italic">Barber: <span class="text-gray-300">{{ $booking->barber->name }}</span></p>
                            </div>

                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-sm text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $booking->booking_date }}
                                </div>
                                <div class="flex items-center text-sm text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $booking->start_time }} WITA
                                </div>
                            </div>

                            @if($booking->status == 'pending' && $booking->booking_date == date('Y-m-d') && !$isExpired)
                            <div class="mb-6 p-3 bg-gray-800 rounded-xl border border-gray-700">
                                <p class="text-[10px] text-gray-500 mb-1 uppercase tracking-widest font-bold">Waktu Tunggu:</p>
                                <div class="text-xl font-mono text-amber-500 countdown" data-time="{{ $booking->booking_date }} {{ $booking->start_time }}">
                                    00:00:00
                                </div>
                            </div>
                            @endif

                            {{-- LOGIKA TOMBOL AKSI DI BAWAH KARTU --}}
                            @if($booking->status == 'pending' && !$isExpired)
                                <form id="cancel-form-{{ $booking->id }}" action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                <button type="button" onclick="confirmCancel({{ $booking->id }})" 
                                        class="w-full py-2.5 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 rounded-xl text-xs font-bold transition duration-300">
                                    Batalkan Booking
                                </button>
                            @elseif($isExpired && $booking->status == 'pending')
                                <div class="w-full py-2.5 bg-gray-700/50 text-gray-500 border border-gray-600 rounded-xl text-xs font-bold text-center italic">
                                    Sesi Melewati Jadwal
                                </div>
                            @elseif($booking->status == 'completed')
                                {{-- JIKA STATUS COMPLETED: CEK APAKAH SUDAH BERI ULASAN --}}
                                @if($booking->review)
                                    <div class="w-full py-2.5 bg-green-500/5 text-green-500/60 border border-green-500/10 rounded-xl text-xs font-medium text-center flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        Ulasan Telah Dikirim
                                    </div>
                                @else
                                    <button type="button" onclick="openReviewModal({{ $booking->id }}, '{{ $booking->booking_code }}', '{{ $booking->barber->name }}')"
                                            class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white border border-amber-600/30 rounded-xl text-xs font-bold transition duration-300 flex items-center justify-center gap-1.5 shadow-lg shadow-amber-600/5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        Beri Ulasan & Rating
                                    </button>
                                @endif
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-600 italic">Belum ada riwayat booking.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL POPUP INPUT REVIEW (KODE PERBAIKAN TOTAL) --}}
<div id="reviewModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeReviewModal()"></div>
    <div class="bg-gray-800 border border-gray-700 w-full max-w-md p-6 rounded-2xl relative z-10 mx-4 shadow-2xl">
        <h3 class="text-xl font-bold text-white mb-1">Berikan Ulasan Anda</h3>
        <p class="text-xs text-gray-400 mb-4">Kode Booking: <span id="modalBookingCode" class="font-mono text-amber-500"></span></p>
        
        <form id="reviewForm" action="" method="POST">
            @csrf
            
            {{-- PILIHAN RATING BINTANG (KODE BARU DENGAN TRIGER JAVASCRIPT) --}}
            <div class="mb-5 text-center">
                <label class="block text-gray-400 text-xs uppercase tracking-wider font-bold mb-3">
                    Bagaimana kualitas cukuran <span id="modalBarberName" class="text-gray-200"></span>?
                </label>
                
                <input type="hidden" id="ratingValue" name="rating" value="" required />

                <div class="flex justify-center items-center gap-2">
                    <button type="button" onclick="setRating(1)" class="text-gray-600 star-btn transition duration-150 outline-none" data-star="1">
                        <svg class="w-8 h-8 fill-currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </button>
                    <button type="button" onclick="setRating(2)" class="text-gray-600 star-btn transition duration-150 outline-none" data-star="2">
                        <svg class="w-8 h-8 fill-currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </button>
                    <button type="button" onclick="setRating(3)" class="text-gray-600 star-btn transition duration-150 outline-none" data-star="3">
                        <svg class="w-8 h-8 fill-currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </button>
                    <button type="button" onclick="setRating(4)" class="text-gray-600 star-btn transition duration-150 outline-none" data-star="4">
                        <svg class="w-8 h-8 fill-currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </button>
                    <button type="button" onclick="setRating(5)" class="text-gray-600 star-btn transition duration-150 outline-none" data-star="5">
                        <svg class="w-8 h-8 fill-currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </button>
                </div>
            </div>
            
            {{-- Kolom Pesan / Komentar --}}
            <div class="mb-5">
                <label class="block text-gray-400 text-xs font-medium mb-2">Tulis Testimoni / Catatan (Opsional)</label>
                <textarea name="comment" rows="3" placeholder="Contoh: Potongannya rapi banget, pelayanan ramah!" class="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 transition resize-none"></textarea>
            </div>
            
            {{-- Tombol Submit --}}
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeReviewModal()" class="px-4 py-2.5 bg-gray-700 text-gray-400 hover:bg-gray-600 hover:text-white rounded-xl text-xs font-bold transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition">Kirim Ulasan</button>
            </div>
        </form>
    </div>
</div>

{{-- TARUH KODE INI DI BAWAH MODAL UNTUK MEMAKSA WARNA EMAS MASUK KE SVG --}}
<style>
    /* Saat JavaScript mengubah warna teks button (.star-btn) menjadi emas, 
       KODE INI akan memaksa path di dalam SVG-nya untuk ikut berubah menjadi emas secara mutlak!
    */
    .star-btn[style*="rgb(217, 119, 6)"] svg,
    .star-btn[style*="rgb(217, 119, 6)"] path {
        fill: #d97706 !important;
        color: #d97706 !important;
    }
</style>

<script>

// Fungsi Konfirmasi Batal
function confirmCancel(bookingId) {
    Swal.fire({
        title: 'Batalkan Booking?',
        text: "Jadwal ini akan tersedia kembali untuk orang lain.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Kembali',
        background: '#1f2937',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancel-form-' + bookingId).submit();
        }
    })
}

// Fungsi Real-time Countdown
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const timeAttr = el.getAttribute('data-time');
        const formattedDate = timeAttr.replace(/-/g, "/") + ":00";
        
        const target = new Date(formattedDate).getTime();
        const now = new Date().getTime();
        const distance = target - now;

        if (distance < 0) {
            el.innerHTML = "Waktunya Cukur!";
            el.classList.remove('text-amber-500');
            el.classList.add('text-green-500');
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        el.innerHTML = 
            (hours < 10 ? "0" + hours : hours) + ":" + 
            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
            (seconds < 10 ? "0" + seconds : seconds);
    });
}

// --- JAVASCRIPT FITUR MODAL REVIEW ---
const reviewModal = document.getElementById('reviewModal');
const reviewForm = document.getElementById('reviewForm');
const modalBookingCode = document.getElementById('modalBookingCode');
const modalBarberName = document.getElementById('modalBarberName');

function openReviewModal(bookingId, bookingCode, barberName) {
    reviewModal.classList.remove('hidden');
    modalBookingCode.innerText = bookingCode;
    modalBarberName.innerText = barberName;
    
    // Ganti route action form secara dinamis
    let actionUrl = "{{ route('customer.review.store', ':id') }}";
    reviewForm.action = actionUrl.replace(':id', bookingId);
}

function setRating(rating) {
    // 1. Simpan angka rating ke dalam input hidden agar bisa dibaca Controller saat di-submit
    document.getElementById('ratingValue').value = rating;

    // 2. Ambil semua elemen tombol bintang
    const starButtons = document.querySelectorAll('.star-btn');

    // 3. Loop untuk mewarnai bintang menggunakan Inline Style (Anti Gagal)
    starButtons.forEach(btn => {
        const starNum = parseInt(btn.getAttribute('data-star'));

        if (starNum <= rating) {
            // Jika nomor bintang kurang dari atau sama dengan yang diklik, paksa warna Amber/Emas kustom
            btn.style.color = '#d97706'; // Setara dengan text-amber-600
        } else {
            // Jika lebih besar, kembalikan ke warna abu-abu kusam semula
            btn.style.color = '#4b5563'; // Setara dengan text-gray-500 / text-gray-600
        }
    });
}

// Modifikasi fungsi closeReviewModal bawaan agar warna bintang ikut mereset saat ditutup
function closeReviewModal() {
    reviewModal.classList.add('hidden');
    reviewForm.reset();
    document.getElementById('ratingValue').value = '';
    
    // Kembalikan semua warna bintang ke abu-abu semula saat modal ditutup atau dibatalkan
    document.querySelectorAll('.star-btn').forEach(btn => {
        btn.style.color = '#4b5563';
    });
}

setInterval(updateCountdowns, 1000);
updateCountdowns();
</script>
@endsection