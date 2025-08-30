@push('css')
    <style>
        .nav-pills .nav-link {
            text-align: left;
            border-radius: 0;
            border-left: 3px solid transparent;
        }

        .nav-pills .nav-link.active {
            background-color: #6c757d;
            border-left: 3px solid #000000;
            font-weight: bold;
        }
    </style>
@endpush

@extends('adminlte::page')

@section('title', 'PTKKA')

@section('content_header')
    @php
        $kategoriLabel = [
            2 => 'WEB',
            3 => 'MOBILE',
        ];
    @endphp
    <h4 class="mb-1">{{ $session->aset->nama_aset ?? '-' }}</h4>
    <h5 class="text-muted"> {{ $kategoriLabel[$session->standar_kategori_id] }}
        {{-- UID: {{ $session->uid }} (
        @if (isset($kategoriLabel[$session->standar_kategori_id]))
            <span class="text-uppercase">
                {{ $kategoriLabel[$session->standar_kategori_id] }}
            </span>
        @endif

        ) --}}
    </h5>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if ($kunci === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
            :: {{ strtoupper($namaOpd ?? '-') }}
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
            {{-- Tombol Kembali dan Simpan --}}
            <div class="d-flex mb-3" style="gap: 10px;">
                <a href="{{ route('opd.ptkka.riwayat', $session->aset) }}" class="btn btn-sm btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if ($session->status === 0)
                    <form action="{{ route('opd.ptkka.ajukanverifikasi', $session) }}" method="POST">
                        @csrf
                        <button onclick="return confirm('Yakin ingin mengajukan verifikasi?')" type="submit"
                            class="btn btn-sm btn-primary mb-3">Ajukan Verifikasi
                        </button>
                    </form>
                @endif
                <a href="{{ route('opd.ptkka.exportPDF', $session) }}" target="_blank" class="btn btn-sm btn-danger mb-3">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>

            {{-- Form --}}
            {{-- <form action="{{ route('opd.ptkka.simpan', $session) }}" method="POST" id="form-ptkka">
                @csrf --}}
            <div class="row">
                {{-- Sidebar Tab --}}
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

                {{-- Konten Tab --}}
                <div class="col-md-9">
                    <div class="tab-content" id="v-tabs-content">
                        {{-- Dalam foreach fungsiStandars --}}
                        @foreach ($fungsiStandars as $index => $fungsi)
                            @php $no = 1; @endphp
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                id="v-fungsi-{{ $fungsi->id }}" role="tabpanel"
                                aria-labelledby="v-tab-{{ $fungsi->id }}">

                                {{-- Form untuk setiap tab --}}
                                <form action="{{ route('opd.ptkka.simpanPerFungsi', [$session->id, $fungsi->id]) }}"
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
                                                        <strong>Tujuan:</strong><br>
                                                        {{ $indikator->tujuan }}
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

                                                            @php
                                                                // Ambil jawaban tersimpan (jangan paksa 0)
                                                                $jawaban = $rek->jawabans->firstWhere(
                                                                    'ptkka_session_id',
                                                                    $session->id,
                                                                );
                                                                $nilaiTersimpan = $jawaban->jawaban ?? null;

                                                                // Urutan prioritas: old() -> tersimpan -> default 1
                                                                $selected = old("jawaban.{$rek->id}", $nilaiTersimpan);
                                                                if ($selected === null || $selected === '') {
                                                                    $selected = 1; // default yang kamu mau
                                                                }
                                                                $selected = (int) $selected;
                                                            @endphp

                                                            <div class="form-group">
                                                                <label><strong>Jawaban Pemilik Aset</strong></label>
                                                                <select name="jawaban[{{ $rek->id }}]"
                                                                    class="form-control" required>
                                                                    <option value="0"
                                                                        {{ $selected === 0 ? 'selected' : '' }}>
                                                                        Tidak Relevan (memang tidak membutuhkan standar
                                                                        tersebut)
                                                                    </option>
                                                                    <option value="1"
                                                                        {{ $selected === 1 ? 'selected' : '' }}>Tidak
                                                                        Diterapkan</option>
                                                                    <option value="2"
                                                                        {{ $selected === 2 ? 'selected' : '' }}>Diterapkan
                                                                        Sebagian</option>
                                                                    <option value="3"
                                                                        {{ $selected === 3 ? 'selected' : '' }}>Diterapkan
                                                                        Sepenuhnya</option>
                                                                </select>
                                                            </div>


                                                            <div class="form-group">
                                                                <label><strong>Penjelasan Pemilik Aset</strong></label>
                                                                <textarea name="penjelasanopd[{{ $rek->id }}]" class="form-control" rows="2" required>{{ optional($jawaban)->penjelasanopd }}</textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Link Bukti Dukung
                                                                    (letakkan hanya di drive.baliprov.go.id)
                                                                </label>
                                                                <input type="url"
                                                                    name="linkbuktidukung[{{ $rek->id }}]"
                                                                    class="form-control"
                                                                    value="{{ optional($jawaban)->linkbuktidukung }}"
                                                                    required>
                                                            </div>

                                                            @if ($session->status === 3)
                                                                <div class="form-group">
                                                                    <label><strong>Catatan Admin</strong></label>
                                                                    <textarea disabled name="catatanadmin[{{ $rek->id }}]" class="form-control" rows="2" required>{{ optional($jawaban)->catatanadmin }}</textarea>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <div class="text-muted">Belum ada rekomendasi</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Tombol Simpan per TAB --}}
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-success">
                                            Simpan Jawaban Aspek "{{ $fungsi->nama }}"
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            {{-- </form> --}}
        </div>
    </div>
@endsection
