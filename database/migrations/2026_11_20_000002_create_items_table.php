<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('type')->default('packaging')->comment('packaging|equipment');
            $table->string('category')->nullable();
            $table->string('unit')->default('pc');
            $table->text('description')->nullable();
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->integer('reorder_level')->default(0);
            $table->boolean('is_loanable')->default(false);
            $table->boolean('requires_preventive_maintenance')->default(false);
            $table->integer('service_interval_days')->nullable();
            $table->date('last_serviced_at')->nullable();
            $table->date('next_service_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
