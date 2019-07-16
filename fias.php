<?php

namespace Knyaz71\Geo;

use DB;

class Fias
{
	public $output;
	public $path;

	public function __construct( $output=null ) {
		$this->output	= $output;
		$this->path		= __DIR__.DIRECTORY_SEPARATOR.'fias';
	}

	public function run( $path=null )
	{
		if( !empty($path) ) { $this->path = $path; }
		
		$list = scandir( $this->path );
		unset( $list[array_search('.', $list)], $list[array_search('..', $list)] );
		foreach ($list as $key => $fileName) {
			if( !preg_match('~ADDROB\d+.DBF~i', $fileName) ) {
				unset( $list[$key] );
			}
		}

		// $list = ['ADDROB71.DBF'];

		if(empty($list)) {
			if(!empty($this->output)) { $this->output->error('Нет файлов в папке '.$path); }
			else { return ['error'=>'Нет файлов в папке '.$path]; }
		}
		else {
			foreach (DB::table('geo__type')->get() as $type) {
				foreach (explode('|', substr($type->fias, 1, -1)) as $val) {
					$this->typeList[$val] = $type->id;
				}
			}

			$country = DB::table('geo__country')->where('name','=','Россия')->first();
			if( empty($country) ){
				if(!empty($this->output)) { $this->output->error('В таблице "geo__country" нет России'); }
				else { return ['error'=>'В списке "geo__country" нет России']; }
			}
			else{
				foreach ($list as $fileName) {
					$this->dbf( $country, $fileName );
				}
			}
		}
	}

	public function dbf($country, $fileName)
	{
		$errorList = [];

		$file = dbase_open(__DIR__.'/fias/'.$fileName, 0);

		if ($file===false) {
			echo 'Ошибка при открытии файла '.$fileName;
		}
		else {
			$record_numbers = dbase_numrecords($file);

			if(!empty($this->output)) {
				$bar = $this->output->createProgressBar($record_numbers);
				$bar->setFormat( '%current%/%max% [%bar%] %percent:3s%% %message% %memory:6s%' );
				$bar->start();
			}

			for ($i = 1; $i <= $record_numbers; $i++) {
				$row = mb_convert_encoding( dbase_get_record($file, $i), 'UTF-8', 'cp866' );
				
				if( $i==1 ) {
					if(!empty($this->output)) {
						$bar->setMessage( '('.$fileName.') '.trim($row[15]) );		// OFFNAME
					}
					else{
						echo '<div>',date('Y-m-d H:i:s'),' - ',trim($row[15]),'</div>';		// OFFNAME
					}
				}

				// echo '<pre>' . print_r($row, true) . '</pre>';

				if ($row[34] == 1) {	// LIVESTATUS

					if( trim($row[15])=='Чувашская Республика -' ){	// OFFNAME
						$row[15]	= 'Чувашская';		// OFFNAME
						$row[25]	= 'Респ';			// SHORTNAME
					}

					$data = [
						'country_id'	=> $country->id,
						'type_id'		=> $this->typeList[ trim($row[3]).'-'.trim($row[25]) ],	// AOLEVEL-SHORTNAME
						'name'			=> trim($row[15]),						// OFFNAME
						'fias'			=> trim($row[1]),						// AOGUID
						'okato'			=> trim($row[16]),						// OKATO
						'oktmo'			=> trim($row[17]),						// OKTMO
						'kladr'			=> trim($row[8]),						// CODE
					];

					$parentId = trim($row[19]);	// PARENTGUID

					switch ($row[3]) {	// AOLEVEL
						case 1:	// уровень региона
								DB::table('geo__region')
									->updateOrInsert(
										[
											'country_id'	=> $data['country_id'],
											'fias'			=> $data['fias'],
										],
										$data
									);
						break;

						case 3:	// уровень района
								if(!$region = DB::table('geo__region')->where('fias',$parentId)->first()) {
									echo '<div>'.$fileName.' - Регион не подобран</div>';
								}
								else{
									$data['region_id'] = $region->id;

									DB::table('geo__area')
										->updateOrInsert(
											[
												'country_id'	=> $data['country_id'],
												'fias'			=> $data['fias'],
											],
											$data
										);
								}
						break;
						
						case 35:// уровень городских и сельских поселений
						case 4:	// уровень города
						case 5:	// уровень внутригородской территории (устаревшее)
						case 6:	// уровень населенного пункта
								
								if(!$area = DB::table('geo__area')->where('fias',$parentId)->first()) {

									if($region = DB::table('geo__region')->where('fias',$parentId)->first()) {
										$data['region_id'] = $region->id;

										DB::table('geo__area')
													->updateOrInsert(
														[
															'country_id'	=> $data['country_id'],
															'fias'			=> $data['fias'],
														],
														$data
													);

										$area = DB::table('geo__area')->where('fias',$data['fias'])->first();
									}
									elseif($locality = DB::table('geo__locality')->where('fias',$parentId)->first()) {
										$area = DB::table('geo__area')->where('id',$locality->area_id)->first();
										$data['locality_id'] = $locality->id;
									}
								}
								$data['area_id'] = $area->id;
								$data['region_id'] = $area->region_id;

								if( empty($area) && empty($region) ) {
									$errorList[] = ['error'=>$fileName.' ('.$data['name'].') - Район не подобран'];
								}
								else{
									switch ($row[6]) {	// CENTSTATUS
										case 1:	$data['centerArea'] = 1; break;		// Районный центр
										case 2:	$data['centerRegion'] = 1; break;	// Региональный центр
										case 3:		// Региональный и районный центр
											$data['centerRegion'] = 1;
											$data['centerArea'] = 1;
										break;
									}

									DB::table('geo__locality')
										->updateOrInsert(
											[
												'country_id'	=> $data['country_id'],
												'fias'			=> $data['fias'],
											],
											$data
										);
								}
						break;
					}
				}

				if(!empty($this->output)) {
					$bar->advance();
				}
			}


			if(!empty($this->output)) {
				$bar->finish();
				$this->output->writeLn('');
			}

			dbase_close($file);

			if(!empty($this->output)) { 
				foreach ($errorList as $error) {
					$this->output->error( $error );
				}
			}
			else {
				return $errorList;
			}
		}
	}
}