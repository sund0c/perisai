@extends('adminlte::page')

@section('title', 'Edit Kategori SE')

@section('css')
    <!-- Selectize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.css">
    <style>
        .selectize-control.multi .selectize-input > div {
            background-color: #6c757d;
            border: 1px solid #5a6268;
            color: #fff;
            border-radius: 4px;
            padding: 2px 8px;
            margin: 2px;
        }
        
        .selectize-control.multi .selectize-input > div .remove {
            color: #fff;
            margin-left: 5px;
            text-decoration: none;
        }
        
        .selectize-control.multi .selectize-input > div .remove:hover {
            color: #dc3545;
        }
        
        .selectize-input {
            min-height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }
        
        .selectize-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .selectize-input.focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endsection

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


    <form action="{{ route('opd.kategorise.update', ['aset' => $aset, 'kategori' => request('kategori')]) }}" method="POST">


        @csrf
        @method('PUT')

        @foreach ($indikators as $indikator)
            @php
                // Ambil seluruh old input untuk field 'jawaban' (bisa null jika tidak ada)
                $oldAll = old('jawaban');

                // Ambil data tersimpan dari DB (bisa null kalau belum ada)
                $saved = is_array($kategoriSe->jawaban ?? null) ? $kategoriSe->jawaban : [];

                // Pilih sumber: prioritas old() (ketika validasi gagal), fallback ke tersimpan
                $fromOld =
                    is_array($oldAll) && array_key_exists($indikator->kode, $oldAll)
                        ? $oldAll[$indikator->kode]
                        : $saved[$indikator->kode] ?? [];

                $jawabanLama = $fromOld['jawaban'] ?? null;
                $keteranganLama = $fromOld['keterangan'] ?? '';
                $dataPribadiLama = $fromOld['data_pribadi'] ?? [];
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
                                        {{ ($jawabanLama ?? 'A') === $opsi ? 'checked' : '' }} required>
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
                            
                            @if (strtolower($indikator->kode) === 'i6')
                                {{-- Form khusus untuk indikator I6 - Input text yang akan di-convert ke Selectize --}}
                                <input type="text" class="selectize-keterangan" 
                                       name="jawaban[{{ $indikator->kode }}][keterangan]" 
                                       id="keterangan_{{ $indikator->kode }}"
                                       placeholder="Pilih data pribadi untuk keterangan..."
                                       value="{{ is_array($keteranganLama) ? implode(',', $keteranganLama) : $keteranganLama }}"
                                       data-selected="{{ json_encode(is_array($keteranganLama) ? $keteranganLama : explode(',', $keteranganLama ?? '')) }}">
                                <small class="form-text text-muted">Pilih data pribadi yang relevan sebagai keterangan untuk indikator ini.</small>
                            @else
                                {{-- Form normal untuk indikator lainnya --}}
                                <textarea name="jawaban[{{ $indikator->kode }}][keterangan]" id="keterangan_{{ $indikator->kode }}"
                                    class="form-control" rows="3" placeholder="Tulis catatan Anda jika ada...">{{ $keteranganLama }}</textarea>
                            @endif
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

