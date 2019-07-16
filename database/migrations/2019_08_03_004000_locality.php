<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Locality extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('geo__locality')) {
			Schema::create('geo__locality', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->bigInteger('country_id')->unsigned()->nullable();
				$table->bigInteger('region_id')->unsigned()->nullable();
				$table->bigInteger('area_id')->unsigned()->nullable();
				$table->bigInteger('type_id')->unsigned()->nullable();
				$table->bigInteger('locality_id')->unsigned()->nullable()->comment('Когда город стал частью другого города');
				$table->string('name');
				$table->boolean('centerRegion')->default(0)->comment('Региональный центр');
				$table->boolean('centerArea')->default(0)->comment('Районный центр');

				$table->uuid('fias')->default('')->unique();
				$table->string('oktmo',20)->default('');
				$table->string('okato',20)->default('');
				$table->string('kladr',20)->default('');

				$table->foreign('country_id')->references('id')->on('geo__country');
				$table->foreign('region_id')->references('id')->on('geo__region');
				$table->foreign('area_id')->references('id')->on('geo__area');
				$table->foreign('locality_id')->references('id')->on('geo__locality');
				$table->foreign('type_id')->references('id')->on('geo__type');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('geo__locality');
	}
}
