<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQsValidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qs_validations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('application_id');
            $table->integer('qs_validation_status')->comment = "1 - Approved, 2 - Disapproved";
            $table->longText('qs_validation_remarks')->nullable();

            $table->timestamps();

            $table->integer('logdel')->comment = "0 - Active, 1- Inactive";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qs_validations');
    }
}
