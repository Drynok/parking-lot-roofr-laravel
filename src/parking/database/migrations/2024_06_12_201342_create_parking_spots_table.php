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
        Schema::create('parking_spots', function (Blueprint $table) {
            $table->id();
            $table->boolean('occupied')->default(false);
            $table->foreignId('parking_lot_id')->constrained();
            $table->foreignId('vehicle_id')->nullable()->constrained();
            $table->integer('plate_number')->nullable(); // int for simplicity
            $table->index('parking_lot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->dropIndex('parking_spots_parking_lot_id_index');
        });
        Schema::dropIfExists('parking_spots');
    }
};
