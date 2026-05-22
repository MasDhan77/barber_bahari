@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Kerja Barber')

@section('admin_content')

@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<p class="text-gray-400 mb-8">Atur hari operasional kerja, jam masuk, jam pulang, serta hari libur rutin mingguan untuk setiap kru barber Anda.</p>

<div class="space-y-12">
    @foreach($barbers as $barber)
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl">
        {{-- Header Profil Barber --}}
        <div class="flex items-center gap-4 mb-6 border-b border-gray-800 pb-4">
            <div class="w-12 h-12 bg-amber-500/10 border border-amber-500/20 text-amber-500 rounded-xl flex items-center justify-center font-bold text-lg uppercase">
                {{ substr($barber->name, 0, 2) }}
            </div>
            <div>
                <h4 class="font-bold text-white text-lg">{{ $barber->name }}</h4>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Status Akun: 
                    <span class="{{ $barber->status === 'active' ? 'text-green-500' : 'text-red-500' }} font-bold">{{ $barber->status }}</span>
                </p>
            </div>
        </div>

        {{-- Grid Form Hari (Senin - Minggu) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $days = [
                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 
                    4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'
                ];
            @endphp

            @foreach($days as $dayNum => $dayName)
                @php
                    // Cari apakah barber ini sudah punya pengaturan jadwal untuk hari ini di database
                    $currentSched = $barber->schedules->where('day_of_week', $dayNum)->first();
                @endphp
                
                <div class="bg-gray-950/50 border border-gray-800 p-4 rounded-xl flex flex-col justify-between">
                    <form action="{{ route('admin.schedules.update', $barber->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="day_of_week" value="{{ $dayNum }}">
                        
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-amber-500 text-sm">{{ $dayName }}</span>
                            {{-- Dropdown Status Kerja / Libur --}}
                            <select name="is_off" onchange="toggleTimeInputs(this, {{ $barber->id }}, {{ $dayNum }})"
                                    class="bg-gray-900 border border-gray-800 text-gray-400 text-[11px] font-bold rounded-lg px-2 py-1 outline-none focus:border-amber-500 transition cursor-pointer">
                                <option value="0" {{ ($currentSched && !$currentSched->is_off) ? 'selected' : '' }}>Masuk</option>
                                <option value="1" {{ (!$currentSched || $currentSched->is_off) ? 'selected' : '' }}>Libur Routine</option>
                            </select>
                        </div>

                        {{-- Input Jam Masuk & Pulang --}}
                        <div id="time-inputs-{{ $barber->id }}-{{ $dayNum }}" class="space-y-2 {{ (!$currentSched || $currentSched->is_off) ? 'hidden' : '' }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-gray-500">Masuk:</span>
                                <input type="time" name="start_time" value="{{ $currentSched ? \Carbon\Carbon::parse($currentSched->start_time)->format('H:i') : '09:00' }}"
                                       class="bg-gray-900 border border-gray-800 text-white text-xs rounded-lg px-2 py-1 outline-none focus:border-amber-500 transition w-28">
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-gray-500">Pulang:</span>
                                <input type="time" name="end_time" value="{{ $currentSched ? \Carbon\Carbon::parse($currentSched->end_time)->format('H:i') : '18:00' }}"
                                       class="bg-gray-900 border border-gray-800 text-white text-xs rounded-lg px-2 py-1 outline-none focus:border-amber-500 transition w-28">
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-4 py-1.5 bg-gray-800 hover:bg-amber-600 text-gray-400 hover:text-white rounded-lg text-xs font-bold transition duration-200">
                            Simpan Jadwal
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>
    // Fungsi JavaScript dinamis untuk menyembunyikan input jam jika statusnya dipilih 'Libur Routine'
    function toggleTimeInputs(selectElement, barberId, dayNum) {
        const timeInputsBox = document.getElementById(`time-inputs-${barberId}-${dayNum}`);
        if (selectElement.value == "1") {
            timeInputsBox.classList.add('hidden');
        } else {
            timeInputsBox.classList.remove('hidden');
        }
    }
</script>
@endpush