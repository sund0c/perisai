<div class="form-group">
    <label for="subklasifikasiaset_id">Sub Klasifikasi Aset</label>
    <select name="subklasifikasiaset_id" class="form-control" required>
        <option value="">Pilih Sub Klasifikasi</option>
        @foreach ($subklasifikasis as $sub)
            <option value="{{ $sub->id }}"
                {{ old('subklasifikasiaset_id', $aset->subklasifikasiaset_id ?? '') == $sub->id ? 'selected' : '' }}>
                {{ $sub->subklasifikasiaset }}
            </option>
        @endforeach
    </select>
</div>
