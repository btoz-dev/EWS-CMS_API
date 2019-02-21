<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EWSSubJob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EWS_SUB_JOB', function (Blueprint $table) {
            $table->char('subJobCode',15)->primary();
            $table->char('jobCode',3);
            $table->string('Description',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EWS_SUB_JOB');
    }
}
