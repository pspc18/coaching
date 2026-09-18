<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BookInvoice extends Model
{
        use SoftDeletes;
	protected $table = "book_invoices"; //table name




}