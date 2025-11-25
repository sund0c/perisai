<div class="form-group">
    <label for="lokasi">Lokasi</label>
    <input type="text" required name="lokasi" id="lokasi" class="form-control"
    value="{{ old('lokasi', $aset->lokasi ?? '') }}">
    <small class="form-text text-muted">(Contoh: Rak arsip Bidang 5 atau Data Center Pemprov Bali)</small>
</div>
