<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // 1. Menyimpan ulasan dari Customer
    public function store(Request $request, $booking_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Pastikan booking benar-owned oleh user dan statusnya memang completed
        $booking = Booking::where('id', $booking_id)
                          ->where('user_id', Auth::id())
                          ->where('status', 'completed')
                          ->firstOrFail();

        // Cek apakah sudah pernah diberi ulasan sebelumnya (mencegah duplikat)
        if ($booking->review) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk layanan ini!');
        }

        Review::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'barber_id'  => $booking->barber_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    // 2. Menampilkan semua ulasan di panel Super Admin
    public function adminIndex()
    {
        $reviews = Review::with(['user', 'booking.services', 'booking.barber'])->latest()->get();
        return view('admin.reviews.index', compact('reviews'));
    }
}