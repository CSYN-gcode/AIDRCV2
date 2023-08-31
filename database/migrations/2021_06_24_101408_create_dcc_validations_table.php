<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDccValidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcc_validations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('application_id');
            $table->integer('dcc_checkpoint_similar');
            $table->integer('dcc_checkpoint_alignment');
            $table->integer('dcc_checkpoint_standard');
            $table->integer('dcc_validation_judgement');
            $table->longText('dcc_validation_remarks')->nullable();

            $table->integer('dcc_in_charge');

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
        Schema::dropIfExists('dcc_validations');
    }
}
