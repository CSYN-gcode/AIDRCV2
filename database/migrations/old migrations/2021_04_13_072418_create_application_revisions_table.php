<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_revisions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('application_id');
            $table->string('aidrc_attachment_filename')->nullable();
            $table->string('original_filename')->nullable();
            $table->integer('document_category');
            $table->string('document_number')->nullable();
            $table->string('document_title');
            $table->string('document_revision_number');

            $table->integer('dcc_in_charge');
            $table->dateTime('dcc_validation_datetime');
            $table->longText('dcc_remarks')->nullable();
            
            $table->timestamps();

            $table->integer('logdel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_revisions');
    }
}
