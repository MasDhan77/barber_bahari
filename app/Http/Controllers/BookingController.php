<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BarberAbsence; // Import Model Tambahan
use App\Models\BarberSchedule; // Import Model Tambahan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Setting;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        // AMANKAN SISTEM: Cek status operasional toko dari tabel settings
        $shopStatus = Setting::where('key', 'shop_status')->value('value');

        if ($shopStatus === 'Tutup') {
            // Jika tutup, lempar kembali ke dashboard atau halaman utama dengan pesan error
            return redirect()->route('dashboard')->with('error', 'Maaf, saat ini Barber Bahari sedang tutup sementara untuk operasional. Silakan kembali beberapa saat lagi.');
        }

        $services = Service::all();
        
        // 1. Ambil tanggal dari request, default ke hari ini
        $selectedDate = $request->input('booking_date', date('Y-m-d'));
        
        // 2. Deteksi nomor hari berdasarkan kalender Carbon
        $carbonDate = Carbon::parse($selectedDate);
        $dayOfWeek = $carbonDate->dayOfWeek;

        // 3. KODE PERBAIKAN MUTLAK: Menggunakan whereDoesntHave yang benar
        $barbers = Barber::where('status', 'active')
            // Filter A: Kecualikan barber yang memiliki jadwal LIBUR rutin (is_off = 1) di hari tersebut
            ->whereDoesntHave('schedules', function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)
                    ->where('is_off', 1);
            })
            // Filter B: Kecualikan barber yang sedang memiliki catatan IZIN/CUTI di tanggal tersebut
            ->whereDoesntHave('absences', function ($query) use ($selectedDate) {
                $query->where('date', $selectedDate);
            })
            ->get();

        $slots = [
            '09:00', '09:15', '09:30', '09:45', '10:00', '10:15', '10:30', '10:45',
            '11:00', '11:15', '11:30', '13:00', '13:15', '13:30', '13:45',
            '14:00', '14:15', '14:30', '14:45', '15:00', '15:15', '15:30',
            '16:00', '16:15', '16:30', '16:45', '17:00', '17:15', '17:30', '17:45',
            '18:00', '18:15', '18:30', '18:45', '19:00', '19:15', '19:30'
        ];

        // Ambil jadwal tanggal terpilih
        $existingBookings = Booking::whereIn('status', ['pending', 'confirmed'])
                                ->where('booking_date', $selectedDate) 
                                ->get(['barber_id', 'booking_date', 'start_time', 'end_time']);

        return view('bookings.create', compact('barbers', 'services', 'slots', 'existingBookings', 'selectedDate'));
    }

    public function store(Request $request)
    {
        $shopStatus = Setting::where('key', 'shop_status')->value('value');
        if ($shopStatus === 'Tutup') {
            return redirect()->back()->with('error', 'Transaksi gagal. Barber Bahari sedang tutup operasional.');
        }
        // 1. Validasi input dasar
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
        ]);

        $date = $request->booking_date;
        $barberId = $request->barber_id;
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek;

        // 2. VALIDASI PENGAMAN BACKEND A: Cek apakah Barber Libur Rutin
        $isRoutineOff = BarberSchedule::where('barber_id', $barberId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_off', 1)
            ->exists();

        if ($isRoutineOff) {
            return redirect()->back()->withInput()->with('error', 'Maaf, barber yang Anda pilih sedang libur rutin pada hari tersebut.');
        }

        // 3. VALIDASI PENGAMAN BACKEND B: Cek apakah Barber Sedang Cuti/Izin
        $isAbsent = BarberAbsence::where('barber_id', $barberId)
            ->where('date', $date)
            ->exists();

        if ($isAbsent) {
            return redirect()->back()->withInput()->with('error', 'Maaf, barber yang Anda pilih sedang tidak masuk/izin cuti pada tanggal tersebut.');
        }

        // 4. Hitung rencana jam mulai dan jam selesai (Data Asli Anda)
        $service = Service::find($request->service_id);
        $newStart = Carbon::parse($request->start_time);
        $newEnd = $newStart->copy()->addMinutes($service->duration_minutes);

        // 5. CEK OVERLAP (Data Asli Anda)
        $isOverlapping = Booking::where('barber_id', $barberId)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->where(function ($q) use ($newStart, $newEnd) {
                    $q->where('start_time', '<', $newEnd->format('H:i'))
                      ->where('end_time', '>', $newStart->format('H:i'));
                });
            })
            ->exists();

        if ($isOverlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Barber sedang melayani pelanggan lain pada rentang waktu tersebut. Silakan pilih jam lain.');
        }

        // 6. Jika semua lolos validasi, simpan data ke database
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'barber_id' => $barberId,
            'booking_code' => 'BBR-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'booking_date' => $date,
            'start_time' => $newStart->format('H:i'),
            'end_time' => $newEnd->format('H:i'),
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        $booking->services()->attach($request->service_id);

        return redirect()->route('dashboard')->with('success', 'Booking berhasil dibuat!');
    }

    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

        $bookingDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
        
        if (now()->greaterThan($bookingDateTime)) {
            return redirect()->back()->with('error', 'Booking sudah kadaluarsa dan tidak dapat dibatalkan.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Booking ini sudah diproses atau dibatalkan.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('dashboard')->with('success', 'Booking berhasil dibatalkan.');
    }
}