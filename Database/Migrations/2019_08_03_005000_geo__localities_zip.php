<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeoLocalitiesZip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo__localities_zip', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('locality_id')->unsigned()->comment('id населенного пункта');
            $table->bigInteger('zip_id')->unsigned()->comment('id почтового индекса');

            $table->foreign('locality_id')->references('id')->on('geo__localities')->onDelete('cascade');
            $table->foreign('zip_id')->references('id')->on('zip')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo__localities_zip');
    }
}
