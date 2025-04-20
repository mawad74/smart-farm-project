<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('category');
            $table->float('value');
            $table->text('description')->nullable();
            $table->timestamps(); // يضيف created_at و updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_details');
    }
}