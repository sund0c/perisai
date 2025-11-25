{{-- <div class="form-group">
    <label for="kerahasiaan">Kerahasiaan (Seberapa penting mencegah akses pihak tidak berwenang terhadap aset?)</label>
    <select name="kerahasiaan" id="kerahasiaan" class="form-control" required>
        <option value="">Pilih</option>
        <option value="1" {{ old('kerahasiaan', $aset->kerahasiaan ?? '') == '1' ? 'selected' : '' }}>
            Tidak signifikan, memang boleh diakses publik
        </option>
        <option value="2" {{ old('kerahasiaan', $aset->kerahasiaan ?? '') == '2' ? 'selected' : '' }}>
            Penting, hanya untuk internal, tidak mengandung data pribadi sensitif UU PDP atau informasi dikecualikan UU
            KIP
        </option>
        <option value="3" {{ old('kerahasiaan', $aset->kerahasiaan ?? '') == '3' ? 'selected' : '' }}>
            Sangat Penting, mengandung data pribadi sensitif UU PDP atau informasi dikecualikan UU KIP
        </option>
    </select>
</div> --}}

<div class="form-group">
    <label for="kerahasiaan">Tingkat Kerahasiaan (C)</label>
    <select name="kerahasiaan" id="kerahasiaan" class="form-control" required>
        <option value="">Pilih</option>
        @foreach ($options['kerahasiaan'] ?? [] as $opt)
            <option value="{{ $opt['value'] }}"
                {{ (string) old('kerahasiaan', $aset->kerahasiaan ?? '') === (string) $opt['value'] ? 'selected' : '' }}>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">(Seberapa penting mencegah akses pihak tidak berwenang terhadap aset?)</small>
</div>
