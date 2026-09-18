<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PermissionManagement extends Model
{
        use SoftDeletes;
	protected $table = "permission_managements"; //table name
	
	
	
}