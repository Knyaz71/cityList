<?php

namespace Knyaz71\Geo\App\Models;

use Illuminate\Database\Eloquent\Model;

class Zip extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'zip';

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

    public function locations()
    {
        return $this->belongsToMany('App\Models\GeoLocality','geo__localities_zip','zip_id','locality_id');
    }
}
