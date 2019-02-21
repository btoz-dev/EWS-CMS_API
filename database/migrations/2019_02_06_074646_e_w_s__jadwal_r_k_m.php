<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EWSJadwalRKM extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EWS_JADWAL_RKM', function (Blueprint $table) {
            $table->char('rkhCode',25)->primary();
            $table->dateTime('rkhDate');
            $table->char('mandorCode',20);
            $table->char('codeAlojob',10);
            $table->char('codeBlok',10);
            $table->char('codePlot',4);
            $table->char('barisStart',4);
            $table->char('barisEnd',4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EWS_JADWAL_RKM');
    }
}
