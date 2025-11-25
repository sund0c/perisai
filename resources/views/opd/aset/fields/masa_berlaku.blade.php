<div class="form-group">
    <label for="masa_berlaku">Masa Berlaku</label>
    <input type="text" required name="masa_berlaku" id="masa_berlaku" class="form-control"
        value="{{ old('masa_berlaku', $aset->masa_berlaku ?? '') }}">
    <small class="form-text text-muted">(Contoh : 5 Tahun atau 12 Bulan)</small>
</div>
