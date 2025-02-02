<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\TransGangguanRemedy;
use Illuminate\Http\Request;

class TransGangguanRemedyController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string',
            'remedy_other' => 'required|string'
        ]);

        $data = TransGangguanRemedy::where('uuid', $request->uuid)->firstOrFail();

        $data->update([
            'remedy_other' => $request->remedy_other,
        ]);

        return redirect()->route('gangguan.edit', $data->gangguan->uuid)->withNotify('Data remedy berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string',
        ]);

        $data = TransGangguanRemedy::where('uuid', $request->uuid)->firstOrFail();

        $validate = TransGangguanRemedy::where('gangguan_id', $data->gangguan_id)->count();
        if($validate <= 1)
        {
            return redirect()->route('gangguan.edit', $data->gangguan->uuid)->withNotifyerror('Data remedy tidak boleh kosong dalam data gangguan');
        }

        $data->forceDelete();

        return redirect()->route('gangguan.edit', $data->gangguan->uuid)->withNotify('Data remedy berhasil dihapus');
    }
}
