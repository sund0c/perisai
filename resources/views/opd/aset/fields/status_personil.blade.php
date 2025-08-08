<div class="form-group">
    <label for="status_personil">Status Personil</label>
    <select name="status_personil" id="status_personil" class="form-control">
        <option value="">Pilih</option>
        <option value="SDM">{{ old("status_personil") == "SDM" ? "selected" : "" }} SDM</option>
        <option value="Pihak Ketiga">{{ old("status_personil") == "Pihak Ketiga" ? "selected" : "" }} Pihak Ketiga</option>
    </select>
</div>
