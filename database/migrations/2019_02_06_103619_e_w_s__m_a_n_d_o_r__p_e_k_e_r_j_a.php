<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EWSMANDORPEKERJA extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EWS_MANDOR_PEKERJA', function (Blueprint $table) {
            $table->increment('id');
            $table->char('codeMandor',20);
            $table->char('codePekerja',20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EWS_MANDOR_PEKERJA');
        //
    }
}
