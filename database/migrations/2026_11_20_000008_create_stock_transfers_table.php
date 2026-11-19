<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transfer_number')->unique();
            $table->unsignedInteger('from_hub_id');
            $table->unsignedInteger('to_hub_id');
            $table->unsignedInteger('requested_by')->nullable();
            $table->unsignedInteger('received_by')->nullable();
            $table->string('status')->default('Transfer Requested');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stock_transfer_id');
            $table->unsignedInteger('item_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->index('stock_transfer_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
}
