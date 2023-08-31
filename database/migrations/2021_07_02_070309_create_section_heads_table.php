<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_heads', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('rapidx_user_id');
            $table->integer('approver_type')->comment = "1 - Production Head, 2 - QC Head, 3 - Engineering Head, 4 - Section Head";

            $table->timestamps();

            $table->integer('logdel')->comment = "0 - Active, 1 - Inactive";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_heads');
    }
}
