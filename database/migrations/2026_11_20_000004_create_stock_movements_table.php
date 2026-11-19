<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hub_id');
            $table->unsignedInteger('item_id');
            $table->string('type');
            $table->integer('quantity')->comment('signed: positive in, negative out');
            $table->string('reference_type')->nullable();
            $table->unsignedInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('employee_id')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'hub_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
}
