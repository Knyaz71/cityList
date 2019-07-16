<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoArea extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'geo__area';

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

	public function region()
	{
		return $this->belongsTo('App\GeoRegion');
	}

	public function cities()
	{
		return $this->hasMany('App\GeoLocality','locality_id');
	}

	public function type()
	{
		return $this->belongsTo('App\GeoType');
	}
}
