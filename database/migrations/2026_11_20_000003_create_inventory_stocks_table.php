<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryStocksTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hub_id');
            $table->unsignedInteger('item_id');
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->timestamps();

            $table->unique(['hub_id', 'item_id']);
            $table->index('item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_stocks');
    }
}
