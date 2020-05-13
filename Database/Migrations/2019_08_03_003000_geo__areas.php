<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeoAreas extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('geo__areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('country_id')->unsigned()->nullable();
            $table->bigInteger('region_id')->unsigned()->nullable();
            $table->smallInteger('type_id')->unsigned()->nullable();
            $table->string('name');

            $table->uuid('fias')->default('')->unique();
            $table->string('oktmo',20)->default('');
            $table->string('okato',20)->default('');
            $table->string('kladr',20)->default('');

            $table->foreign('country_id')->references('id')->on('geo__countries')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('geo__regions')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('geo__types');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('geo__areas');
	}
}
