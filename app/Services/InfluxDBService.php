<?php

namespace App\Services;

use Exception;
use InfluxDB2\ApiException;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

class InfluxDBService
{
    protected $client;
    protected $writeApi;
    protected $bucket;
    protected $org;

    public function __construct()
    {
        $this->client = new Client([
            'url' => env('INFLUXDB_URL', 'http://rs.jakartamrt.co.id:51823'),
            'token' => env('INFLUXDB_TOKEN'),
        ]);

        $this->writeApi = $this->client->createWriteApi();
        $this->bucket = env('INFLUXDB_BUCKET', 'default');
        $this->org = env('INFLUXDB_ORG', 'mrtjakarta');
    }

    public function writeSensorData(string $equipment_id, float $temperature, float $humidity): string
    {
        try {
            $point = Point::measurement('sensor_data')
                ->addTag('equipment_id', $equipment_id)
                ->addField('temperature', $temperature)
                ->addField('humidity', $humidity)
                ->time(microtime(true), WritePrecision::S);

            $this->writeApi->write($point, WritePrecision::S, $this->bucket, $this->org);

            return 'Data written successfully to InfluxDB.';
        } catch (Exception $e) {
            return 'Failed to write data to InfluxDB: ' . $e->getMessage();
        }
    }

    public function readSensorData(string $range = '-1h', string $equipment_id, string $field = null): array
    {
        $query = <<<FLUX
            from(bucket: "{$this->bucket}")
            |> range(start: $range)
            |> filter(fn: (r) => r._measurement == "sensor_data" and r.equipment_id == "$equipment_id")
            |> keep(columns: ["_time", "_field", "_value", "equipment_id"])
            FLUX;

        if($field)
        {
            $query .= ' |> filter(fn: (r) => r["_field"] == "' . $field . '")';
        }

        $tables = $this->client->createQueryApi()->query($query, $this->org);
        $results = [];

        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                $results[] = [
                    'time' => $record->getTime(),
                    'equipment_id' => $record->values['equipment_id'],
                    'field' => $record->getField(),
                    'value' => $record->getValue(),
                ];
            }
        }

        return $results;
    }

    public function __destruct()
    {
        $this->client->close();
    }
}
