<div>
    <div class="form-group">
        <label for="relasi_struktur_id">Work Center</label>
        <select class="form-control form-control-lg" name="relasi_struktur_id" id="relasi_struktur_id"
            wire:model.live='relasi_struktur_id' required>
            <option value="" selected disabled>- pilih work center -</option>
            @foreach ($relasi_struktur as $item)
                <option value="{{ $item->id }}">{{ $item->departemen->name ?? '-' }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="relasi_area_id">Location</label>
        <select class="form-control form-control-lg" name="relasi_area_id" id="relasi_area_id"
            wire:model.live='relasi_area_id' required>
            <option value="" selected disabled>- pilih location -</option>
            <option value="">- pilih semua -</option>
            @foreach ($relasi_area as $item)
                <option value="{{ $item->id }}">{{ $item->sub_lokasi->name ?? '-' }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="tipe_equipment_id">Tipe Equipment <span class="text-info">(optional)</span></label>
        <select class="form-control form-control-lg" id="tipe_equipment_id" wire:model.live='tipe_equipment_id'>
            <option value="" selected disabled>- pilih tipe equipment -</option>
            <option value="">- pilih semua -</option>
            @foreach ($tipe_equipment as $item)
                <option value="{{ $item->id }}">{{ $item->code ?? '-' }} ({{ $item->name }})</option>
            @endforeach
        </select>
    </div>

    <label for="equipment_ids">Equipment</label>
    <div class="form-group table-responsive mt-1">
        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Equipment Number</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipment as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <input type="checkbox" class="form-check-primary" name="equipment_ids[]"
                                value="{{ $item->id }}" checked>
                        </td>
                        <td class="text-left">{{ $item->code }} ({{ $item->name }})</td>
                        <td>{{ $item->equipment_number ?? '-' }}</td>
                        <td>{{ $item->tipe_equipment->code ?? '-' }}</td>
                    </tr>
                @endforeach
                @if (count($equipment) == 0)
                    <tr>
                        <td colspan="5">
                            There are no equipments selected.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
