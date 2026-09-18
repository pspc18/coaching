<?php

namespace App\Models;

use App\Models\User;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Expense extends Model
{
    use SoftDeletes;
        
    protected $fillable = [
        'user_id', 'session_id', 'branch_id', 'name', 'date', 'quantity',
        'rate', 'amount', 'payment_mode_id', 'total_amt', 'attachment', 'description',
        'category_id', 'invoice_no', 'created_by', 'payee_name', 'bill_no',
        'payment_reference', 'expense_type', 'recurring_frequency', 'payment_status'
    ];
	protected $table = "expenses"; //table name
   
    public static function totalExpense(){
        $data=Expense::where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->sum('amount');
        return $data;
    }
    
    public static function thisMonthExpense(){
        $data=Expense::where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->whereMonth('date',date('m'))->sum('amount');
        return $data;
    }
    
    public static function todayExpense(){
        $data=Expense::where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->where('date',date('Y-m-d'))->sum('amount');
        return $data;
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    protected static function booted()
    {
        static::saved(function ($expense) {
            \App\Helpers\helper::clearExpenseCache($expense->branch_id ?? null, $expense->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($expense->branch_id ?? null, $expense->session_id ?? null);
        });
        static::deleted(function ($expense) {
            \App\Helpers\helper::clearExpenseCache($expense->branch_id ?? null, $expense->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($expense->branch_id ?? null, $expense->session_id ?? null);
        });
    }
}
