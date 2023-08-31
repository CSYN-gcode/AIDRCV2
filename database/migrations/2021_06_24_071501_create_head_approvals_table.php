<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHeadApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('head_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('application_id');
            $table->integer('head_approval_status')->comment = "1 - Approved, 2 - Disapproved";
            $table->longText('head_approval_remarks')->nullable();

            $table->timestamps();

            $table->integer('logdel',0)->comment = "0 - Active, 1 - Inactive";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('head_approvals');
    }
}
