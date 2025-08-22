<div class="form-group">
    <label for="penyedia_aset">Penyedia Aset</label>
    <input type="text" required name="penyedia_aset" id="penyedia_aset" class="form-control"
        value="{{ old('penyedia_aset', $aset->penyedia_aset ?? '') }}">
</div>
