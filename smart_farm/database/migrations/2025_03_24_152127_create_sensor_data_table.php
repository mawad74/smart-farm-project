<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorDataTable extends Migration
{
    public function up()
    {
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensors')->onDelete('cascade');
            $table->float('soil_moisture_raw');
            $table->float('soil_moisture_percentage');
            $table->string('moisture_status');
            $table->float('light_level_raw');
            $table->float('light_percentage');
            $table->string('light_status');
            $table->float('temperature');
            $table->float('humidity');
            $table->dateTime('timestamp');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sensor_data');
    }
}