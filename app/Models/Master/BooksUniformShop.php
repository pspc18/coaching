<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BooksUniformShop extends Model
{
        use SoftDeletes;
	protected $table = "books_uniform_shops"; //table name
}