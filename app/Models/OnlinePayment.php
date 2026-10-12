<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlinePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'online_payments';

    protected $fillable = [
        'admission_id',
        'branch_id',
        'session_id',
        'merchant_order_id',
        'phonepe_order_id',
        'amount',
        'amount_paise',
        'payment_status',
        'payment_mode',
        'phonepe_transaction_id',
        'invoice_id',
        'request_payload',
        'response_payload',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paise' => 'integer',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function invoice()
    {
        return $this->belongsTo(\app\Models\fees\FeesDetailsInvoices::class, 'invoice_id');
    }
}

// Bi-directional class aliases for full case-insensitivity on all environments (Linux/Windows)
if (!class_exists('App\Models\OnlinePayment', false)) {
    class_alias('app\Models\OnlinePayment', 'App\Models\OnlinePayment');
}
if (!class_exists('app\Models\OnlinePayment', false)) {
    class_alias('App\Models\OnlinePayment', 'app\Models\OnlinePayment');
}
