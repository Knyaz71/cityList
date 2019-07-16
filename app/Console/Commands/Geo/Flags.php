<?php

namespace App\Console\Commands\Geo;

use Illuminate\Console\Command;

class Flags extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'geo:flags
									{--c|country= : Копирование флагов только указанной страны, например russia}
									{--p|path : Копирование флагов в указанную дирикторию [по умолчанию, /public/image/flags]}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Копирование флагов в папку "/public/images/flags/" в соответствующие поддериктории';

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
		//
	}
}
