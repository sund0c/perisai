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
        \Carbon\Carbon::setLocale('id');
        Gate::define('is-opd', fn($user) => $user->getRoleNames()->first() === 'opd');
        Gate::define('is-admin', fn($user) => $user->getRoleNames()->first() === 'admin');
        Gate::define('is-bidang', fn($user) => $user->getRoleNames()->first() === 'bidang');
        if (Schema::hasTable('periodes')) {
            $tahunAktifGlobal = Periode::where('status', 'open')->first();

            if ($tahunAktifGlobal) {
                View::share('tahunAktifGlobal', $tahunAktifGlobal->tahun);
                View::share('kunci', $tahunAktifGlobal->kunci);
            }
        }
    }
}
