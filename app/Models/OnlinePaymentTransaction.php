<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OnlinePaymentTransaction extends Model
{
        use SoftDeletes;
	protected $table = "online_payment_transactions"; //table name
	
	public function Student()
    {
        return $this->belongsTo('App\Models\Admission','admission_id');
    }	
	
}