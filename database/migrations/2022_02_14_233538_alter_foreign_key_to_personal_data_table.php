<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterForeignKeyToPersonalDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personal_data', function (Blueprint $table) {
            $table->dropForeign('personal_data_province_id_foreign');
            $table->dropForeign('personal_data_municipality_id_foreign');
            $table->foreign('province_id')
                ->references('id')
                ->on('ref_provinces')
                ->change();
            $table->foreign('municipality_id')
                ->references('id')
                ->on('ref_cities')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personal_data', function (Blueprint $table) {
            //
        });
    }
}
