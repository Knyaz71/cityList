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
									{--l|list 		: Список поддерживаемых стран}
									{--d|directory=	: Загрузить данные из файлов справочника}
									{--p|path=		: Путь к папке с файлами справочника от корня сайта, по умолчанию "/vendor/knyaz71/geo/Temp/_Country_name_/_Name_of_directory_/*"}
									{--c|country=	: Название страны для установки. Если укзать "*", то установятся все поддерживаемые страны}';

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

		if( $this->option('list') ) {
			$this->line('Список поддерживаемых стран');

			$list = $geo->directoryList();
            $res = [];
			foreach( $list as $country => $refs ) {
                foreach( $refs as $ref => $format ) {
                    $res[] = [
                        $country,
                        $ref,
                        $format,
                    ];
                }
            }

			$this->table(['Страна','Справочник','Формат'], $res);
		}
        else {
            $geo->seed();

			if( $this->option('country') && $this->option('directory') ) {
				$directory = $this->option('directory');
				if( !empty($directory) && $directory[0]=='=' )    { $directory = mb_substr($directory, 1); }

				$country = $this->option('country');
				if( !empty($country) && $country[0]=='=' )  { $country = mb_substr($country, 1); }

				if( !isset($geo->directoryList()[ucfirst($country)][$directory]) ) {
					$this->error('У "'.$country.'" справочник "'.$directory.'" не поддерживается');
				}
				else {
					$path = $this->option('path');
					if( !empty($path) && $path[0]=='=' ) { $path = mb_substr($path, 1); }

					$d = 'Knyaz71\\Geo\\App\\Controllers\\'.ucfirst($country).'\\' .ucfirst($directory);
					$d = new $d(  $this->getOutput() );
					$d->run( $path );
				}
			}
		}
	}
}
