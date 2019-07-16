<?php

namespace Knyaz71\Geo\Database\Seeds\Country\Russia;

class TulSeeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		if(Schema::hasTable('geo__area')) {
			if($country = DB::table('geo__region')->where('nameShort','=','Россия')->first()) {
				if( $region = DB::table('geo__region')->where('country_id','=',$country->id)->where('name','=','Тульская')->first() ) {
					
					$type = DB::table('geo__type')->where('group','=','area')->get()->keyBy('name');
					
					$dataList = [
						['type_id'=>$type->{'Городской округ'}->id,	'name'=>'Алексин',			'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Городской округ'}->id,	'name'=>'Донской',			'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Городской округ'}->id,	'name'=>'Ефремов',			'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Городской округ'}->id,	'name'=>'Новогуровский',	'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Городской округ'}->id,	'name'=>'Новомосковск',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Городской округ'}->id,	'name'=>'Славный',			'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Городской округ'}->id,	'name'=>'Тула',				'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],

						['type_id'=>$type->{'Район'}->id,			'name'=>'Арсеньевский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Белёвский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Богородицкий',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Венёвский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Воловский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Дубенский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Заокский',			'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Каменский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Кимовский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Киреевский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Куркинский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Одоевский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Плавский',			'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Суворовский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Тёпло-Огарёвский',	'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Узловский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Чернский',			'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Щёкинский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
						['type_id'=>$type->{'Район'}->id,			'name'=>'Ясногорский',		'fias' => '',	'oktmo' => '',	'okato' => '',	'kladr' => ''],
					];

					foreach ($dataList as $data) {
						DB::table('geo__area')
							->updateOrInsert(
								[
									'country_id'=> $country->id,
									'region_id'	=> $region->id,
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
}
