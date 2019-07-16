<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Type extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('geo__type')) {
			Schema::create('geo__type', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->string('name');
				$table->string('nameShort');
				$table->string('fias');
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
		Schema::dropIfExists('geo__type');
	}
}
