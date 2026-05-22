@extends('layouts.app')

{{-- Tambahkan CDN AOS di paling atas content --}}
@section('content')
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

{{-- HERO SECTION --}}
<section class="flex flex-col items-center justify-center text-center h-[80vh] bg-cover bg-center" style="background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&q=80&w=2070');">
    <h2 class="text-5xl md:text-7xl font-extrabold mb-4" data-aos="fade-down" data-aos-duration="1000">
        GANTENG MAKSIMAL <br> <span class="text-amber-500">TANPA ANTRI.</span>
    </h2>
    <p class="text-gray-300 text-lg mb-8 max-w-2xl" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
        Nikmati pengalaman cukur rambut premium dengan barber profesional. Booking jadwal Anda secara online sekarang juga.
    </p>
    <a href="/booking" class="bg-amber-600 text-white px-8 py-4 rounded-lg text-xl font-bold hover:scale-105 transition-transform" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="400">
        PESAN JADWAL SEKARANG
    </a>
</section>

{{-- STATISTIC COUNTER SECTION --}}
<section class="grid grid-cols-1 md:grid-cols-3 gap-8 p-20 text-center bg-gray-800 overflow-hidden">
    <div data-aos="zoom-in" data-aos-duration="600">
        <h3 class="text-4xl font-bold text-amber-500">{{ $totalBarbers }}+</h3>
        <p class="text-gray-400">Barber Profesional</p>
    </div>
    <div data-aos="zoom-in" data-aos-duration="600" data-aos-delay="200">
        <h3 class="text-4xl font-bold text-amber-500">{{ $totalServices }}+</h3>
        <p class="text-gray-400">Jenis Layanan</p>
    </div>
    <div data-aos="zoom-in" data-aos-duration="600" data-aos-delay="400">
        <h3 class="text-4xl font-bold text-amber-500">{{ $totalSatisfiedCustomers }}+</h3>
        <p class="text-gray-400">Pelanggan Puas</p>
    </div>
</section>

{{-- SERVICES SECTION --}}
<section id="services" class="py-20 px-10 bg-gray-900 overflow-hidden">
    <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="800">
        <h2 class="text-4xl font-bold text-amber-500 mb-2">LAYANAN KAMI</h2>
        <div class="h-1 w-20 bg-amber-600 mx-auto"></div>
        <p class="text-gray-400 mt-4">Pilih perawatan terbaik untuk penampilan Anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        @forelse($services as $index => $service)
            <div class="bg-gray-800 p-8 rounded-2xl border border-gray-700 hover:border-amber-500 transition-all group"
                 data-aos="fade-up" 
                 data-aos-duration="800" 
                 data-aos-delay="{{ $index * 100 }}">
                <div class="text-amber-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758L6.242 12l2.879-2.879" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-2">{{ $service->name }}</h3>
                <p class="text-gray-400 mb-4 text-sm">{{ $service->description }}</p>
                <div class="flex justify-between items-center mt-6">
                    <span class="text-amber-500 font-bold text-xl">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                    <span class="text-gray-500 text-xs">{{ $service->duration_minutes }} Menit</span>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-10 border-2 border-dashed border-gray-700 rounded-xl">
                <p class="text-gray-500 italic">Belum ada layanan yang tersedia.</p>
            </div>
        @endforelse
    </div>
</section>

