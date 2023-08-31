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

             $table->integer('application_id')->comment = "ID from Applications";

            //affected document
            $table->string('document_number');
            $table->string('document_name');
            $table->string('document_revision_number');
            $table->dateTime('document_revision_due_date');
            $table->longText('document_remarks')->nullable();

            //point persons
            $table->integer('person_in_charge')->comment = "ID from RapidX";

             //approver
            $table->integer('affected_document_approver')->nullable()->comment = "ID from RapidX";
            $table->integer('approver_type')->nullable()->comment = "1 - Affected Document, 2 - FMEA, 3 - Control Plan, 4 - Checksheet";
            $table->longText('approval_remarks')->nullable();
            $table->dateTime('approval_datetime')->nullable();

            $table->integer('document_status')->comment = "1 - for Approval, 2 - Approved, 3 - Revised, 4 - Controlled";

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
