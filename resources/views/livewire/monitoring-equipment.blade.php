<div class="row" id="equipmentContainer">
    <div class="mb-3">
        <select class="form-control form-control-lg" wire:model.live='area_id'>
            <option value="" selected disabled>- Filter by Stasiun -</option>
            <option value="">Tampilkan Semua</option>
            @foreach ($area as $item)
                <option value="{{ $item->id }}">{{ $item->sub_lokasi->name ?? '-' }}</option>
            @endforeach
        </select>
    </div>
    @foreach ($monitoring_equipment as $item)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card-device">
                <div
                    class="card-header-device @if ($item->status == 'connected') bg-gradient-success @else bg-gradient-danger @endif fw-bolder text-center">
                    <h4>{{ $item->equipment->name ?? '-' }}</h4>
                </div>
                <div class="card-body-device">
                    <div class="table">
                        <p><strong>Stasiun :</strong>
                            {{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}</p>
                        <p><strong>Corner :</strong> {{ $item->equipment->arah->name ?? '-' }}
                        </p>
                        <p><strong>Status :</strong> <span
                                class="badge @if ($item->status == 'connected') badge-gradient-success @else badge-gradient-danger @endif text-uppercase">
                                {{ $item->status }}
                            </span>
                        </p>
                        <p><strong>Waktu :</strong> {{ $item->waktu }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
