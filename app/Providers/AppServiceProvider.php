<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Periode;

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
    Gate::define('is-opd', fn ($user) => $user->role === 'opd');
    Gate::define('is-admin', fn ($user) => $user->role === 'admin');
    Gate::define('is-super', fn ($user) => $user->role === 'super');
    // Bagikan tahun aktif ke semua view
    // $tahunAktifGlobal = Periode::where('status', 'open')->value('tahun');
    // View::share('tahunAktifGlobal', $tahunAktifGlobal);
     if (Schema::hasTable('periodes')) {
        $tahunAktifGlobal = Periode::where('status', 'open')->first();
        View::share('tahunAktifGlobal', $tahunAktifGlobal->tahun);
        View::share('kunci', $tahunAktifGlobal->kunci);
    }

    }
}
