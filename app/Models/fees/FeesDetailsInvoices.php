<?php

namespace App\Models\fees;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FeesDetailsInvoices extends Model
{
        use SoftDeletes;
	protected $table = "fees_details_invoices"; //table name
}