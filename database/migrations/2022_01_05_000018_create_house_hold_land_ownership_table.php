<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHouseHoldLandOwnershipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('house_hold_land_ownership', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("house_hold_id")->nullable();
            $table->unsignedBigInteger("land_ownership_id")->nullable();
            $table->timestamps();

            $table->foreign('house_hold_id')->references('id')->on('house_hold_data');
            $table->foreign('land_ownership_id')->references('id')->on('land_ownership');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('house_hold_land_ownership');
    }
}