{{-- BARBERS SECTION WITH INTEGRATED ALPINE --}}
<section id="barbers" class="py-20 px-10 bg-gray-800 overflow-hidden" x-data="{ openReviewModal: false, activeReviews: [], activeBarberName: '' }">
    <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="800">
        <h2 class="text-4xl font-bold text-amber-500 mb-2">PROFESSIONAL BARBERS</h2>
        <div class="h-1 w-20 bg-amber-600 mx-auto"></div>
        <p class="text-gray-400 mt-4">Temui para ahli di bidang penataan rambut pria</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 max-w-6xl mx-auto">
        @forelse($barbers as $index => $barber)
            <div class="bg-gray-900 rounded-xl overflow-hidden shadow-lg border border-gray-700 group hover:border-amber-500 transition"
                 data-aos="fade-up" 
                 data-aos-duration="800" 
                 data-aos-delay="{{ $index * 150 }}">
                <div class="h-64 overflow-hidden bg-gray-700">
                    <img src="{{ $barber->photo ? asset('storage/'.$barber->photo) : 'https://ui-avatars.com/api/?name='.urlencode($barber->name).'&background=D97706&color=fff&size=512' }}" 
                         alt="{{ $barber->name }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-5 text-center">
                    <h3 class="text-xl font-bold text-white">{{ $barber->name }}</h3>
                    <p class="text-amber-500 text-sm mb-3">Master Barber</p>
                    <div class="flex justify-center items-center text-gray-400 text-xs">
                        <button type="button" 
                                data-reviews="{{ json_encode($barber->reviews) }}"
                                @click="
                                    activeBarberName = '{{ $barber->name }}';
                                    activeReviews = JSON.parse($el.getAttribute('data-reviews')) || [];
                                    openReviewModal = true;
                                "
                                class="flex items-center gap-1 bg-gray-950/60 hover:bg-amber-500/10 hover:text-amber-500 px-3 py-1.5 rounded-lg transition border border-gray-800 cursor-pointer">
                            <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-bold">{{ number_format($barber->reviews_avg_rating ?? 5.0, 1) }}</span> 
                            <span class="underline ml-0.5">({{ $barber->reviews_count }} Ulasan)</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-10">
                <p class="text-gray-500 italic text-sm">Belum ada data barber.</p>
            </div>
        @endforelse
    </div>

    {{-- MODAL REVIEW POPUP --}}
    <div x-show="openReviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak style="display: none;">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl flex flex-col max-h-[70vh]" @click.away="openReviewModal = false">
            <div class="flex items-center justify-between border-b border-gray-800 pb-3 mb-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    ⭐ Ulasan Pelanggan: <span class="text-amber-500" x-text="activeBarberName"></span>
                </h3>
                <button type="button" @click="openReviewModal = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>

            <div class="space-y-3 overflow-y-auto pr-1 flex-1">
                <template x-if="activeReviews && activeReviews.length > 0">
                    <template x-for="(review, index) in activeReviews" :key="index">
                        <div class="bg-gray-950 p-3.5 rounded-xl border border-gray-800 text-left">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-xs font-bold text-amber-500" x-text="review.user && review.user.name ? review.user.name : 'Pelanggan Setia'"></span>
                                <span class="text-[11px] text-yellow-400 font-semibold" x-text="'⭐ ' + review.rating + '/5'"></span>
                            </div>
                            <p class="text-xs text-gray-300 italic" x-text="review.comment && review.comment.trim() !== '' ? review.comment : 'Puas dengan hasil layanan potongan rambut!'"></p>
                        </div>
                    </template>
                </template>

                <template x-if="!activeReviews || activeReviews.length === 0">
                    <div class="text-center py-8 text-gray-500 text-xs italic">
                        Belum ada riwayat ulasan tertulis untuk barber ini.
                    </div>
                </template>
            </div>

            <div class="border-t border-gray-800 pt-3 mt-4 text-right">
                <button type="button" @click="openReviewModal = false" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-semibold rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</section>

{{-- KATALOG REFERENSI GAYA RAMBUT --}}
<div id="katalog" class="mt-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 overflow-hidden">
    <div class="flex items-center justify-between mb-8" data-aos="fade-right" data-aos-duration="800">
        <div>
            <h3 class="text-3xl font-bold text-amber-500 flex items-center gap-3">
                <i data-lucide="scissors" class="w-5 h-5 text-amber-500"></i> Inspirasi Gaya Rambut
            </h3>
            <p class="text-sm text-gray-400 mt-1">Bingung mau potong apa? Pilih model rambut di bawah ini dan temui spesialisnya!</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 items-stretch">
        @forelse($hairReferences as $index => $ref)
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-xl group flex flex-col h-full transition duration-300 hover:border-gray-700"
                 data-aos="zoom-in" 
                 data-aos-duration="600" 
                 data-aos-delay="{{ $index * 100 }}">
                <div class="relative w-full aspect-[3/4] bg-gray-950 overflow-hidden">
                    <img src="{{ asset('storage/' . $ref->image_path) }}" alt="{{ $ref->type }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 object-center">
                    <div class="absolute bottom-3 left-3 right-3 bg-gray-950/85 backdrop-blur-md border border-gray-800/60 px-3 py-2 rounded-xl flex items-center justify-between shadow-lg">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider font-medium">Spesialis:</span>
                        <span class="text-xs font-bold text-amber-500 uppercase tracking-wide">{{ $ref->barber->name }}</span>
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-grow bg-gray-900 border-t border-gray-800/50">
                    <h4 class="font-bold text-white text-base mb-1.5 uppercase tracking-wide border-b border-gray-800/40 pb-1 group-hover:text-amber-500 transition line-clamp-1">
                        {{ $ref->type ?? 'Gaya Rambut' }}
                    </h4>
                    <p class="text-xs text-gray-400 italic line-clamp-2 mt-1">
                        "{{ $ref->caption ?? 'Katalog gaya rambut andalan Barber Bahari.' }}"
                    </p>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-900/50 border border-gray-800 p-12 rounded-2xl text-center text-gray-500 text-sm">
                📌 Belum ada katalog referensi model rambut yang diunggah oleh admin.
            </div>
        @endforelse
    </div>

    <div class="mt-10 text-center" data-aos="fade-up" data-aos-duration="800">
        <a href="{{ route('gallery.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 hover:bg-gray-800 border border-gray-800 hover:border-gray-700 text-white text-sm font-semibold rounded-xl transition shadow-lg group">
            Lihat Model Rambut Selengkapnya
            <i data-lucide="arrow-right" class="w-4 h-4 text-amber-500 group-hover:translate-x-1 transition duration-200"></i>
        </a>
    </div>
</div>

{{-- SCRIPT INITIALIZATION --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Init Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Init AOS Animation
        AOS.init({
            once: true, // Animasi cuma jalan sekali saat di-scroll ke bawah (tidak berulang saat di-scroll ke atas)
            offset: 120 // Animasi baru akan terpicu jika elemen sudah berjarak 120px dari bawah layar
        });
    });
</script>
@endsection