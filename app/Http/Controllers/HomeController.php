<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Barber;
use App\Models\Booking; 
use App\Models\Gallery; // Tetap import model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    // 1. PINDAHKAN KE SINI: Halaman Depan (Landing Page) sekarang membaca katalog foto
    public function index()
    {
        // 1. Ambil data layanan utama
        $services = Service::all();
        
        // 2. PERBAIKAN UTAMA: Ambil barber beserta reviews DAN user yang mengulas secara bersih
        $barbers = Barber::with(['reviews.user']) // Mengambil review sekaligus data user pembuatnya
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();
        
        // 3. Ambil data galeri foto referensi gaya rambut terbaru
        $hairReferences = Gallery::with('barber')->latest()->take(4)->get();

        // 4. Hitung statistik dinamis
        $totalBarbers = Barber::count();
        $totalServices = Service::count();
        $totalSatisfiedCustomers = Booking::where('status', 'completed')->count();

        // 5. Oper semua variabel ke file landing.blade.php
        return view('landing', compact(
            'services', 
            'barbers', 
            'hairReferences', 
            'totalBarbers', 
            'totalServices', 
            'totalSatisfiedCustomers'
        ));
    }

    // 2. Kembalikan fungsi Dashboard ke kode aslimu yang bersih
    public function dashboard()
    {
        $myBookings = Booking::where('user_id', Auth::id())
                             ->orderBy('booking_date', 'desc')
                             ->orderBy('start_time', 'desc')
                             ->get();

        return view('dashboard', compact('myBookings'));
    }

    public function allGalleries()
    {
        // Mengambil semua data tanpa batas untuk halaman khusus katalog
        $allReferences = Gallery::with('barber')->latest()->get();
        
        return view('gallery_index', compact('allReferences'));
    }
}