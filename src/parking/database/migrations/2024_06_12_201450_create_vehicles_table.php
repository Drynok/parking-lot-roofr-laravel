<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('parking_spot_id')->constrained();
            $table->string('plate_number');
            $table->timestamps();
            // $table->foreign('parking_spot_id')->references('id')->on('parking_spots')->index();

            $table->index('parking_spot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('vehicles_parking_spot_id_index');
        });
        Schema::dropIfExists('vehicles');
    }
};
