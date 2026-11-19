<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::create('access_audit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id')->nullable()->comment('subject of the event');
            $table->unsignedInteger('actor_id')->nullable()->comment('who performed the event');
            $table->string('event');
            $table->text('description')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedInteger('subject_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('event');
            $table->index('employee_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('access_audit_logs');
    }
}
