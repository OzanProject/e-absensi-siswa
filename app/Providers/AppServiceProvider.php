<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        try {
            // Share Settings Globally to all views
            // Menggunakan try-catch agar tidak error saat running migration awal
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $globalSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
                
                // Helper untuk URL Logo
                $logoPath = $globalSettings['school_logo'] ?? null;
                // FIX: Gunakan Storage::disk('public')->exists() yang lebih aman di hosting daripada public_path()
                // karena public_path() bergantung pada symlink yang mungkin tidak terdeteksi oleh PHP file_exists
                $hasLogo = $logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath);
                
                $globalSettings['logo_url'] = $hasLogo ? asset('storage/' . $logoPath) : null;

                \Illuminate\Support\Facades\View::share('globalSettings', $globalSettings);
            }
        } catch (\Exception $e) {
            // Do nothing during migration/setup if table doesn't exist
        }
    }
}
