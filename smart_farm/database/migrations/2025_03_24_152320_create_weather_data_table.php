<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeatherDataTable extends Migration
{
    public function up()
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->float('temperature');
            $table->float('rainfall');
            $table->float('wind_speed');
            $table->dateTime('timestamp');
            $table->timestamps(); // يضيف created_at و updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('weather_data');
    }
}