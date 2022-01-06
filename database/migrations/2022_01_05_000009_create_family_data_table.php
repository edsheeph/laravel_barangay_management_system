<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('family_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->unsignedBigInteger("personal_data_id")->nullable();
            $table->unsignedBigInteger("relationship_type_id")->nullable();
            $table->string("first_name");
            $table->string("middle_name")->nullable();
            $table->string("last_name");
            $table->date("birth_date");
            $table->string('contact_no', 15);
            $table->boolean("same_address");
            $table->string("address");
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('personal_data_id')->references('id')->on('personal_data');
            $table->foreign('relationship_type_id')->references('id')->on('relationship_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('family_data');
    }
}
