<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\InfluxDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Faker\Factory as Faker;

class LogSensorAPIController extends Controller
{
    protected $influx;

    public function __construct(InfluxDBService $influx)
    {
        $this->influx = $influx;
    }

    public function index(Request $request, InfluxDBService $influxService)
    {
        $request->validate([
            'equipment_id' => 'required',
            'range' => 'required',
            'field' => 'nullable',
        ]);

        $equipment_id = $request->equipment_id;
        $range = $request->query($request->range, '-1h');
        $field = $request->field;

        $data = $influxService->readSensorData($range, $equipment_id, $field);

        return response()->json($data);
    }

    public function store(Request $request, InfluxDBService $influxService)
    {
        $request->validate([
            'equipment_id' => 'required',
            'temperature' => 'required',
            'humidity' => 'required',
        ]);

        $equipment_id = $request->equipment_id;
        $temperature = $request->temperature;
        $humidity = $request->humidity;

        $result = $influxService->writeSensorData($equipment_id, (float)$temperature, (float)$humidity);

        return response()->json(['message' => $result]);
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function deleteData(string $measurement = 'sensor_data')
    {
        $url = env('INFLUXDB_URL') . '/api/v2/delete?' . http_build_query([
            'bucket' => env('INFLUXDB_BUCKET'),
            'org' => env('INFLUXDB_ORG'),
        ]);

        $response = Http::withToken(env('INFLUXDB_TOKEN'))
            ->post($url, [
                'start' => '1970-01-01T00:00:00Z',
                'stop' => '2100-01-01T00:00:00Z',
                'predicate' => '_measurement="' . $measurement . '"',
            ]);

        if ($response->failed()) {
            throw new \Exception('Failed to delete data: ' . $response->body());
        }

        return $response->body();
    }
}
