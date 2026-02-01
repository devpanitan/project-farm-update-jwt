<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('actuator_commands', function (Blueprint $table) {
            $table->id()->comment('Primary Key');
            $table->string('uuid', 45);
            $table->unsignedBigInteger('auto_rule_id')->nullable()->comment('FK: ชี้ไปที่ auto_rules(id)');
            $table->string('actuator_prefix', 50)->nullable()->comment('รหัสอุปกรณ์สั่งการ');
            $table->integer('pin')->nullable()->comment('ช่อง Pin ที่สั่งงาน');
            $table->string('val', 50)->nullable()->comment('ค่าที่สั่ง (เช่น ON/OFF)');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uuid')->references('uuid')->on('iot_devices')->onDelete('cascade');
            // When auto_rules table is created, you can add this foreign key
            // $table->foreign('auto_rule_id')->references('id')->on('auto_rules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actuator_commands');
    }
};
