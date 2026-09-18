<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class OtpRequest extends Model
{
        use SoftDeletes;
protected $table = "otp_requests"; //table name

}
