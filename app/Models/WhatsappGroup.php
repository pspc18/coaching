<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WhatsappGroup extends Model
{
        use SoftDeletes;
	protected $table = "whatsapp_groups"; //table name
	

    
    
}