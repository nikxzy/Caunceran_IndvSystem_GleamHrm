<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_number')->unique();
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('hub_id');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('driver_id')->nullable();
            $table->string('status')->default('Pending Handoff');
            $table->string('picking_slip_status')->default('Pending');
            $table->string('verification_code')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('picking_slip_generated_at')->nullable();
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('hub_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('item_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->timestamps();

            $table->index('order_id');
        });

        Schema::create('delivery_proofs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('received_by_name');
            $table->string('verification_code')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('delivered_by')->nullable();
            $table->timestamp('delivered_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_proofs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
}
