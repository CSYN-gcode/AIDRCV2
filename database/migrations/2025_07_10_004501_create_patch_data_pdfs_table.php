<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatchDataPdfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patch_data_pdfs', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->tinyInteger('status')->default(0)->comment ='0 - Pending, 1 - Done';
            $table->unsignedBigInteger('application_id')->comment ='ID from applications(table)';
            $table->unsignedBigInteger('patch_data')->nullable();
            $table->unsignedBigInteger('font_size')->nullable();
            $table->string('page_no')->nullable();
            $table->string('coordinates')->nullable();
            $table->string('remarks')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->softDeletes()->comment ='0-Active, 1-Deleted';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patch_data_pdfs');
    }
}
