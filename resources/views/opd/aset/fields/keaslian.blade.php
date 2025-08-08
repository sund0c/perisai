<div class="form-group">
    <label for="keaslian">Keaslian (Memastikan keaslian aset)</label>
    <select name="keaslian" id="keaslian" class="form-control">
        <option value="">Pilih</option>
        <option value="1" {{ old('keaslian', $aset->keaslian ?? '') == '1' ? 'selected' : '' }}> 
            Tidak Penting
        </option>
        <option value="2" {{ old('keaslian', $aset->keaslian ?? '') == '2' ? 'selected' : '' }}> 
            Penting
        </option>
        <option value="3" {{ old('keaslian', $aset->keaslian ?? '') == '3' ? 'selected' : '' }}> 
            Sangat Penting
        </option>
    </select>
</div>
