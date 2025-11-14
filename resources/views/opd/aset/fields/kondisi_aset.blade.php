<div class="form-group">
    <label for="kondisi_aset">Kondisi Aset </label>
    <select name="kondisi_aset" id="kondisi_aset" class="form-control" required>
        <option value="">Pilih</option>
        <option value="Baik" {{ old('kondisi_aset', $aset->kondisi_aset ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
        <option value="Tidak Layak" {{ old('kondisi_aset', $aset->kondisi_aset ?? '') == 'Tidak Layak' ? 'selected' : '' }}> Tidak Layak</option>
        <option value="Rusak" {{ old('kondisi_aset', $aset->kondisi_aset ?? '') == 'Rusak' ? 'selected' : '' }}> Rusak</option>
    </select>
</div>
