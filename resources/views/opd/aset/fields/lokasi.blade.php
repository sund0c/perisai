<div class="form-group">
    <label for="lokasi">Lokasi (contoh: rak arsip Bidang 5 atau Data Center Pemprov Bali)</label>
    <input type="text" name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi', $aset->lokasi ?? '') }}">
</div>
