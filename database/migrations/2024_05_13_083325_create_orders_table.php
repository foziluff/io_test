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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('startLongitude');
            $table->string('startLatitude');
            $table->string('endLongitude');
            $table->string('endLatitude');
            $table->double('amount');
            $table->string('status');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('drivers');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
