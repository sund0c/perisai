<div class="form-group">
    <label for="ketersediaan">Ketersediaan (Dampak jika aset tidak tersedia)</label>
    <select name="ketersediaan" id="ketersediaan" class="form-control" required>
        <option value="">Pilih</option>
        <option value="1" {{ old('ketersediaan', $aset->ketersediaan ?? '') == '1' ? 'selected' : '' }}>
            Tidak signifikan jika sementara
        </option>
        <option value="2" {{ old('ketersediaan', $aset->ketersediaan ?? '') == '2' ? 'selected' : '' }}>
            Mengganggu operasional bahkan jika hanya sementara
        </option>
        <option value="3" {{ old('ketersediaan', $aset->ketersediaan ?? '') == '3' ? 'selected' : '' }}>
            Sangat Kritikal (membahayakan,tuntutan hukum dll)
        </option>
    </select>
</div>
