<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlCommandsTable extends Migration
{
    public function up()
    {
        Schema::create('control_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actuator_id')->constrained('actuators')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('command_type');
            $table->dateTime('executed_at');
            $table->boolean('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('control_commands');
    }
}