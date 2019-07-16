<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Country extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('geo__country')) {
			Schema::create('geo__country', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->string('name');
				$table->string('nameFull')->default('');
				$table->string('iso2',2)->default('')->comment('ISO 3166-1 Alpha-2');
				$table->string('iso3',3)->default('')->comment('ISO 3166-1 Alpha-3');
				$table->string('isoN',3)->default('')->comment('ISO 3166-1 numeric');
				$table->string('gost7_67',3)->default('')->comment('ГОСТ 7.67-2003');
				$table->string('flag')->default('');
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
		Schema::dropIfExists('geo__country');
	}
}
