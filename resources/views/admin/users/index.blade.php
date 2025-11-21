@extends('adminlte::page')

@section('title', 'Daftar Pengguna')

@section('content_header')
    <h1>Daftar Pengguna & Role</h1>
@endsection

@section('content')
    @foreach (['success' => 'success', 'error' => 'danger'] as $flashKey => $alertClass)
        @if ($message = session($flashKey))
            <div class="alert alert-{{ $alertClass }}">
                {{ $message }}
            </div>
        @endif
    @endforeach

    <div class="alert alert-light border mb-3">
        <strong>Penanda:</strong>
        <span class="badge bg-info text-uppercase">SSO</span> = role terakhir yang datang dari SSO,
        <span class="badge bg-secondary">Manual</span> = role yang diatur admin melalui aplikasi.
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">Seluruh pengguna terdaftar beserta role yang dimiliki</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 4rem;">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>OPD</th>
                        <th>Role SSO</th>
                        <th>Role Aplikasi</th>
                        <th style="width: 8rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->opd->namaopd ?? '—' }}</td>
                            <td>
                                @if ($user->role)
                                    <span class="badge bg-info text-uppercase">{{ $user->role }}</span>
                                @else
                                    <span class="text-muted">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">Belum ada role</span>
                                @endforelse
                            </td>
                            <td>
                                @if (auth()->id() === $user->id)
                                    <span class="badge bg-light text-muted">Tidak dapat ubah diri sendiri</span>
                                @else
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Ubah Role
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
