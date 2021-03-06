<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFieldsToIncidentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_data', function (Blueprint $table) {
            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->dateTime('incident_date_resolved', $precision = 0)->nullable();
            $table->string('incident_no')->nullable();
            $table->longText('incident_message')->nullable()->change();
            $table->longText('incident_resolution')->nullable();
            $table->dateTime('incident_date_action_taken', $precision = 0)->nullable();

            $table->foreign('incident_status_id')->references('id')->on('incident_status');
            $table->foreign('barangay_id')->references('id')->on('barangays');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_data', function (Blueprint $table) {
            //
        });
    }
}
