<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActuatorsTable extends Migration
{
    public function up()
    {
        Schema::create('actuators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->foreignId('plant_id')->constrained('plants')->onDelete('cascade');
            $table->enum('type', ['irrigation_pump', 'ventilation', 'lighting']);
            $table->enum('status', ['active', 'inactive', 'faulty'])->default('active');
            $table->string('action_type')->nullable();
            $table->dateTime('last_triggered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('actuators');
    }
}