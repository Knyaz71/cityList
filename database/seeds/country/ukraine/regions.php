<?php

namespace Knyaz71\Geo\Database\Seeds\Country\Ukraine;

use Schema;
use DB;

class Regions
{
	public function run()
	{
		if (Schema::hasTable('geo__country')) {
			if( $country = DB::table('geo__country')->where('name','=','Украина')->first() ) {

				$type = DB::table('geo__type')->get()->keyBy('name');

				$dataList = [
					['type_id'=>$type['Область']->id,	'iso'=>'UA-05',	'name'=>'Винницкая'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-07',	'name'=>'Волынская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-09',	'name'=>'Луганская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-12',	'name'=>'Днепропетровская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-14',	'name'=>'Донецкая'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-18',	'name'=>'Житомирская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-56',	'name'=>'Ровненская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-21',	'name'=>'Закарпатская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-23',	'name'=>'Запорожская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-26',	'name'=>'Ивано-Франковская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-32',	'name'=>'Киевская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-35',	'name'=>'Кировоградская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-46',	'name'=>'Львовская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-48',	'name'=>'Николаевская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-51',	'name'=>'Одесская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-53',	'name'=>'Полтавская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-59',	'name'=>'Сумская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-61',	'name'=>'Тернопольская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-63',	'name'=>'Харьковская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-65',	'name'=>'Херсонская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-68',	'name'=>'Хмельницкая'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-71',	'name'=>'Черкасская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-74',	'name'=>'Черниговская'],
					['type_id'=>$type['Область']->id,	'iso'=>'UA-77',	'name'=>'Черновицкая'],
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