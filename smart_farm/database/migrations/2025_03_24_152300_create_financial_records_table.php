<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->enum('type', ['resource_cost', 'labor_cost', 'revenue', 'profit_loss']);
            $table->float('value');
            $table->text('description')->nullable();
            $table->dateTime('timestamp');
            $table->timestamps(); // يضيف created_at و updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_records');
    }
}