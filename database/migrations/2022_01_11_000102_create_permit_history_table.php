<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permit_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("template_id")->nullable();
            $table->unsignedBigInteger("permit_type_id");
            $table->unsignedBigInteger("category_id");
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("barangay_id");
            $table->unsignedBigInteger("payment_method_id")->nullable();
            $table->unsignedBigInteger("status_id");
            $table->string("control_number");
            $table->date("release_date")->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('waive_reason')->nullable();
            $table->unsignedSmallInteger('is_waive');

            $table->timestamps();

            $table->foreign('permit_type_id')->references('id')->on('permit_type');
            $table->foreign('category_id')->references('id')->on('permit_category');
            $table->foreign('barangay_id')->references('id')->on('barangays');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('payment_method_id')->references('id')->on('permit_payment_method');
            $table->foreign('status_id')->references('id')->on('permit_status');


        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permit_history');
    }
}
