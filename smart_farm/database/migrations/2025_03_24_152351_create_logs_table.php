<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('farm_id')->nullable()->constrained('farms')->onDelete('set null');
            $table->string('action');
            $table->enum('status', ['success', 'failed', 'info']);
            $table->text('message')->nullable();
            $table->dateTime('timestamp');
            $table->dateTime('logout_time')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
}