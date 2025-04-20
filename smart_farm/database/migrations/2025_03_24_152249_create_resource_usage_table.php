<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceUsageTable extends Migration
{
    public function up()
    {
        Schema::create('resource_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->foreignId('plant_id')->nullable()->constrained('plants')->onDelete('set null');
            $table->enum('type', ['water', 'electricity', 'fertilizer']);
            $table->float('value');
            $table->dateTime('timestamp');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_usage');
    }
}