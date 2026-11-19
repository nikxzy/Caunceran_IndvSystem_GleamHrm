<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_number')->unique();
            $table->unsignedInteger('vendor_id')->nullable();
            $table->unsignedInteger('hub_id');
            $table->unsignedInteger('requested_by')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->string('status')->default('Pending Approval');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('hub_id');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_order_id');
            $table->unsignedInteger('item_id');
            $table->integer('quantity');
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->timestamps();

            $table->index('purchase_order_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
}
