<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'farmer_manager'])->default('farmer_manager'); // الـ Role افتراضيًا farmer_manager
            $table->boolean('is_active')->default(false); // حقل جديد للحالة (افتراضيًا موقوف)
            $table->dateTime('last_login_attempt')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
