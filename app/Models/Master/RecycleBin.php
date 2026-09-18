<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RecycleBin extends Model
{
        use SoftDeletes;
	protected $table = "recycle_bins"; //table name

   
    }
	
	
		
