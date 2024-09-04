<div>
    <div class="form-group mt-4">
        <label for="form_id">Jenis Checksheet</label>
        <select class="form-control form-control-lg" id="form_id" wire:model.live='form_id'>
            <option value="">- pilih jenis checksheet -</option>
            @foreach ($form as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    @if (count($parameter) > 0)
        <div class="p-2 my-3">
            <h3 class="text-center">Form Checksheet</h3>
            <hr class="mb-5">
            @foreach ($parameter as $item)
                @if ($item->tipe == 'option')
                    <div class="form-group">
                        <label class="fw-bolder">{{ $loop->iteration }}. {{ $item->name }}
                            @if ($item->photo_instruction != null)
                                <span>
                                    <button type="button" class="btn btn-gradient-primary btn-rounded btn-icon"
                                        title="Show Instruction Photo" data-bs-toggle='modal'
                                        data-bs-target='#photoModal'
                                        data-photo='{{ asset('storage/' . $item->photo_instruction) }}'>
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </span>
                            @endif
                        </label>
                        <select class="form-control form-control-lg" name="values[]" required>
                            <option value="">- pilih value -</option>
                            @php
                                $option = json_decode($item->option_form->value);
                            @endphp
                            @foreach ($option as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="parameter_ids[]" value="{{ $item->id }}" hidden>
                    </div>
                @else
                    <div class="form-group">
                        <label class="fw-bolder">{{ $loop->iteration }}. {{ $item->name }} @if ($item->tipe == 'number')
                                ({{ $item->min_value }} - {{ $item->max_value }}{{ $item->satuan->code ?? '' }})
                            @endif
                            @if ($item->photo_instruction != null)
                                <span>
                                    <button type="button" class="btn btn-gradient-primary btn-rounded btn-icon"
                                        title="Show Instruction Photo" data-bs-toggle='modal'
                                        data-bs-target='#photoModal'
                                        data-photo='{{ asset('storage/' . $item->photo_instruction) }}'>
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </span>
                            @endif
                        </label>
                        <input type="{{ $item->tipe }}" class="form-control" name="values[]" autocomplete="off"
                            accept="image/*" required
                            placeholder="input {{ $item->code }} {{ $item->satuan_id ? '(' . $item->satuan->name . ')' : '' }}"
                            step="0.01">
                        <input type="text" name="parameter_ids[]" value="{{ $item->id }}" hidden>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
