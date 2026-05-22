@extends('layouts.admin')

@section('title', 'Ulasan & Rating Pelanggan')

@section('admin_content')
<p class="text-gray-400 mb-8">Berikut adalah daftar testimoni, kritik, dan rating bintang yang diberikan oleh pelanggan outlet Anda.</p>

<div class="grid grid-cols-1 gap-4">
    @forelse($reviews as $review)
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl flex flex-col md:flex-row justify-between gap-4">
        <div class="flex gap-4">
            <div class="w-12 h-12 bg-amber-600/10 border border-amber-600/20 text-amber-500 rounded-2xl flex items-center justify-center font-bold uppercase shrink-0">
                {{ substr($review->user->name, 0, 2) }}
            </div>
            
            <div>
                <div class="flex items-center gap-3">
                    <h5 class="font-bold text-white text-base">{{ $review->user->name }}</h5>
                    <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                
                <div class="flex items-center gap-0.5 mt-1.5 text-amber-500">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $review->rating)
                            <i data-lucide="star" class="w-4 h-4 fill-amber-500"></i>
                        @else
                            <i data-lucide="star" class="w-4 h-4 text-gray-700"></i>
                        @endif
                    @endfor
                </div>

                <p class="text-gray-300 text-sm mt-3 italic bg-gray-950/40 p-3 border border-gray-800/50 rounded-xl">
                    "{{ $review->comment ?? 'Pelanggan tidak menulis komentar, hanya memberikan rating.' }}"
                </p>
            </div>
        </div>

        <div class="text-left md:text-right shrink-0 border-t md:border-t-0 border-gray-800 pt-3 md:pt-0 flex flex-col justify-between">
            <div>
                <span class="text-[11px] text-gray-500 uppercase tracking-wider block">Barber</span>
                <p class="text-sm font-semibold text-white">{{ $review->booking->barber->name ?? 'Barber Terhapus' }}</p>
            </div>
            <div class="mt-2 md:mt-0">
                <span class="text-[11px] text-gray-500 uppercase tracking-wider block">Jasa Jangkauan</span>
                <p class="text-xs bg-gray-800 text-gray-400 px-2 py-1 rounded-lg inline-block font-medium mt-1">
                    {{ $review->booking->services->pluck('name')->implode(', ') }}
                </p>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-12 text-center text-gray-500 shadow-xl">
        <i data-lucide="message-square-dashed" class="w-12 h-12 mx-auto mb-3 text-gray-700"></i>
        Belum ada ulasan atau rating yang masuk dari pelanggan.
    </div>
    @endforelse
</div>
@endsection