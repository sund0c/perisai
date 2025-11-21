<div class="form-group">
    <label for="link_url">URL Aplikasi (Isi jika aplikasi berbasis website)</label>
    <input type="text" name="link_url" id="link_url" class="form-control"
        value="{{ old('link_url', $aset->link_url ?? '') }}">
</div>
