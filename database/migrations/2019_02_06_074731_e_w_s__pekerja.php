<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EWSPekerja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EWS_PEKERJA', function (Blueprint $table) {
            $table->char('codePekerja',20)->primary();
            $table->string('namaPekerja',128);
            $table->integer('idRole');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EWS_PEKERJA');
    }
}
