<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRealStateAddColumnAddressId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('real_states', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable();

            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('real_states', function (Blueprint $table) {
            $table->dropForeign('real_states_address_id_foreign');
            $table->dropColumn('address_id');
        });
    }
}
