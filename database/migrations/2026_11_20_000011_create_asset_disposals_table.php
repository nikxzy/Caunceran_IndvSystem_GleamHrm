<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetDisposalsTable extends Migration
{
    public function up()
    {
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no')->unique();
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('hub_id');
            $table->integer('quantity');
            $table->string('type')->default('damaged_packaging')->comment('damaged_packaging|scrap_machinery|obsolete');
            $table->text('reason');
            $table->string('disposal_method')->nullable();
            $table->decimal('estimated_value', 12, 2)->default(0);
            $table->unsignedInteger('approved_by')->nullable();
            $table->unsignedInteger('disposed_by')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_disposals');
    }
}
