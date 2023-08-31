<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_level', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('rapidx_id')->comment = "User from RAPIDX";
            $table->integer('access_level')->comment = "1 - Administrator, 2 - QAD, 3 - Section Head, 4 - QS Inspector, 5 - Regular User";
            $table->timestamps();
            $table->integer('added_by')->comment = "User from RAPIDX";
            $table->integer('logdel')->comment = "0 - active, 2 - inactive";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_level');
    }
}
