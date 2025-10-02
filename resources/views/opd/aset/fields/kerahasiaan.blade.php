<div class="form-group">
    <label for="kerahasiaan">Kerahasiaan</label>
    <select name="kerahasiaan" id="kerahasiaan" class="form-control" required>
        <option value="">Pilih</option>
        <option value="1" {{ old('kerahasiaan', $aset->kerahasiaan ?? '') == '1' ? 'selected' : '' }}>
            Tidak signifikan, tidak ada data sensitif
        </option>
        <option value="2" {{ old('kerahasiaan', $aset->kerahasiaan ?? '') == '2' ? 'selected' : '' }}>
            Berdampak tapi tidak kritis
        </option>
        <option value="3" {{ old('kerahasiaan', $aset->kerahasiaan ?? '') == '3' ? 'selected' : '' }}>
            Sangat Kritikal (membahayakan,tuntutan hukum dll)
        </option>
    </select>
</div>
