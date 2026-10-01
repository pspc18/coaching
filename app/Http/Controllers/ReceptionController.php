<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class ReceptionController extends Controller
{
    public function receptionfile()
    {
        return view('reception.reception_dashboard');
    }


}
