<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ambil semua setting jika tabelnya sudah ada (menghindari error saat migrasi)
        if (config('database.default')) {
            try {
                $globalSettings = Setting::pluck('value', 'key')->toArray();
                // Bagikan variabel $globalSettings ke SEMUA file blade .blade.php
                View::share('globalSettings', $globalSettings);
            } catch (\Exception $e) {
                // Antisipasi jika tabel belum di-migrate atau kosong
            }
        }
    }
}