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
    /**
     * @var Sensor
     */
    private $sensor;

    public function __construct(Farm $farm)
    {
        $this->farm = $farm;
    }

    public static function make(Farm $farm): SensorService
    {
        return new self($farm);
    }

    public function setOrCreateLdrSensor(): SensorService
    {
        $this->sensor = $this->getOrCreateSensor(SensorTypeConstant::LDR, 'LDR Sensor', 'Lux');

        return $this;
    }

    public function setOrCreateSoilMoistureSensor(): SensorService
    {
        $this->sensor = $this->getOrCreateSensor(SensorTypeConstant::SOIL_MOISTURE, 'Soil Moisture Sensor', 'Percentage');

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

    public function storeSensorData(float $value): Model
    {
        return $this->sensor->sensorData()->create([
            'value' => $value,
        ]);
    }

    public function setOrCreateTemperatureSensor(): SensorService
    {
        $this->sensor = $this->getOrCreateSensor(SensorTypeConstant::TEMPERATURE, 'Temperature Sensor', '°C');

        return $this;
    }

    public function setOrCreateHumiditySensor(): SensorService
    {
        $this->sensor = $this->getOrCreateSensor(SensorTypeConstant::HUMIDITY, 'Humidity Sensor', '%');

        return $this;
    }
}
