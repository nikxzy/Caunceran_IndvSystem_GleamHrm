<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceLogsTable extends Migration
{
    public function up()
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('hub_id');
            $table->string('type')->default('corrective')->comment('preventive|corrective|incident');
            $table->string('status')->default('Open');
            $table->unsignedInteger('reported_by')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['item_id', 'status']);
            $table->index('scheduled_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_logs');
    }
}
