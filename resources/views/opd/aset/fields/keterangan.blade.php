<div class="form-group">
    <label for="keterangan">Keterangan</label>
    <input type="text" required name="keterangan" id="keterangan" class="form-control"
        value="{{ old('keterangan', $aset->keterangan ?? '') }}">
</div>