@section('js')
    <!-- jQuery dan Selectize JavaScript dari cdnjs -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>
    
    <script>
        $(document).ready(function() {
            console.log('Document ready, jQuery version:', $.fn.jquery);
            console.log('Checking if Selectize is available...');
            
            // Tunggu sampai Selectize benar-benar tersedia
            function waitForSelectize(callback, maxRetries = 30) {
                if (typeof $.fn.selectize !== 'undefined') {
                    console.log('Selectize is available!');
                    callback();
                } else if (maxRetries > 0) {
                    console.log('Selectize not yet available, retrying... (' + maxRetries + ' attempts left)');
                    setTimeout(function() {
                        waitForSelectize(callback, maxRetries - 1);
                    }, 200);
                } else {
                    console.error('Selectize failed to load after maximum retries');
                }
            }
            
            waitForSelectize(function() {
                console.log('Starting selectize keterangan initialization...');
                
                // Cari select untuk keterangan indikator I6
                const $keteranganSelect = $('.selectize-keterangan');
                console.log('Found selectize keterangan elements:', $keteranganSelect.length);
                
                if ($keteranganSelect.length > 0) {
                    console.log('Processing selectize keterangan element...');
                    
                    // Ambil data yang sudah dipilih sebelumnya
                    const selectedData = JSON.parse($keteranganSelect.attr('data-selected') || '[]');
                    console.log('Selected keterangan data:', selectedData);
                    
                    // Fetch data pribadi dari API
                    $.ajax({
                        url: '{{ url("/api/data-pribadi-master/kode") }}',
                        type: 'GET',
                        dataType: 'json',
                        success: function(apiData) {
                            console.log('API response for keterangan:', apiData);
                            
                            let options = [];
                            let items = [];
                            
                            // Siapkan options dan items dari API
                            if (apiData.success && Array.isArray(apiData.data)) {
                                apiData.data.forEach(function(item) {
                                    options.push({
                                        value: item,
                                        text: item
                                    });
                                    
                                    // Tambahkan ke items jika sudah dipilih sebelumnya
                                    if (selectedData.includes(item)) {
                                        items.push(item);
                                    }
                                });
                            }
                            
                            console.log('Options prepared for keterangan:', options);
                            console.log('Items to select for keterangan:', items);
                            
                            try {
                                // Inisialisasi Selectize untuk input text (akan menghasilkan comma-separated string)
                                const selectizeInstance = $keteranganSelect.selectize({
                                    plugins: ['remove_button'],
                                    delimiter: ',', // Delimiter untuk multiple values
                                    persist: false,
                                    create: false, // Tidak memungkinkan menambah tag baru
                                    placeholder: 'Pilih data pribadi untuk keterangan...',
                                    options: options,
                                    items: items, // Items yang sudah terpilih
                                    valueField: 'value',
                                    labelField: 'text',
                                    searchField: 'text',
                                    closeAfterSelect: false,
                                    hideSelected: true,
                                    maxItems: null, // Unlimited items
                                    onItemAdd: function(value, $item) {
                                        console.log('Keterangan tag added:', value);
                                        // Update input value sebagai comma-separated string
                                        const values = this.getValue();
                                        const stringValue = Array.isArray(values) ? values.join(', ') : values;
                                        this.$input[0].value = stringValue;
                                        console.log('Input value updated on add:', stringValue);
                                    },
                                    onItemRemove: function(value) {
                                        console.log('Keterangan tag removed:', value);
                                        // Update input value sebagai comma-separated string
                                        const values = this.getValue();
                                        const stringValue = Array.isArray(values) ? values.join(', ') : values;
                                        this.$input[0].value = stringValue;
                                        console.log('Input value updated on remove:', stringValue);
                                    },
                                    onInitialize: function() {
                                        console.log('Selectize initialized with items:', this.items);
                                        // Set initial value sebagai comma-separated string
                                        const values = this.getValue();
                                        const stringValue = Array.isArray(values) ? values.join(', ') : values;
                                        this.$input[0].value = stringValue;
                                        console.log('Initial input value set:', stringValue);
                                    }
                                });
                                
                                console.log('Selectize keterangan initialized successfully');
                                
                            } catch (error) {
                                console.error('Error initializing Selectize keterangan:', error);
                            }
                            
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data pribadi for keterangan:', error);
                            
                            // Fallback: Inisialisasi Selectize tanpa data API (tanpa create)
                            try {
                                $keteranganSelect.selectize({
                                    plugins: ['remove_button'],
                                    delimiter: ',',
                                    persist: false,
                                    create: false, // Tetap tidak boleh menambah tag baru
                                    placeholder: 'Error: Tidak dapat memuat data',
                                    options: [],
                                    items: []
                                });
                                
                                // Disable selectize jika error
                                if ($keteranganSelect[0].selectize) {
                                    $keteranganSelect[0].selectize.disable();
                                }
                            } catch (fallbackError) {
                                console.error('Fallback Selectize keterangan initialization failed:', fallbackError);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
