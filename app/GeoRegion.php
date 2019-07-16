<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoRegion extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'geo__region';

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

	public function country()
	{
		return $this->belongsTo('App\GeoCountry');
	}

	public function areas()
	{
		return $this->hasMany('App\GeoArea','area_id');
	}

	public function localities()
	{
		return $this->hasMany('App\GeoLocality','locality_id');
	}

	public function type()
	{
		return $this->belongsTo('App\GeoType');
	}
}
