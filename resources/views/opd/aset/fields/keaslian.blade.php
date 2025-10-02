{{-- <div class="form-group">
    <label for="keaslian">Keaslian</label>
    <select name="keaslian" id="keaslian" class="form-control" required>
        <option value="">Pilih</option>
        <option value="1" {{ old('keaslian', $aset->keaslian ?? '') == '1' ? 'selected' : '' }}>
            Tidak Penting
        </option>
        <option value="2" {{ old('keaslian', $aset->keaslian ?? '') == '2' ? 'selected' : '' }}>
            Penting
        </option>
        <option value="3" {{ old('keaslian', $aset->keaslian ?? '') == '3' ? 'selected' : '' }}>
            Sangat Penting
        </option>
    </select>
</div> --}}

<div class="form-group">
    <label for="keaslian">Keaslian (Seberapa penting membuktikan bahwa aset adalah versi asli dan sah?)</label>
    <select name="keaslian" id="keaslian" class="form-control" required>
        <option value="">Pilih</option>
        @foreach ($options['keaslian'] ?? [] as $opt)
            <option value="{{ $opt['value'] }}"
                {{ (string) old('keaslian', $aset->keaslian ?? '') === (string) $opt['value'] ? 'selected' : '' }}>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>
</div>
