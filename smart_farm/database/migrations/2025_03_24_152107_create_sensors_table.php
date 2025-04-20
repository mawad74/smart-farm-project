<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorsTable extends Migration
{
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->foreignId('plant_id')->constrained('plants')->onDelete('cascade');
            $table->enum('type', ['temperature', 'humidity', 'soil_moisture', 'ph', 'nutrient']);
            $table->float('value')->nullable();
            $table->enum('status', ['active', 'inactive', 'faulty'])->default('active');
            $table->string('location')->nullable();
            $table->float('light_intensity')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sensors');
    }
}