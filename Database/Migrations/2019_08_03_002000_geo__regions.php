<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeoRegions extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('geo__regions')) {
			Schema::create('geo__regions', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->smallInteger('country_id')->unsigned()->nullable();
				$table->smallInteger('type_id')->unsigned()->nullable();
				$table->string('name');

				$table->string('iso',10)->default('')->comment('ISO 3166-2');
				$table->uuid('fias')->default('')->unique();
				$table->string('oktmo',20)->default('');
				$table->string('okato',20)->default('');
				$table->string('kladr',20)->default('');

                $table->foreign('country_id')->references('id')->on('geo__countries')->onDelete('cascade');
                $table->foreign('type_id')->references('id')->on('geo__types');
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
		Schema::dropIfExists('geo__regions');
	}
}
