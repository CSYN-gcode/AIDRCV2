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

            $table->integer('application_id')->comment = "ID from Applications";

            $table->integer('checkpoint_similar')->comment = "1 - Compliant, 2 - Non-Compliant";
            $table->integer('checkpoint_alignment')->comment = "1 - Compliant, 2 - Non-Compliant";
            $table->integer('checkpoint_standard')->comment = "1 - Compliant, 2 - Non-Compliant";

            $table->integer('dcc_in_charge')->comment = "ID from users in RapidX";
            $table->dateTime('dcc_validation_date');
            $table->longText('dcc_remarks')->nullable();           

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
