@extends('adminlte::page')

@section('title', 'PTKKA')

@section('content_header')
    <h1>Penilaian Tingkat Kepatuhan Keamanan Aplikasi</h1>
    <p class="text-muted small mb-0">
        Kepgub Bali No. 584/03-E/HK/2024 Pedoman Manajemen Keamanan Informasi
    </p>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if ($kunci === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
        </span>
    </li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="ptkkaTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="100px">Kode Aset</th>
                        <th>Nama Aset</th>
                        <th width="200px">Skor PTKKA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($asets as $aset)
                        @php
                            $session = $aset->ptkkaTerakhir;
                            $skor = $session ? $session->jawabans->sum('jawaban') : null;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('ptkka.riwayat', $aset->id) }}" class="text-primary font-weight-bold">
                                    {{ $aset->kode_aset }}
                                </a>
                            </td>
                            <td>{{ $aset->nama_aset }}</td>
                            <td>
                                @if ($session)
                                    Skor: {{ $skor }}
                                @else
                                    <span class="text-danger">BELUM PERNAH</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada aset ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            $('#ptkkaTable').DataTable({
                autoWidth: false,
                pageLength: 50
            });
        });
    </script>
@endsection
