@extends('adminlte::page')

@section('title', 'Ubah Role Pengguna')

@section('content_header')
    <h1>Ubah Role {{ $user->name }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">OPD</label>
                    <input type="text" class="form-control" value="{{ $user->opd->namaopd ?? '—' }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role dari SSO</label>
                    <input type="text" class="form-control" value="{{ $user->role ? strtoupper($user->role) : '—' }}"
                        disabled>
                    <small class="form-text text-muted">Nilai ini otomatis mengikuti role terakhir dari SSO.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role Aplikasi</label>
                    <select name="roles[]" class="form-control @error('roles') is-invalid @enderror" multiple size="5">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected($user->roles->contains('id', $role->id))>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('roles')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center" style="gap: .5rem;">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
