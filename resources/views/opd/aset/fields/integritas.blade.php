<div class="form-group">
    <label for="integritas">Integritas (Dampak jika aset berubah secara ilegal)</label>
    <select name="integritas" id="integritas" class="form-control" required>
        <option value="">Pilih</option>
        <option value="1" {{ old('integritas', $aset->integritas ?? '') == '1' ? 'selected' : '' }}>
            Tidak signifikan
        </option>
        <option value="2" {{ old('integritas', $aset->integritas ?? '') == '2' ? 'selected' : '' }}>
            Mengganggu operasional
        </option>
        <option value="3" {{ old('integritas', $aset->integritas ?? '') == '3' ? 'selected' : '' }}>
            Sangat Kritikal (membahayakan,tuntutan hukum dll)
        </option>
    </select>
</div>
