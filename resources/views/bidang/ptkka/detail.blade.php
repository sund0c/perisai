{{-- Pindahkan @extends ke paling atas file --}}
@extends('adminlte::page')

@push('css')
    <style>
        .nav-pills .nav-link {
            text-align: left;
            border-radius: 0;
            border-left: 3px solid transparent;
        }

        .nav-pills .nav-link.active {
            background: #6c757d;
            border-left: 3px solid #000;
            font-weight: bold;
        }
    </style>
@endpush

@section('title', 'Catatan Verifikasi PTKKA')

@section('content_header')


    <h1>Verifikasi: {{ $session->aset->nama_aset }} [Standard: {{ $kategoriText }}]</h1>
    <p class="mb-1">OPD: {{ $session->aset->opd->namaopd ?? '-' }}</p>
    <p class="text-muted small mb-0">
        Catatan dari Dinas Kominfos Prov Bali
    </p>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if (($kunci ?? 'locked') === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @else
                <i class="fas fa-lock-open text-success ml-1" title="Terbuka"></i>
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
            {{-- Tombol atas tetap, tapi (opsional) sesuaikan route ajukan verifikasi ke versi "bidang" --}}
            <div class="d-flex mb-3" style="gap:10px;">
                <a href="{{ route('bidang.ptkka.index') }}" class="btn btn-secondary mb-3 btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if ((int) $session->status === 3)
                    {{-- Saat KLARIFIKASI → tampil tombol CLOSING --}}
                    <form action="{{ route('bidang.ptkka.ajukanclosing', $session->id) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success mb-3 btn-sm"
                            onclick="return confirm('Yakin membuat PTKKA ini RAMPUNG ?')">
                            CLOSING
                        </button>
                    </form>
                @else
                    {{-- Saat bukan KLARIFIKASI → tampil tombol ajukan klarifikasi --}}
                    <form action="{{ route('bidang.ptkka.ajukanklarifikasi', $session->id) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary mb-3 btn-sm"
                            onclick="return confirm('Yakin ingin mengajukan klarifikasi?')">
                            Proses Klarifikasi
                        </button>
                    </form>
                @endif
                <a href="{{ route('bidang.ptkka.exportPDF', $session->id) }}" target="_blank"
                    class="btn btn-danger mb-3 btn-sm">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>

            <div class="row">
                {{-- Sidebar kiri tetap --}}
                <div class="col-md-3">
                    <div class="nav flex-column nav-pills" id="v-tabs" role="tablist" aria-orientation="vertical">
                        @foreach ($fungsiStandars as $index => $fungsi)
                            <a class="nav-link {{ $index == 0 ? 'active' : '' }}" id="v-tab-{{ $fungsi->id }}"
                                data-toggle="pill" href="#v-fungsi-{{ $fungsi->id }}" role="tab"
                                aria-controls="v-fungsi-{{ $fungsi->id }}"
                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                {{ $fungsi->nama }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Konten kanan --}}
                <div class="col-md-9">
                    <div class="tab-content" id="v-tabs-content">
                        @foreach ($fungsiStandars as $index => $fungsi)
                            @php $no = 1; @endphp
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                id="v-fungsi-{{ $fungsi->id }}" role="tabpanel"
                                aria-labelledby="v-tab-{{ $fungsi->id }}">

                                {{-- FORM per TAB: hanya kirim catatanadmin --}}
                                <form class="form-catatan"
                                    action="{{ route('bidang.ptkka.simpanCatatan', ['session' => $session->id, 'fungsi' => $fungsi->id]) }}"
                                    method="POST">
                                    @csrf

                                    @foreach ($fungsi->indikators as $indikator)
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <strong>{{ $no++ }}. {{ $indikator->indikator }}</strong>
                                            </div>
                                            <div class="card-body">
                                                @if ($indikator->tujuan)
                                                    <div class="mb-2">
                                                        <strong>Tujuan:</strong><br>{{ $indikator->tujuan }}
                                                    </div>
                                                @endif

                                                <div class="mb-2">
                                                    @forelse ($indikator->rekomendasis as $rek)
                                                        @php
                                                            $jawaban = $rek->jawabans->firstWhere(
                                                                'ptkka_session_id',
                                                                $session->id,
                                                            );
                                                            $nilai = optional($jawaban)->jawaban ?? 0;
                                                        @endphp
                                                        <div class="border p-2 mb-4 bg-white">
                                                            <p class="mb-1">
                                                                <strong>Rekomendasi:</strong><br>{{ $rek->rekomendasi }}
                                                            </p>
                                                            <p class="mb-2"><strong>Bukti
                                                                    Dukung:</strong><br>{{ $rek->buktidukung }}</p>

                                                            <div class="form-group">
                                                                <label><strong>Jawaban Pemilik Aset</strong></label>
                                                                {{-- disabled → tidak terkirim --}}
                                                                <select disabled class="form-control">
                                                                    <option value="0"
                                                                        {{ $nilai == 0 ? 'selected' : '' }}>Tidak
                                                                        Diterapkan</option>
                                                                    <option value="1"
                                                                        {{ $nilai == 1 ? 'selected' : '' }}>Sebagian
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ $nilai == 2 ? 'selected' : '' }}>Sepenuhnya
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <label><strong>Penjelasan Pemilik Aset</strong></label>
                                                                {{-- ganti readonly -> disabled, dan HAPUS name agar pasti tidak terkirim --}}
                                                                <textarea class="form-control" rows="2" disabled>{{ optional($jawaban)->penjelasanopd }}</textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label><strong>Link Bukti Dukung (Google
                                                                        Drive)</strong></label>
                                                                {{-- ganti readonly -> disabled, dan HAPUS name --}}
                                                                <input type="url" class="form-control"
                                                                    value="{{ optional($jawaban)->linkbuktidukung }}"
                                                                    disabled>
                                                            </div>

                                                            <div class="form-group">
                                                                <label><strong>Catatan Diskominfos Prov
                                                                        Bali</strong></label>
                                                                {{-- hanya ini yang DIKIRIM --}}
                                                                <textarea name="catatanadmin[{{ $rek->id }}]" class="form-control" rows="2">{{ optional($jawaban)->catatanadmin }}</textarea>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="text-muted">Belum ada rekomendasi</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-success">Update Catatan untuk Aspek
                                            "{{ $fungsi->nama }}"</button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Guard tambahan: kalau ada elemen bernama selain catatanadmin, disable saat submit
        document.querySelectorAll('form.form-catatan').forEach(form => {
            form.addEventListener('submit', () => {
                form.querySelectorAll(
                        '[name]:not([name^="catatanadmin"]):not([name="_token"]):not([name="_method"])'
                    )
                    .forEach(el => (el.disabled = true));
            });

        });
    </script>
@endpush
