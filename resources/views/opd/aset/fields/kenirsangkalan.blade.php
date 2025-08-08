<div class="form-group">
    <label for="kenirsangkalan">Kenirsangkalan (Memastikan siapa pembuat/menyetujui/masuk/dll aset)</label>
    <select name="kenirsangkalan" id="kenirsangkalan" class="form-control">
        <option value="">Pilih</option>
        <option value="1" {{ old('kenirsangkalan', $aset->kenirsangkalan ?? '') == '1' ? 'selected' : '' }}>
            Tidak Penting 
        </option>
        <option value="2" {{ old('kenirsangkalan', $aset->kenirsangkalan ?? '') == '2' ? 'selected' : '' }}>
            Penting
        </option>
        <option value="3" {{ old('kenirsangkalan', $aset->kenirsangkalan ?? '') == '3' ? 'selected' : '' }}>
            Sangat Penting
        </option>
    </select>
</div>
