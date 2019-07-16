<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoCountry extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'geo__country';

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
		return $this->hasMany('App\GeoRegion','country_id');
	}

	public function areas()
	{
		return $this->hasMany('App\GeoArea','area_id');
	}

	public function localities()
	{
		return $this->hasMany('App\GeoLocality','locality_id');
	}
}
