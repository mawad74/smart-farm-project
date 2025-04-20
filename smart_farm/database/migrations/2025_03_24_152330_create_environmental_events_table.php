<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnvironmentalEventsTable extends Migration
{
    public function up()
    {
        Schema::create('environmental_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->enum('type', ['heatwave', 'heavy_rain', 'frost']);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('severity', ['low', 'medium', 'high']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('environmental_events');
    }
}