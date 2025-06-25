<?php

namespace App\Constants;

class SensorTypeConstant
{
    public const LDR = 'ldr';
    public const SOIL_MOISTURE = 'soil_moisture';
    public const HUMIDITY = 'humidity';
    public const TEMPERATURE = 'temperature';

    public static function all(): array
    {
        return [
            self::LDR,
            self::SOIL_MOISTURE,
            self::HUMIDITY,
            self::TEMPERATURE,
        ];
    }
}
