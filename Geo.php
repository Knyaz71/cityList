<?php

namespace Knyaz71\Geo;

use Artisan;

class Geo
{
	public $output;
	public $country;

	public function __construct( $output=null ) {
		$this->output = $output;
	}

	/**
	 * Миграция всех таблиц
	 */
	public function migrate() {
		Artisan::call('migrate', [
									'--path' => 'vendor\\Knyaz71\\Geo\\Database\\Migrations',
								],
								$this->output
			);
	}

	/**
	 * Удаление последних установленных таблиц
	 */
	public function migrate_rollback() {
		Artisan::call('migrate:rollback', [
									'--path' => 'vendor\\Knyaz71\\Geo\\Database\\Migrations',
								],
								$this->output
			);
	}

	/**
	 * Список поддерживаемых справочников
	 * @return array [Страна => Справочник]
	 */
	public function directoryList() {
		return [
			'Russia' => ['fias' => 'XML'],
		];
	}

	/**
	 * Заполнение данных
	 */
	public function seed() {
	    echo 'Заполнение стран.'."\n";
		(new Database\Seeds\Country($this->output))->run();
        echo 'Заполнение типов записей.'."\n";
		(new Database\Seeds\Type($this->output))->run();
	}
}
