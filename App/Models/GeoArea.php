<?php

namespace Knyaz71\Geo\App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoArea extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'geo__areas';

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
		return $this->belongsTo('App\Models\GeoCountry');
	}

	public function region()
	{
		return $this->belongsTo('App\Models\GeoRegion');
	}

	public function localities()
	{
		return $this->hasMany('App\Models\GeoLocality','locality_id');
	}

	public function streets()
	{
		return $this->hasMany('App\Models\GeoLocality','locality_id');
	}

	public function type()
	{
		return $this->belongsTo('App\Models\GeoType');
	}
}
