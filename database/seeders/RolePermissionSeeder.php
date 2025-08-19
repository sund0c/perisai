<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'dashboard.view',

            'sk.manage',
            'opd.manage',

            'aset.klasifikasi.manage',
            'aset.subklasifikasi.manage',
            'aset.range.manage',

            'se.range.manage',
            'periode.manage',
            'se.kategori.indikator.manage',

            'bidang.aset.manage',
            'bidang.se.kategori.manage',
            'bidang.ptkka.manage',

            'ptkka.export.pdf',

            'opd.ptkka.manage',
            'opd.aset.manage',
            'opd.se.kategori.manage'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $bidang = Role::firstOrCreate(['name' => 'bidang']);
        $opd = Role::firstOrCreate(['name' => 'opd']);

        $admin->givePermissionTo([
            'dashboard.view',

            'sk.manage',
            'opd.manage',

            'aset.klasifikasi.manage',
            'aset.subklasifikasi.manage',
            'aset.range.manage',

            'se.range.manage',
            'periode.manage',
            'se.kategori.indikator.manage',
        ]);
        $bidang->givePermissionTo([
            'dashboard.view',

            'periode.manage',

            'bidang.aset.manage',
            'bidang.se.kategori.manage',
            'bidang.ptkka.manage',
        ]);
        $opd->givePermissionTo([
            'dashboard.view',

            'ptkka.export.pdf',

            'opd.ptkka.manage',
            'opd.aset.manage',
            'opd.se.kategori.manage'
        ]);

        // Assign role to all user available
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role) { 
                $user->assignRole($user->role);
            }
        }
    }
}
