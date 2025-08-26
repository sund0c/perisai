<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Aset;
use App\Policies\AsetPolicy;
use App\Models\KategoriSe;
use App\Policies\KategoriSePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping model ke policy
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Aset::class => AsetPolicy::class,

    ];

    /**
     * Daftarkan policy dan Gate sebelum boot.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 1) DEFINISIKAN GATE UNTUK MENU
        Gate::define('is-opd',    fn($user) => $user->hasRole('opd'));
        Gate::define('is-bidang', fn($user) => $user->hasRole('bidang'));
        Gate::define('is-admin',  fn($user) => $user->hasRole('admin'));

        // 2) (OPSIONAL) BATASI BYPASS ADMIN HANYA UNTUK ABILITY TERTENTU (BUKAN MENU)
        Gate::before(function ($user, $ability) {
            // Jangan bypass untuk gate menu sidebar
            if (in_array($ability, ['is-opd', 'is-bidang', 'is-admin'])) {
                return null;
            }

            // Kalau mau admin bypass untuk policy tertentu, batasi dengan prefix ability:
            // if ($user->hasRole('admin') && str_starts_with($ability, 'aset.')) return true;

            return null; // default: jangan bypass
        });
    }
}
