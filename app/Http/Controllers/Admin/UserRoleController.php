<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with(['roles:id,name', 'opd:id,namaopd'])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $user->load(['roles:id,name', 'opd:id,namaopd']);
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $roleIds = $validated['roles'] ?? [];
        $roleNames = $roleIds
            ? Role::query()->whereIn('id', $roleIds)->pluck('name')->all()
            : [];

        $user->syncRoles($roleNames);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Role pengguna {$user->name} berhasil diperbarui.");
    }
}
