<div class="form-group">
    <label for="link_url">URL Aplikasi (Isi jika aplikasi berbasis website)</label>
    <input type="url" name="link_url" id="link_url" class="form-control @error('link_url') is-invalid @enderror"
        placeholder=""
        pattern="https://.*"
        oninvalid="this.setCustomValidity('URL harus diawali dengan https://')"
        oninput="this.setCustomValidity('')"
        value="{{ old('link_url', $aset->link_url ?? '') }}">
    @error('link_url')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
