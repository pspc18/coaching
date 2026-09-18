<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Setting extends Model
{
        use SoftDeletes;
	protected $table = "settings"; //table name
	
	public function City()
    {
        return $this->belongsTo('App\Models\City','city_id');
    }
    
    	public function Country()
    {
        return $this->belongsTo('App\Models\Country','country_id');
    }
    
    	public function State()
    {
        return $this->belongsTo('App\Models\State','state_id');
    }
    	public function Account()
    {
        return $this->belongsTo('App\Models\Account','account_id');
    }
    
    protected static function booted()
    {
        static::saved(function ($setting) {
            \App\Helpers\helper::clearSettingCache($setting->branch_id ?? null);
        });
        static::deleted(function ($setting) {
            \App\Helpers\helper::clearSettingCache($setting->branch_id ?? null);
        });
    }
}

