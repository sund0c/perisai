@extends('adminlte::page')

@section('title', 'Edit Kategori SE')

@section('content_header')
    <h1>Edit {{ $aset->nama_aset }}</h1>
@endsection


@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if ($kunci === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
            :: {{ strtoupper($namaOpd) }}
        </span>
    </li>
@endsection

@section('content')
    <form action="{{ route('opd.kategorise.update', $aset->id) }}" method="POST">
        @csrf
        @method('PUT')

        @foreach ($indikators as $indikator)
            @php
                $jawabanLama = $kategoriSe->jawaban[$indikator->kode]['jawaban'] ?? null;
                $keteranganLama = $kategoriSe->jawaban[$indikator->kode]['keterangan'] ?? '';
            @endphp

            <div class="card mb-4">
                <div class="card-header font-weight-bold">
                    {{ $indikator->kode }}. {{ $indikator->pertanyaan }}
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Kiri: Pilihan Jawaban --}}
                        <div class="col-md-6 border-right">
                            @foreach (['A', 'B', 'C'] as $opsi)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio"
                                        name="jawaban[{{ $indikator->kode }}][jawaban]" value="{{ $opsi }}"
                                        {{ old("jawaban.{$indikator->kode}.jawaban", $jawabanLama ?? 'A') === $opsi ? 'checked' : '' }}
                                        required>

                                    <label class="form-check-label">
                                        <strong>{{ $opsi }}</strong> –
                                        {{ $indikator['opsi_' . strtolower($opsi)] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Kanan: Keterangan --}}
                        <div class="col-md-6">
                            <label for="keterangan_{{ $indikator->kode }}"><strong>Keterangan:</strong></label>
                            <textarea name="jawaban[{{ $indikator->kode }}][keterangan]" id="keterangan_{{ $indikator->kode }}"
                                class="form-control" rows="3" placeholder="Tulis catatan Anda jika ada...">{{ old('jawaban.' . $indikator->kode . '.keterangan', $keteranganLama) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <input type="hidden" name="kategori" value="{{ request('kategori') }}">


        <div class="text-left mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Penilaian
            </button>
            <a href="{{ route('opd.kategorise.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form><BR><BR>
@endsection
