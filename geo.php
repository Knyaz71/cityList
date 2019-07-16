<?php

namespace Knyaz71\Geo;

use Artisan;
use DB;

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
	 * Список стран для которых есть данные
	 */
	public function countryList() {
		$list = scandir(__DIR__.'/database/seeds/country');
		unset( $list[array_search('.', $list)], $list[array_search('..', $list)] );
		return $list;
	}

	/**
	 * Список поддерживаемых справочников
	 * @return array [Справочник => Для каких стран]
	 */
	public function directoryList() {
		return [
			'fias' => 'Россия',
		];
	}

	/**
	 * Заполнение данных
	 * @param  string	$country	Страна для которой требуется запонить данные
	 */
	public function seed($country=null) {

		// Заполняем список стран
		(new Database\Seeds\Country() )->run();

		(new Database\Seeds\Type() )->run();

		$error = false;

		if( empty($country) || $country!='countries' ) {

			// Список стран какие данные надо установить
			$countryList = $this->countryList();

			// если указана конкретная страна
			if(!empty($country)) {
				if(in_array($country, $countryList)) {
					$countryList = [$country];
				}
				else{
					$error = true;
					if(!empty($this->output)) { $this->output->error('Страна "'.$country.'" не поддерживается'); }
					else { return ['error'=>'Страна "'.$country.'" не поддерживается']; }
				}
			}

			// если нет ошибок, то устанавливаем подготовленные данные из файлов
			if( !$error ) {
				foreach ($countryList as $country) {
					$region = 'Knyaz71\\Geo\\Database\\Seeds\\Country\\'.ucfirst($country).'\Regions';

					$region = new $region();
					$region->run();
				}
			}
		}
	}
}