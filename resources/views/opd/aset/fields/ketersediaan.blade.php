{{-- <div class="form-group">
    <label for="ketersediaan">Ketersediaan</label>
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
</div> --}}

<div class="form-group">
    <label for="ketersediaan">Tingkat Ketersediaan (A)</label>
    <select name="ketersediaan" id="ketersediaan" class="form-control" required>
        <option value="">Pilih</option>
        @foreach ($options['ketersediaan'] ?? [] as $opt)
            <option value="{{ $opt['value'] }}"
                {{ (string) old('ketersediaan', $aset->ketersediaan ?? '') === (string) $opt['value'] ? 'selected' : '' }}>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">(Seberapa besar dampaknya bagi instansi jika aset ini tidak dapat digunakan atau diakses saat dibutuhkan?)</small>
</div>
