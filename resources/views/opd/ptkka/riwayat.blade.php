@extends('adminlte::page')

@section('title', 'PTKKA')

@section('content_header')
    <h1>PTKKA: {{ $aset->kode_aset }} {{ $aset->nama_aset }}</h1>

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
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <a href="{{ route('ptkka.index') }}" class="btn btn-secondary mb-3 me-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <form action="{{ route('ptkka.store', $aset->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="button" class="btn btn-primary mb-3"" data-toggle="modal"
                    data-target="#modalKategori{{ $aset->id }}">
                    <i class="fas fa-plus"></i> Tambah PTKKA
                </button>
            </form>







            <table class="table table-bordered" id="ptkkaTable">
                <thead>
                    <tr>
                        <th>UID</th>
                        <th>Tanggal Mulai</th>
                        <th>Status</th>
                        <th>Total Skor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $i => $session)
                        @php $totalSkor = $session->jawabans->sum('jawaban'); @endphp
                        @php
                            $statusLabel = [
                                0 => ['label' => 'Pengisian', 'class' => 'secondary'],
                                1 => ['label' => 'Pengajuan', 'class' => 'info'],
                                2 => ['label' => 'Verifikasi', 'class' => 'warning'],
                                3 => ['label' => 'Klarifikasi', 'class' => 'primary'],
                                4 => ['label' => 'Rampung', 'class' => 'success'],
                            ];
                        @endphp
                        @php
                            $kategoriLabel = [
                                2 => 'WEB',
                                3 => 'MOBILE',
                            ];
                        @endphp

                        <tr>
                            <td>
                                {{ $session->uid ?? '-' }}
                                @if (isset($kategoriLabel[$session->standar_kategori_id]))
                                    &nbsp;&middot;&nbsp;<span class="text-uppercase">[

                                        {{ $kategoriLabel[$session->standar_kategori_id] }} ]
                                    </span>
                                @endif
                            </td>

                            <td>{{ $session->created_at->format('d/m/Y H:i') }}</td>

                            <td>
                                <div class="badge badge-{{ $statusLabel[$session->status]['class'] }} p-2">
                                    {{ strtoupper($statusLabel[$session->status]['label']) }}

                                    @if ($session->latestStatusLog)
                                        &nbsp;&middot;&nbsp;
                                        {{ $session->latestStatusLog->changed_at->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            </td>

                            <td>{{ $totalSkor }}</td>
                            <td>
                                @if ($session->status === 0 || $session->status === 3)
                                    <a href="{{ route('ptkka.detail', $session->id) }}" class="btn btn-warning btn-sm"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif

                                @if ($session->status === 0)
                                    <form action="{{ route('ptkka.destroy', $session->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin ingin menghapus sesi PTKKA ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif

                                @if ($session->status === 4)
                                    <a href="{{ route('ptkka.pdf', $session->id) }}" class="btn btn-primary btn-sm"
                                        title="Unduh PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum pernah mengisi PTKKA</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <!-- Modal Pilihan Web/Mobile -->
            <div class="modal fade" id="modalKategori{{ $aset->id }}" tabindex="-1" role="dialog"
                aria-labelledby="modalKategoriLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form method="POST" action="{{ route('ptkka.store', $aset->id) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalKategoriLabel">Pilih Standard </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>PTKKA pada <strong>{{ $aset->nama_aset }}</strong> menggunakan standard </p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="standar_kategori_id"
                                        id="web{{ $aset->id }}" value="2" checked>
                                    <label class="form-check-label" for="web{{ $aset->id }}">Aplikasi Web</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="standar_kategori_id"
                                        id="mobile{{ $aset->id }}" value="3">
                                    <label class="form-check-label" for="mobile{{ $aset->id }}">Aplikasi Mobile</label>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Lanjutkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#ptkkaTable').DataTable({
                pageLength: 50,
                autoWidth: false
            });
        });
    </script>
@endsection
