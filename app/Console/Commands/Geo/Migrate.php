<?php

namespace App\Console\Commands\Geo;

use Illuminate\Console\Command;

use Knyaz71\Geo\Geo;

class Migrate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'geo:migrate
									{--rollback : Удалить последние установленные Geo таблицы}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Установить Geo таблицы';

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

		if( !$this->option('rollback') ) {
			$geo->migrate();
		}
		else {
			$geo->migrate_rollback();
		}
	}
}
