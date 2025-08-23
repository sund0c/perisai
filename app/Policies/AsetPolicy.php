<?php

namespace App\Policies;

use App\Models\Aset;
use App\Models\User;

class AsetPolicy
{
    // daftar OPD bisa melihat index aset miliknya
    public function viewAny(User $user): bool
    {
        // Jika pakai spatie:
        // return $user->hasRole('opd') || $user->hasAnyPermission(['aset.view','aset.index']);
        return (bool) $user->opd_id; // sederhana: user OPD
    }

    public function view(User $user, Aset $aset): bool
    {
        return $user->opd_id === $aset->opd_id;
    }

    public function create(User $user): bool
    {
        // Sesuaikan dgn permission Anda
        // return $user->can('aset.create');
        return (bool) $user->opd_id;
    }

    public function update(User $user, Aset $aset): bool
    {
        return $user->opd_id === $aset->opd_id;
    }

    public function delete(User $user, Aset $aset): bool
    {
        return $user->opd_id === $aset->opd_id;
    }
}
