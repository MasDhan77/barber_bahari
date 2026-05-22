<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Barber;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BarberSchedule; 
use App\Models\BarberAbsence;
use App\Models\Setting;
use App\Models\Gallery;


class AdminController extends Controller
{
    public function index()
    {
        // Statistik Ringkas (Untuk Dashboard)
        $totalBookingHariIni = Booking::whereDate('booking_date', date('Y-m-d'))->count();
        $pendingBooking = Booking::where('status', 'pending')->count();
        
        $recentBookings = Booking::with(['user', 'barber'])
                        ->orderBy('booking_date', 'desc')
                        ->orderBy('start_time', 'desc')
                        ->take(5)
                        ->get();

        return view('admin.dashboard', compact('totalBookingHariIni', 'pendingBooking', 'recentBookings'));
    }

    public function manageBookings(Request $request)
    {
        $query = Booking::with(['user', 'barber', 'services'])
                    ->orderBy('booking_date', 'desc')
                    ->orderBy('start_time', 'desc');

        // Filter Berdasarkan Pencarian Nama
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Berdasarkan Waktu
        if ($request->filled('filter')) {
            if ($request->filter == 'today') {
                $query->whereDate('booking_date', now());
            } elseif ($request->filter == 'last_week') {
                $query->whereBetween('booking_date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            } elseif ($request->filter == 'last_month') {
                $query->whereMonth('booking_date', now()->subMonth()->month)
                    ->whereYear('booking_date', now()->subMonth()->year);
            }
        }

        // NEW: Filter Berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // withQueryString() penting agar saat pindah halaman, filter tidak hilang
        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function manageBarbers()
    {
        $barbers = Barber::all();
        $users = User::where('role', 'admin')->get();
        return view('admin.barbers.index', compact('barbers', 'users'));
    }

    public function storeBarber(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:barbers,user_id',
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        
        $data = $request->only(['user_id', 'name']);;
        $data['status'] = 'active';

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('barbers', 'public');
        }

        Barber::create($data);
        return redirect()->route('admin.barbers')->with('success', 'Barber baru berhasil ditambahkan!');
    }

    public function deleteBarber($id)
    {
        $barber = Barber::findOrFail($id);
        $barber->delete();
        return back()->with('success', 'Data barber telah dihapus.');
    }


    public function confirm($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Booking #' . $booking->booking_code . ' telah dikonfirmasi.');
    }

    public function complete($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'completed']);

