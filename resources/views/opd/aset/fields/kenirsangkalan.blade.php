{{-- <div class="form-group">
    <label for="kenirsangkalan">Kenirsangkalan</label>
    <select name="kenirsangkalan" id="kenirsangkalan" class="form-control" required>
        <option value="">Pilih</option>
        <option value="1" {{ old('kenirsangkalan', $aset->kenirsangkalan ?? '') == '1' ? 'selected' : '' }}>
            Tidak Penting
        </option>
        <option value="2" {{ old('kenirsangkalan', $aset->kenirsangkalan ?? '') == '2' ? 'selected' : '' }}>
            Penting
        </option>
        <option value="3" {{ old('kenirsangkalan', $aset->kenirsangkalan ?? '') == '3' ? 'selected' : '' }}>
            Sangat Penting
        </option>
    </select>
</div> --}}

<div class="form-group">
    <label for="kenirsangkalan">Tingkat Kenirsangkalan (N/A)</label>
    <select name="kenirsangkalan" id="kenirsangkalan" class="form-control" required>
        <option value="">Pilih</option>
        @foreach ($options['kenirsangkalan'] ?? [] as $opt)
            <option value="{{ $opt['value'] }}"
                {{ (string) old('kenirsangkalan', $aset->kenirsangkalan ?? '') === (string) $opt['value'] ? 'selected' : '' }}>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>
</div>
