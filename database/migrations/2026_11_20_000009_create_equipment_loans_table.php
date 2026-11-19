<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentLoansTable extends Migration
{
    public function up()
    {
        Schema::create('equipment_loans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_name');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('equipment_id');
            $table->unsignedInteger('hub_id');
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->string('status')->default('On Loan');
            $table->string('condition_out')->nullable();
            $table->string('condition_in')->nullable();
            $table->unsignedInteger('handled_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('equipment_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('equipment_loans');
    }
}
