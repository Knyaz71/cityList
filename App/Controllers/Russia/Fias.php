<?php

namespace Knyaz71\Geo\App\Controllers\Russia;

use Knyaz71\Geo\App\Models\GeoType;
use Knyaz71\Geo\App\Models\GeoCountry;
use Knyaz71\Geo\App\Models\GeoRegion;
use Knyaz71\Geo\App\Models\GeoArea;
use Knyaz71\Geo\App\Models\GeoLocality;
use Knyaz71\Geo\App\Models\GeoStreet;
use Knyaz71\Geo\App\Models\GeoHouse;
use Knyaz71\Geo\App\Models\Zip;

class Fias
{
    use \Knyaz71\Geo\Config;

    public $output;
    public $country;
    public $typeList;
    public $postponed;    // массив записей, которые не удалось обработать из-за того что небыло родительской записи

    public function __construct( $output=null ) {
        $this->output	= $output;
        $this->path	   .= 'temp'.DIRECTORY_SEPARATOR.'russia'.DIRECTORY_SEPARATOR.'fias';
    }

    public function run( $path=null )
    {
        if( !empty($path) ) { $this->path = $path; }

        $list = scandir( $this->path );
        unset( $list[array_search('.', $list)], $list[array_search('..', $list)] );
        $sort = [];
        foreach ($list as $key => $fileName) {
            /*if(strpos($fileName,'AS_SOCRBASE')!==false) {
                $sort[0] = ['method'=>'socrbase', 'fileName'=>$fileName];
            }
            elseif(strpos($fileName,'AS_ADDROBJ')!==false) {
                $sort[1] = ['method'=>'addrobj', 'fileName'=>$fileName];
            }
            else*/if(strpos($fileName,'AS_HOUSE')!==false) {
                $sort[2] = ['method'=>'house', 'fileName'=>$fileName];
            }
        }
        ksort($sort);

        if(empty($sort)) {
            if(!empty($this->output)) { $this->output->error('Нет необходимых файлов в папке '.$this->path); }
            else { return ['error'=>'Нет необходимых файлов в папке '.$this->path]; }
        }
        else {
            foreach (GeoType::get() as $type) {
                foreach (explode('|', substr($type->fias, 1, -1)) as $val) {
                    $this->typeList[$val] = $type->id;
                }
            }

            $this->country = GeoCountry::where('name','=','Россия')->first();
            if( empty($this->country) ){
                if(!empty($this->output)) { $this->output->error('В таблице "geo__country" нет России'); }
                else { return ['error'=>'В списке "geo__country" нет России']; }
            }
            else{
                foreach($sort as $data){
                    echo 'Обработка файла ',$data['fileName'],'.',"\n";
                    $this->{$data['method']}( $data['fileName'] );
                }
            }
        }
    }

    public function socrbase($fileName){
        $this->reader = new \XMLReader();

        $this->reader->open($this->path.DIRECTORY_SEPARATOR.$fileName);
        while ($this->reader->read()) {
            while ($this->reader->read()) {
                if($this->reader->name == 'AddressObjectType') {
                    $res = $this->parseXmlBlock();
                }
            }
        }
        $this->reader->close();
    }

