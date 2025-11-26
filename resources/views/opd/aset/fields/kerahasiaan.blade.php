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
    <label for="kerahasiaan" style="margin-bottom:0; padding-bottom:0; display:block; line-height:1;">Tingkat Kerahasiaan
        (C)</label>
    <small class="text-muted" style="margin-top:0;padding-top:0; display:block; line-height:1;">
        (Seberapa besar dampaknya bagi instansi jika informasi atau akses dari aset ini
        diketahui oleh pihak yang tidak berwenang?)</small>
    <select name="kerahasiaan" id="kerahasiaan" class="form-control" style="margin-top:6px;" required>
        <option value="">Pilih</option>
        @foreach ($options['kerahasiaan'] ?? [] as $opt)
            <option value="{{ $opt['value'] }}"
                {{ (string) old('kerahasiaan', $aset->kerahasiaan ?? '') === (string) $opt['value'] ? 'selected' : '' }}>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>

</div>
