<div class="form-group">
    <label for="status_aktif">Status Aktif</label>
    <select name="status_aktif" id="status_aktif" class="form-control">
        <option value="">Pilih</option>
        <option value="Aktif" {{ old('status_aktif', $aset->status_aktif ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="Tidak Aktif" {{ old('status_aktif', $aset->status_aktif ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
    </select>
</div>
