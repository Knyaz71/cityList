<?php

namespace Knyaz71\Geo\Database\Seeds\Country\Byelorussia;

use Schema;
use DB;

class Regions
{
	public function run()
	{
		if (Schema::hasTable('geo__country')) {
			if( $country = DB::table('geo__country')->where('name','=','Белоруссия')->first() ) {

				$type = DB::table('geo__type')->get()->keyBy('name');

				$dataList = [
					['type_id'=>$type['Область']->id,	'iso'=>'BY-BR',	'name'=>'Брестская'],
					['type_id'=>$type['Область']->id,	'iso'=>'BY-HO',	'name'=>'Гомельская'],
					['type_id'=>$type['Область']->id,	'iso'=>'BY-HR',	'name'=>'Гродненская'],
					['type_id'=>$type['Область']->id,	'iso'=>'BY-MA',	'name'=>'Могилёвская'],
					['type_id'=>$type['Область']->id,	'iso'=>'BY-MI',	'name'=>'Минская'],
					['type_id'=>$type['Область']->id,	'iso'=>'BY-VI',	'name'=>'Витебская'],
				];

				foreach ($dataList as $data) {
					DB::table('geo__region')
						->updateOrInsert(
							[
								'country_id'=> $country->id,
								'type_id'	=> $data['type_id'],
								'name'		=> $data['name'],
							],
							$data
						);
				}
			}
		}
	}
}