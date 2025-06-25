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
            $table->string('action'); // مثلاً: login, logout, create_farm, delete_sensor
            $table->enum('status', ['success', 'failed', 'info']);
            $table->text('message')->nullable(); // تفاصيل النشاط
            $table->dateTime('timestamp')->useCurrent(); // وقت حدوث النشاط
            $table->dateTime('logout_time')->nullable(); // وقت تسجيل الخروج إذا كان login
            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
}