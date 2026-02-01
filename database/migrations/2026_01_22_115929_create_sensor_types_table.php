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
        Schema::create('sensor_types', function (Blueprint $table) {
            $table->id()->comment('Primary Key ของประเภทเซนเซอร์');
            $table->string('type_name', 100)->comment('เช่น Temperature, Soil Moisture');
            $table->string('unit', 20)->comment('เช่น °C, %, Lux');
            $table->string('description', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_types');
    }
};
