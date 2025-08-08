@extends('adminlte::page')

@section('title', 'Atur Tampilan Field Aset')

@section('content_header')
  <h1>Sub Klas Aset dari <span class="text-primary">[ {{ $klasifikasi->klasifikasiaset }} ]</span></h1>
@endsection


@section('content')
    <form action="{{ route('klasifikasiaset.field', $klasifikasi->id) }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">
                <p>Pilih field di tabel aset yang ingin ditampilkan pada menu Aset</p>

                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="checkAll">
                  <label class="form-check-label" for="checkAll">
                    Centang Semua Field Aset
                  </label>
                </div>

@php
    $fieldWajib = ['klasifikasiaset_id', 'opd_id','periode_id','subklasifikasiaset_id','kode_aset', 'nama_aset','keterangan','kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan'];
@endphp


        @foreach ($daftarFieldAset as $field)
          @php $wajib = in_array($field, $fieldWajib); @endphp

          <div class="form-check">
              <input class="form-check-input" type="checkbox"
                  name="fields[]" value="{{ $field }}"
                  {{ $wajib ? 'checked disabled' : (in_array($field, $selectedFields) ? 'checked' : '') }}>
              <label class="form-check-label">
                  {{ $field }}
              </label>

              @if($wajib)
                  <!-- Hidden input agar tetap terkirim meskipun checkbox-nya disabled -->
                  <input type="hidden" name="fields[]" value="{{ $field }}">
              @endif
          </div>
        @endforeach


                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                <a href="{{ route('klasifikasiaset.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </div>
        </div>
    </form>
@endsection

@section('js')
<script>
    document.getElementById('checkAll').addEventListener('change', function () {
        const excluded = ['kode_aset', 'nama_aset','kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan'];

        document.querySelectorAll('input[name="fields[]"]').forEach(cb => {
            if (!excluded.includes(cb.value)) {
                cb.checked = this.checked;
            }
        });
    });
</script>
@endsection

