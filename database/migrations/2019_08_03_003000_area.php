<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Area extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('geo__area')) {
			Schema::create('geo__area', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->bigInteger('country_id')->unsigned()->nullable();
				$table->bigInteger('region_id')->unsigned()->nullable();
				$table->bigInteger('type_id')->unsigned()->nullable();
				$table->string('name');

				$table->uuid('fias')->default('')->unique();
				$table->string('oktmo',20)->default('');
				$table->string('okato',20)->default('');
				$table->string('kladr',20)->default('');

				$table->foreign('country_id')->references('id')->on('geo__country');
				$table->foreign('region_id')->references('id')->on('geo__region');
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
		Schema::dropIfExists('geo__area');
	}
}
