<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EWSLokTanaman extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EWS_LOK_TANAMAN', function (Blueprint $table) {
            $table->char('codeTanaman',20)->primary();
            $table->string('Description',128);
            $table->char('codeBlok',8);
            $table->char('plot',4);
            $table->char('baris',4);
            $table->char('noTanam',4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EWS_LOK_TANAMAN');
    }
}