        return back()->with('success', 'Booking #' . $booking->booking_code . ' ditandai selesai. Mantap!');
    }

    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking #' . $booking->booking_code . ' telah dibatalkan.');
    }

    public function noShow($id)
    {
        $booking = Booking::findOrFail($id);
        // Kita set status ke 'failed' atau 'expired'
        $booking->update(['status' => 'failed']);

        return back()->with('error', 'Pelanggan #' . $booking->booking_code . ' ditandai tidak datang.');
    }


    public function updateBarber(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $barber = Barber::findOrFail($id);
        $data = $request->only('name');

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada agar penyimpanan server tidak penuh/bengkak
            if ($barber->photo && Storage::disk('public')->exists($barber->photo)) {
                Storage::disk('public')->delete($barber->photo);
            }
            // Simpan foto profil baru
            $data['photo'] = $request->file('photo')->store('barbers', 'public');
        }

        $barber->update($data);
        return back()->with('success', 'Data barber berhasil diperbarui!');
    }


    // 1. Menampilkan Halaman Utama Layanan
    public function manageServices()
    {
        $services = Service::latest()->get();
        return view('admin.services.index', compact('services'));
    }

    // 2. Menyimpan Layanan Baru (Create)
    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        Service::create($request->all());

        return back()->with('success', 'Layanan baru berhasil ditambahkan!');
    }

    // 3. Memperbarui Data Layanan (Update)
    public function updateService(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $service = Service::findOrFail($id);
        $service->update($request->all());

        return back()->with('success', 'Data layanan berhasil diperbarui!');
    }

    // 4. Menghapus Layanan (Delete)
    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return back()->with('success', 'Layanan berhasil dihapus!');
    }

    public function reports(Request $request)
    {
        // Tangkap parameter filter periode dari URL (default-nya jika kosong: '6_months')
        $period = $request->get('period', '6_months');

        // --- BASE QUERY UNTUK TOTAL PENDAPATAN & TRANSAKSI ---
        $revenueQuery = Booking::where('bookings.status', 'completed')
            ->join('booking_services', 'bookings.id', '=', 'booking_services.booking_id')
            ->join('services', 'booking_services.service_id', '=', 'services.id');

        $transactionQuery = Booking::where('status', 'completed');

        // --- LOGIKA FILTER BERDASARKAN PILIHAN DROPDOWN ---
        if ($period === 'weekly') {
            // Filter untuk minggu ini (Senin - Minggu berjalan)
            $revenueQuery->whereBetween('bookings.booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
            $transactionQuery->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'monthly') {
            // Filter untuk bulan ini saja
            $revenueQuery->whereMonth('bookings.booking_date', now()->month)
                        ->whereYear('bookings.booking_date', now()->year);
            $transactionQuery->whereMonth('booking_date', now()->month)
                            ->whereYear('booking_date', now()->year);
        }

        // Eksekusi nilai total utama setelah difilter
        $totalRevenue = $revenueQuery->sum('services.price');
        $totalTransactions = $transactionQuery->count();

        // --- DATA UNTUK GRAFIK (CHART) ---
        $chartQuery = Booking::where('bookings.status', 'completed')
            ->join('booking_services', 'bookings.id', '=', 'booking_services.booking_id')
            ->join('services', 'booking_services.service_id', '=', 'services.id');

        if ($period === 'weekly') {
            // Jika mingguan, grafik menampilkan pendapatan per hari (Senin, Selasa, dst)
            $chartDataRaw = $chartQuery->whereBetween('bookings.booking_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->select(
                    DB::raw("DATE_FORMAT(bookings.booking_date, '%W') as label"), // %W menghasilkan nama hari dalam Inggris
                    DB::raw("SUM(services.price) as total"),
                    'bookings.booking_date'
                )
                ->groupBy('label', 'bookings.booking_date')
                ->orderBy('bookings.booking_date', 'asc')
                ->get();
        } elseif ($period === 'monthly') {
            // Jika bulanan, grafik menampilkan pendapatan per minggu di bulan ini
            $chartDataRaw = $chartQuery->whereMonth('bookings.booking_date', now()->month)
                ->whereYear('bookings.booking_date', now()->year)
                ->select(
                    DB::raw("CONCAT('Minggu ', WEEK(bookings.booking_date) - WEEK(DATE_SUB(bookings.booking_date, INTERVAL DAYOFMONTH(bookings.booking_date)-1 DAY)) + 1) as label"),
                    DB::raw("SUM(services.price) as total")
                )
                ->groupBy('label')
                ->get();
        } else {
            // Default: 6 Bulan Terakhir (KODE PERBAIKAN)
            $chartDataRaw = $chartQuery->select(
                    DB::raw("DATE_FORMAT(bookings.booking_date, '%b %Y') as label"),
                    DB::raw("SUM(services.price) as total"),
                    DB::raw("MIN(bookings.booking_date) as sort_date") // Ambil tanggal terkecil sebagai acuan sorting
                )
                ->groupBy(DB::raw("DATE_FORMAT(bookings.booking_date, '%b %Y')")) // Group BY murni hanya berdasarkan label bulan
                ->orderBy('sort_date', 'asc') // Urutkan berdasarkan urutan kalender yang benar
                ->take(6)
                ->get();
        }

        $chartLabels = $chartDataRaw->pluck('label')->toArray();
        $chartData = $chartDataRaw->pluck('total')->toArray();

        // Pengaman jika data filter kosong agar chart tidak crash
        if (empty($chartLabels)) {
            if ($period === 'weekly') { $chartLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']; $chartData = [0,0,0,0,0,0,0]; }
            elseif ($period === 'monthly') { $chartLabels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4']; $chartData = [0,0,0,0]; }
            else { $chartLabels = ['Mar 2026', 'Apr 2026', 'Mei 2026']; $chartData = [0, 0, $totalRevenue]; }
        }

        // --- DATA LAIN-LAIN (TETAP) ---
        $barberPerformance = Barber::withCount(['bookings' => function($query) use ($period) {
            $query->where('status', 'completed');
            if ($period === 'weekly') { $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]); }
            elseif ($period === 'monthly') { $query->whereMonth('booking_date', now()->month)->whereYear('booking_date', now()->year); }
        }])->get();

        $recentEarnings = Booking::where('bookings.status', 'completed')
            ->with(['user', 'services'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->take(10)->get();
            
        // Kirim variabel 'period' ke view untuk menandai opsi mana yang sedang aktif
        return view('admin.reports.index', compact(
            'totalRevenue', 'totalTransactions', 'barberPerformance', 'recentEarnings', 'chartLabels', 'chartData', 'period'
        ));
    }

    public function manageUsers()
    {
        // Mengambil semua user selain akun yang sedang login saat ini
        // Diurutkan berdasarkan tingkatan role (superadmin -> admin -> customer), lalu berdasarkan data terbaru
        $users = User::where('id', '!=', Auth::id())
            ->orderByRaw("FIELD(role, 'superadmin', 'admin', 'customer') ASC")
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    // 2. Memperbarui Role User (Update)
    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:superadmin,admin,customer',
        ]);

        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role pengguna ' . $user->name . ' berhasil diperbarui!');
    }

    // 3. Menghapus Akun Pengguna (Delete)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi tambahan agar tidak bisa menghapus sesama superadmin via web demi keamanan
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Tidak dapat menghapus sesama akun Superadmin!');
        }

        $user->delete();
        return back()->with('success', 'Akun pengguna berhasil dihapus permanen!');
    }

    // 1. Menampilkan halaman manajemen jadwal semua barber
    public function schedules()
    {
        // Mengambil data semua barber beserta relasi jadwal mingguan mereka
        $barbers = Barber::with('schedules')->get();
        return view('admin.schedules.index', compact('barbers'));
    }

    public function updateSchedule(Request $request, $barber_id)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time'  => 'nullable|date_format:H:i',
            'end_time'    => 'nullable|date_format:H:i',
            'is_off'      => 'required|boolean',
        ]);

        // AMANKAN DATA: Jika libur routine (is_off = 1), paksa isi jam dengan 00:00 
        // agar database yang bertipe NOT NULL tidak melempar error QueryException.
        $startTime = $request->is_off ? '00:00' : $request->start_time;
        $endTime   = $request->is_off ? '00:00' : $request->end_time;

        // Eksekusi simpan atau update ke database
        BarberSchedule::updateOrCreate(
            [
                'barber_id'   => $barber_id,
                'day_of_week' => $request->day_of_week,
            ],
            [
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'is_off'      => $request->is_off,
            ]
        );

        return back()->with('success', 'Jadwal kerja barber berhasil diperbarui!');
    }

    // 1. Menampilkan halaman daftar izin/cuti barber beserta form inputnya
    public function absences()
    {
        $barbers = Barber::where('status', 'active')->get();
        // Ambil data absen, urutkan dari tanggal yang paling baru
        $absences = BarberAbsence::with('barber')->latest()->get();
        
        return view('admin.absences.index', compact('barbers', 'absences'));
    }

    // 2. Menyimpan data izin/cuti baru ke database
    public function storeAbsence(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date'      => 'required|date|after_or_equal:today', // Tidak boleh pilih tanggal yang sudah lewat
            'reason'    => 'required|string|max:255',
        ]);

        // Cek apakah di tanggal tersebut barber yang sama sudah diinput izinnya (mencegah duplikat data)
        $exists = BarberAbsence::where('barber_id', $request->barber_id)
                            ->where('date', $request->date)
                            ->exists();

        if ($exists) {
            return back()->with('error', 'Barber yang bersangkutan sudah memiliki catatan izin pada tanggal tersebut!');
        }

        BarberAbsence::create([
            'barber_id' => $request->barber_id,
            'date'      => $request->date,
            'reason'    => $request->reason,
        ]);

        return back()->with('success', 'Data absensi/izin cuti barber berhasil ditambahkan!');
    }

    // 3. Menghapus data izin cuti (jika barber batal izin dan masuk kembali)
    public function deleteAbsence($id)
    {
        $absence = BarberAbsence::findOrFail($id);
        $absence->delete();
        
        return back()->with('success', 'Data absensi berhasil dihapus!');
    }

    // 1. Menampilkan Halaman Pengaturan Aplikasi
    public function settings()
    {
        // Mengambil semua data setting dan mengubahnya menjadi key-value pair agar mudah dipanggil di Blade
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
    }

    // 2. Menyimpan Perubahan Pengaturan
    public function updateSettings(Request $request)
    {
        // Ambil semua data input kecuali token CSRF
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            // Simpan atau update berdasarkan 'key' nya
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return back()->with('success', 'Konfigurasi sistem Barber Bahari berhasil diperbarui!');
    }

    // 1. Menampilkan Halaman Manajemen Galeri di Panel Admin
    public function galleries()
    {
        $barbers = Barber::where('status', 'active')->get();
        $galleries = Gallery::with('barber')->latest()->get();
        return view('admin.galleries.index', compact('barbers', 'galleries'));
    }

    // 2. Menyimpan Foto Portofolio Baru
    public function storeGallery(Request $request)
    {
        // 1. Validasi Input dari Form
        $request->validate([
            'barber_id'   => 'required|exists:barbers,id',
            'title'       => 'required|string|max:100', // Ini nama input dari form blade
            'image'       => 'required|image|mimes:jpeg,png,jpg|max:2048', 
            'description' => 'nullable|string', // Ini keterangan input dari form blade
        ]);

        // 2. Proses Upload Gambar ke folder storage
        $imagePath = $request->file('image')->store('galleries', 'public');

        // 3. Simpan ke Database dengan Menyesuaikan Nama Kolom phpMyAdmin kamu
        Gallery::create([
            'barber_id'  => $request->barber_id,
            'image_path' => $imagePath,
            'type'       => $request->title,       // PERBAIKAN: Input 'title' dari form disimpan ke kolom 'type'
            'caption'    => $request->description, // PERBAIKAN: Input 'description' dari form disimpan ke kolom 'caption'
        ]);

        return back()->with('success', 'Referensi model rambut berhasil ditambahkan ke katalog!');
    }

    // Menyimpan Perubahan Data Katalog yang Di-edit
    public function updateGallery(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'barber_id'   => 'required|exists:barbers,id',
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        // 2. Cari data galeri berdasarkan ID
        $gallery = \App\Models\Gallery::findOrFail($id);

        // 3. Update data ke database (Menyesuaikan kolom type dan caption kamu)
        $gallery->update([
            'barber_id' => $request->barber_id,
            'type'      => $request->title,       // Teks judul disimpan ke kolom 'type'
            'caption'   => $request->description, // Teks deskripsi disimpan ke kolom 'caption'
        ]);

        return back()->with('success', 'Katalog referensi model rambut berhasil diperbarui!');
    }

    // 3. Menghapus Foto Galeri
    public function deleteGallery($id)
    {
        $gallery = Gallery::findOrFail($id);
        
        // Hapus file fisik gambar dari storage agar tidak memenuhi memori laptop
        Storage::disk('public')->delete($gallery->image_path);
        
        $gallery->delete();
        return back()->with('success', 'Foto galeri berhasil dihapus!');
    }
    
}