    public function addrobj($fileName){
        $this->reader = new \XMLReader();
        $this->postponed = [];
        $res = [
            'region' => 0,
            'area' => 0,
            'locality' => 0,
            'street' => 0,
        ];

        // регионы
        $i = 0;
        echo '- Начата обработка регионов',"\n";
        $this->reader->open($this->path.DIRECTORY_SEPARATOR.$fileName);
        while ($this->reader->read()) {
            while ($this->reader->read()) {
                ++$i;
                if($i%100000==0){
                    echo date('Y-m-d H:i:s').' - '.$i."\n";
                }

                if($this->reader->name == 'Object') {
                    $row = $this->parseXmlBlock();
                    // dd($row);
                    // dump($row);

                    if($row['AOLEVEL']==1){
                        $this->addrobj_row($row);
                        ++$res['region'];
                    }
                }
            }
        }
        $this->reader->close();

        // районы
        $i = 0;
        echo '- Начата обработка районов',"\n";
        $this->reader->open($this->path.DIRECTORY_SEPARATOR.$fileName);
        while ($this->reader->read()) {
            while ($this->reader->read()) {
                ++$i;
                if($i%100000==0){
                    echo date('Y-m-d H:i:s').' - '.$i."\n";
                }

                if($this->reader->name == 'Object') {
                    $row = $this->parseXmlBlock();
                    // dd($row);
                    // dump($row);

                    if($row['AOLEVEL']==3){
                        $this->addrobj_row($row);
                        ++$res['area'];
                    }
                }
            }
        }
        $this->reader->close();

        // населенные пункты и улицы
        $i = 0;
        echo '- Начата обработка населенных пунктов и улиц',"\n";
        $this->reader->open($this->path.DIRECTORY_SEPARATOR.$fileName);
        while ($this->reader->read()) {
            while ($this->reader->read()) {
                ++$i;
                if($i%100000==0){
                    echo date('Y-m-d H:i:s').' - '.$i."\n";
                }

                if($this->reader->name == 'Object') {
                    $row = $this->parseXmlBlock();
                    // dd($row);
                    // dump($row);

                    if( in_array($row['AOLEVEL'],[35,4,5,6 , 7]) ){
                        $this->addrobj_row($row);

                        if(in_array($row['AOLEVEL'],[35,4,5,6])){
                            ++$res['locality'];
                        }
                        elseif( $row['AOLEVEL']==7 ){
                            ++$res['street'];
                        }
                    }
                }
            }
        }
        $this->reader->close();

        echo '- Обработано ',$i,' записей.',"\n";
        echo '--- Регионов - ',$res['region'],'.',"\n";
        echo '--- Районов - ',$res['area'],'.',"\n";
        echo '--- Населенных пунктов - ',$res['locality'],'.',"\n";
        echo '--- Улиц - ',$res['street'],'.',"\n";

        if(!empty($this->postponed)) {
            echo 'Пропущенные записи:',"\n";
            dd($this->postponed);
        }
    }
    public function addrobj_row($row){
        if ($row['LIVESTATUS'] == 1) {

            if( trim($row['OFFNAME'])=='Чувашская Республика -' ){
                $row['OFFNAME']	= 'Чувашская';
                $row['SHORTNAME']	= 'Респ';
            }

            $data = [
                'country_id'	=> $this->country->id,
                'type_id'		=> $this->typeList[ trim($row['AOLEVEL']).'-'.trim($row['SHORTNAME']) ],
                'name'			=> trim($row['OFFNAME']),
                'fias'			=> trim($row['AOGUID']),
                'okato'			=> isset($row['OKATO']) ? trim($row['OKATO']) : '',
                'oktmo'			=> isset($row['OKTMO']) ? trim($row['OKTMO']) : '',
                'kladr'			=> isset($row['CODE']) ? trim($row['CODE']) : '',
            ];

            $parentId = null;
            if($row['AOLEVEL']!=1) {
                $parentId = trim($row['PARENTGUID']);
            }

            $postponed = false;
            switch ($row['AOLEVEL']) {
                case 1:	// уровень региона
                    GeoRegion::updateOrCreate(
                        [
                            'country_id'	=> $data['country_id'],
                            'fias'			=> $data['fias'],
                        ],
                        $data
                    );
                    $postponed = true;
                    break;

                case 3:	// уровень района
                    if(!$region = GeoRegion::where('fias',$parentId)->first()) {
                        $this->postponed[$parentId][] = $row;
                    }
                    else{
                        $data['region_id'] = $region->id;

                        GeoArea::updateOrCreate(
                            [
                                'country_id'	=> $data['country_id'],
                                'fias'			=> $data['fias'],
                            ],
                            $data
                        );
                        $postponed = true;
                    }
                    break;

                case 35:// уровень городских и сельских поселений
                case 4:	// уровень города
                case 5:	// уровень внутригородской территории (устаревшее)
                case 6:	// уровень населенного пункта
                    if(!$area = GeoArea::where('fias',$parentId)->first()) {

                        if($region = GeoRegion::where('fias',$parentId)->first()) {
                            $data['region_id'] = $region->id;

                            GeoArea::updateOrCreate(
                                [
                                    'country_id'	=> $data['country_id'],
                                    'fias'			=> $data['fias'],
                                ],
                                $data
                            );

                            $area = GeoArea::where('fias',$data['fias'])->first();
                        }
                        elseif($locality = GeoLocality::where('fias',$parentId)->first()) {
                            $area = GeoArea::where('id',$locality->area_id)->first();
                            $data['locality_id'] = $locality->id;
                        }
                    }

                    if( empty($area) && empty($region) ) {
                        $this->postponed[$parentId][] = $row;
                    }
                    else{
                        $data['area_id'] = $area->id;
                        $data['region_id'] = $area->region_id;

                        switch ($row['CENTSTATUS']) {
                            case 1:	$data['centerArea'] = 1; break;		// Районный центр
                            case 2:	$data['centerRegion'] = 1; break;	// Региональный центр
                            case 3:		// Региональный и районный центр
                                $data['centerRegion'] = 1;
                                $data['centerArea'] = 1;
                                break;
                        }

                        GeoLocality::updateOrCreate(
                            [
                                'country_id'	=> $data['country_id'],
                                'fias'			=> $data['fias'],
                            ],
                            $data
                        );
                        $postponed = true;
                    }
                    break;

                case 7:	// уровень улицы
                    if(!$locality = GeoLocality::where('fias',$parentId)->first()) {
                        $this->postponed[$parentId][] = $row;
                    }
                    else{
                        $data['locality_id'] = $locality->id;
                        $data['area_id'] = $locality->area_id;
                        $data['region_id'] = $locality->region_id;

                        GeoStreet::updateOrCreate(
                            [
                                'country_id'	=> $data['country_id'],
                                'fias'			=> $data['fias'],
                            ],
                            $data
                        );
                        $postponed = true;
                    }
                    break;
            }

            if($postponed && !empty($this->postponed[$data['fias']])) {
                foreach ($this->postponed[$data['fias']] as $row){

                    $this->addrobj_row($row);
                }
                unset($this->postponed[$data['fias']]);
            }
        }
    }

