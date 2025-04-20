<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->foreignId('plant_id')->nullable()->constrained('plants')->onDelete('set null');
            $table->enum('type', ['watering', 'fertilizing', 'soil_aeration', 'other']);
            $table->enum('status', ['pending', 'completed']);
            $table->float('completion_rate')->nullable();
            $table->integer('time_taken')->nullable(); // In seconds
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}