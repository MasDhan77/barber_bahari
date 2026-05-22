@extends('layouts.app') {{-- Sesuaikan dengan layout utamamu --}}

@section('content')
<div class="py-12 bg-gray-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-900 pb-6 mb-10">
            <div>
                <a href="{{ url('/') }}" class="text-xs text-amber-500 hover:underline flex items-center gap-1 mb-2">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i> Kembali ke Beranda
                </a>
                <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                    <i data-lucide="scissors" class="w-7 h-7 text-amber-500"></i> Katalog Lengkap Gaya Rambut
                </h1>
                <p class="text-sm text-gray-400 mt-1">Eksplorasi seluruh tren model potongan rambut terbaik dari Barber Bahari.</p>
            </div>
        </div>

        {{-- Grid Menampilkan Semua Foto --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 items-stretch">
            @foreach($allReferences as $ref)
                <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-xl flex flex-col h-full">
                    <div class="relative w-full aspect-[3/4] bg-gray-950 overflow-hidden">
                        <img src="{{ asset('storage/' . $ref->image_path) }}" alt="{{ $ref->type }}" class="w-full h-full object-cover object-center">
                        <div class="absolute bottom-3 left-3 right-3 bg-gray-950/85 backdrop-blur-md border border-gray-800/60 px-3 py-2 rounded-xl flex items-center justify-between">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-medium">Spesialis:</span>
                            <span class="text-xs font-bold text-amber-500 uppercase">{{ $ref->barber->name }}</span>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col flex-grow bg-gray-900 border-t border-gray-800/50">
                        <h4 class="font-bold text-white text-base mb-1.5 uppercase tracking-wide border-b border-gray-800/40 pb-1">
                            {{ $ref->type ?? 'Gaya Rambut' }}
                        </h4>
                        <p class="text-xs text-gray-400 italic line-clamp-2 mt-1">
                            "{{ $ref->caption ?? 'Katalog gaya rambut andalan Barber Bahari.' }}"
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });
</script>