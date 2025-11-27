<div class="form-group">
    <label for="link_pse">Link PSE</label>
    <input type="url" name="link_pse" id="link_pse" class="form-control @error('link_pse') is-invalid @enderror"
        placeholder=""
        pattern="https?://.*"
        oninvalid="this.setCustomValidity('URL harus diawali dengan http:// atau https://')"
        oninput="this.setCustomValidity('')"
        value="{{ old('link_pse', $aset->link_pse ?? '') }}">
    
    @error('link_pse')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Link PSE Komdigi (Isi jika ada)</small>
</div>
