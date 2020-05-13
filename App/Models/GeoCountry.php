<?php

namespace Knyaz71\Geo\App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoCountry extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'geo__countries';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	public function regions()
	{
		return $this->hasMany('App\Models\GeoRegion','country_id');
	}

	public function areas()
	{
		return $this->hasMany('App\Models\GeoArea','area_id');
	}

    public function localities()
    {
        return $this->hasMany('App\Models\GeoLocality','locality_id');
    }

    public function streets()
    {
        return $this->hasMany('App\Models\GeoLocality','locality_id');
    }
}
