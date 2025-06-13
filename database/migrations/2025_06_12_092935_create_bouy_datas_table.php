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
        Schema::create('bouy_datas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('transmit_time')->nullable();
            $table->unsignedBigInteger('device_id');
            $table->string('fLat')->nullable();
            $table->string('fLon')->nullable();
            $table->boolean('entered_water')->nullable();
            $table->string('left_water')->nullable();
            $table->string('resurfaced')->nullable();
            $table->boolean('scheduled')->nullable();
            $table->string('remains_at_surface')->nullable();
            $table->string('moved')->nullable();
            $table->string('fH20Temp')->nullable();
            $table->string('nTimeToNxtUpd')->nullable();
            $table->string('fDepthMean')->nullable();
            $table->string('nSOC')->nullable();
            $table->string('low_battery')->nullable();
            $table->string('nDiveCount')->nullable();
            $table->string('fVelocity')->nullable();
            $table->string('fDepthMax')->nullable();
            $table->string('nDiveSeconds')->nullable();
            $table->boolean('is_backup')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouy_datas');
    }
};
