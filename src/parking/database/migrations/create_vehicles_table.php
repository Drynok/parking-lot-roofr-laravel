<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkingSpotsTable extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('parking_spot_id')->constrained();
            $table->timestamps();
            // $table->foreign('parking_spot_id')->references('id')->on('parking_spots')->index();

            $table->index('parking_spot_id');
        });

    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('vehicles_parking_spot_id_index');
        });
        Schema::dropIfExists('vehicles');
    }
}
