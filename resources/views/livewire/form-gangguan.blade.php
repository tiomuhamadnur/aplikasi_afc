<div>
    <div class="form-group">
        <label for="relasi_area_id">Location</label>
        <select class="form-control form-control-lg" id="relasi_area_id" wire:model.live='relasi_area_id' required>
            <option value="" selected disabled>- pilih location -</option>
            @foreach ($area as $item)
                <option value="{{ $item->id }}">
                    {{ $item->sub_lokasi->name ?? '-' }} - ({{ $item->sub_lokasi->code ?? '-' }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="tipe_equipment_id">Equipment Type</label>
        <select class="form-control form-control-lg" id="tipe_equipment_id" wire:model.live='tipe_equipment_id' required>
            <option value="" selected disabled>- pilih equipment type -</option>
            @foreach ($tipe_equipment as $item)
                <option value="{{ $item->id }}">
                    {{ $item->code ?? '-' }} - ({{ $item->name ?? '-' }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="equipment_id">Equipment</label>
        <select class="form-control form-control-lg" id="equipment_id" name="equipment_id" required>
            <option value="" selected disabled>- pilih equipment -</option>
            @foreach ($equipment as $item)
                <option value="{{ $item->id }}">
                    {{ $item->name }} - ({{ $item->code ?? '-' }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="category_id">Category</label>
        <select class="form-control form-control-lg" name="category_id" id="category_id" wire:model.live='category_id'
            required>
            <option value="" selected disabled>- pilih category problem -</option>
            @foreach ($category as $item)
                <option value="{{ $item->id }}">
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>
    {{-- <div class="form-group">
        <label for="problem_id">Problem (P)</label>
        <select class="form-control form-control-lg" name="problem_id" id="problem_id" wire:model.live='problem_id'
            required>
            <option value="" selected disabled>- pilih problem -</option>
            <option value="0">- Other -</option>
            @foreach ($problem as $item)
                <option value="{{ $item->id }}">
                    {{ $item->name ?? '-' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="cause_id">Cause (C)</label>
        <select class="form-control form-control-lg" name="cause_id" id="cause_id" wire:model.live='cause_id' required>
            <option value="" selected disabled>- pilih cause -</option>
            <option value="0">- Other -</option>
            @foreach ($cause as $item)
                <option value="{{ $item->id }}">
                    {{ $item->name ?? '-' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="remedy_id">Remedy (R)</label>
        <select class="form-control form-control-lg" name="remedy_id" id="remedy_id" wire:model.live='remedy_id'
            required>
            <option value="" selected disabled>- pilih remedy -</option>
            <option value="0">- Other -</option>
            @foreach ($remedy as $item)
                <option value="{{ $item->id }}">
                    {{ $item->name ?? '-' }}
                </option>
            @endforeach
        </select>
    </div> --}}
    <div class="form-group">
        <label for="classification_id">Classification</label>
        <select class="form-control form-control-lg" name="classification_id" id="classification_id" required>
            <option value="" selected disabled>- pilih classification -</option>
            @foreach ($classification as $item)
                <option value="{{ $item->id }}">
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
