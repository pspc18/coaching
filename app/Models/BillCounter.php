<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillCounter extends Model
{
    protected $fillable = [
        'id', 'type','counter',
    ];

}
