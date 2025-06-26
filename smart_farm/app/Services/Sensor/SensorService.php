<?php

namespace App\Services\Sensor;

use App\Constants\SensorTypeConstant;
use App\Models\Farm;
use App\Models\Sensor;
use Illuminate\Database\Eloquent\Model;

class SensorService
{
    /**
     * @var Farm
     */
    private $farm;

    public function __construct(Farm $farm)
    {
        $this->farm = $farm;
    }

    public static function make(Farm $farm): SensorService
    {
        return new self($farm);
    }

    public function storeLdrSensorData(float $value): SensorService
    {
        $sensor = $this->getOrCreateSensor(SensorTypeConstant::LDR, 'LDR Sensor', 'Lux');

        $this->storeSensorData($sensor, $value);

        return $this;
    }

    public function storeSoilMoistureSensorData(float $value): SensorService
    {
        $sensor = $this->getOrCreateSensor(SensorTypeConstant::SOIL_MOISTURE, 'Soil Moisture Sensor', 'Percentage');

        $this->storeSensorData($sensor, $value);

        return $this;
    }

    private function getOrCreateSensor(string $type, string $name, ?string $unit = null): Model
    {
        return $this->farm->sensors()->firstOrCreate(
            [
                'type' => $type,
                'name' => $name,
            ],
            [
                'unit' => $unit,
                'status' => 'active',
            ]
        );
    }

    public function storeSensorData(Sensor $sensor, float $value): Model
    {
        return $sensor->sensorData()->create([
            'value' => $value,
        ]);
    }

    public function storeTemperatureSensorData(float $value): SensorService
    {
        $sensor = $this->getOrCreateSensor(SensorTypeConstant::TEMPERATURE, 'Temperature Sensor', '°C');

        $this->storeSensorData($sensor, $value);

        return $this;
    }

    public function storeHumiditySensorData(float $value): SensorService
    {
        $sensor = $this->getOrCreateSensor(SensorTypeConstant::HUMIDITY, 'Humidity Sensor', '%');

        $this->storeSensorData($sensor, $value);

        return $this;
    }
}
