<div class="form-group">
    <label for="format_penyimpanan">Format Penyimpanan</label>
    <select name="format_penyimpanan" id="format_penyimpanan" class="form-control">
        <option value="">Pilih</option>
        <option value="Fisik" {{ old('format_penyimpanan', $aset->format_penyimpanan ?? '') == 'Fisik' ? 'selected' : '' }}>Fisik</option>
        <option value="Dokumen Elektronik" {{ old('format_penyimpanan', $aset->format_penyimpanan ?? '') == 'Dokumen Elektronik' ? 'selected' : '' }}>Dokumen Elektronik</option>
        <option value="Fisik dan Dokumen Elektronik" {{ old('format_penyimpanan', $aset->format_penyimpanan ?? '') == 'Fisik dan Dokumen Elektronik' ? 'selected' : '' }}>Fisik dan Dokumen Elektronik</option>
    </select>
</div>
