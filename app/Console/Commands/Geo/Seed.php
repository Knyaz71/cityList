<?php

namespace App\Console\Commands\Geo;

use Illuminate\Console\Command;

use Knyaz71\Geo\Geo;

class Seed extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'geo:seed
									{--dList 		: Список поддерживаемых справочников}
									{--d|directory=	: Загрузить данные из файлов справочника}
									{--p|path=		: Путь к папке с файлами справочника от корня сайта, по умолчанию "/vendor/knyaz71/geo/name_of_handbook"}
									{--cList		: Список готовых стран}
									{--c|country=	: Название страны для установки. Если укзать "countries", то установится только список стран}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Заполнить данные';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$geo = new Geo( $this->getOutput() );

		if( $this->option('cList') ) {
			$this->line('Список готовых стран');
			foreach ($geo->countryList() as $country) {
				$this->line('- '.$country);
			}
		}

		if( $this->option('dList') ) {
			$this->line('Список поддерживаемых справочников');
			$dList = collect($geo->directoryList());
			$this->table(['Справочник','Страны'], $dList->keys()->zip( $dList->values() ) );
		}

		if( empty($this->option('cList')) && empty($this->option('dList')) ) {
			if( $this->option('directory') ) {
				$directory = $this->option('directory');
				if( $directory[0]=='=' )	{ $directory = mb_substr($directory, 1); }

				if( !isset($geo->directoryList()[$directory]) ) {
					$this->error('Справочник "'.$directory.'" не поддерживается');
					if($directory=='List'){
						$this->info('Возможно Вы забыли поставить еще одно тире (-) перед опцией');
					}
				}
				else {
					$path = $this->option('path');
					if( $path[0]=='=' )			{ $path = mb_substr($path, 1); }

					$d = 'Knyaz71\\Geo\\' . $directory;
					$d = new $d(  $this->getOutput() );
					$d->run( $path );
				}
			}
			else{
				$country = $this->option('country');
				if( $country[0]=='=' )	{ $country = mb_substr($country, 1); }

				$geo->seed( $country );
			}
		}
	}
}
