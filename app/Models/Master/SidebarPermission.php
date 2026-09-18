<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SidebarPermission extends Model
{
        use SoftDeletes;
	protected $table = "sidebar_parmissions"; //table name

    public function roleName(){
       return $this->belongsTo('App\Models\Role','role_id');
    }

    public function sidebarName(){
       return $this->belongsTo('App\Models\Sidebar','sidebar_id');
    }



	
}