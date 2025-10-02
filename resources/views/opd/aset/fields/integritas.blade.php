{{-- <div class="form-group">
    <label for="integritas">Integritas</label>
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
</div> --}}

<div class="form-group">
    <label for="integritas">Integritas (Seberapa penting menjaga agar aset tidak berubah tanpa izin?)</label>
    <select name="integritas" id="integritas" class="form-control" required>
        <option value="">Pilih</option>
        @foreach ($options['integritas'] ?? [] as $opt)
            <option value="{{ $opt['value'] }}"
                {{ (string) old('integritas', $aset->integritas ?? '') === (string) $opt['value'] ? 'selected' : '' }}>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>
</div>