    public function house($fileName){
        $this->reader = new \XMLReader();
        $i = 0;

        $this->reader->open($this->path.DIRECTORY_SEPARATOR.$fileName);
        while ($this->reader->read()) {
            while ($this->reader->read()) {
                if($this->reader->name == 'House') {
                    ++$i;
                    if($i%100000==0){
                        echo date('Y-m-d H:i:s').' - '.$i."\n";
                    }

                    $row = $this->parseXmlBlock();
                    // dd($row);
                    // dump($row);

                    if ($row['ENDDATE'] > date('Y-m-d')) {
                        if(!empty($row['POSTALCODE'])) {
                            $data = [
                                'country_id'	=> $this->country->id,
                                // 'type_id'		=> $this->typeList[ trim($row['AOLEVEL']).'-'.trim($row['SHORTNAME']) ],
                                // 'name'			=> trim($row['OFFNAME']),
                                // 'fias'			=> trim($row['AOGUID']),
                                'okato'			=> isset($row['OKATO']) ? trim($row['OKATO']) : '',
                                'oktmo'			=> isset($row['OKTMO']) ? trim($row['OKTMO']) : '',
                                // 'kladr'			=> isset($row['CODE']) ? trim($row['CODE']) : '',
                            ];

                            $parentId = trim($row['AOGUID']);

                            if( !empty($row['POSTALCODE']) ) {
                                $locality = null;
                                if( $street = GeoStreet::where('fias',$parentId)->first() ) {
                                    $locality = $street->locality;
                                }
                                else{
                                    $locality = GeoLocality::where('fias',$parentId)->first();
                                }

                                if(!empty($locality)){
                                    if( empty($locality->zips()->where('zip',$row['POSTALCODE'])->first()) ){
                                        if( $zip = Zip::firstOrCreate(['zip'=>$row['POSTALCODE']]) ) {
                                            $locality->zips()->attach( $zip->id );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->reader->close();
    }








    private function parseXmlBlock() {
        $result = [];

        switch ($this->reader->nodeType) {
            case 1:
                $nodeName = $this->reader->name;
                if ($this->reader->hasAttributes) {
                    $attributeCount = $this->reader->attributeCount;

                    for ($i = 0; $i < $attributeCount; $i++) {
                        $this->reader->moveToAttributeNo($i);
                        $result[$this->reader->name] = $this->reader->value;
                    }
                    $this->reader->moveToElement();
                }
            break;

            case 3:
            case 4:
                $result = $this->reader->value;
            break;
        }

        return $result;
    }
}
