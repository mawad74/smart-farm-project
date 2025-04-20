<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemMetricsTable extends Migration
{
    public function up()
    {
        Schema::create('system_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->enum('type', ['uptime', 'sensor_status', 'communication_error']);
            $table->float('value');
            $table->dateTime('timestamp');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_metrics');
    }
}