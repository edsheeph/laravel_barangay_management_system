<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationalOtherDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educational_other_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("level_id")->nullable();
            $table->string("level_code")->nullable();
            $table->unsignedBigInteger("course_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('educational_other_data');
    }
}
