<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffectedDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affected_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('application_id')->comment = "id from applications";
            $table->integer('document_id')->comment = "ID from ACDCS";
            $table->string('rev_no')->comment = "from ACDCS when request was created";
            $table->date('revision_due_date');
            $table->integer('document_status')->comment = "1 - For checking, 2 - Changed, 3 - Failed";
            $table->longText('document_status_remarks');
            $table->integer('qad_in_charge')->comment ="ID from session rapidx, where aidrc.accesslevel = 2";
            $table->date('qad_date');
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
        Schema::dropIfExists('affected_documents');
    }
}
