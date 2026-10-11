<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceLeaveApprovalController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->to(url('/'))->with('error', 'Leave management has been disabled.');
    }

    public function action(Request $request)
    {
        return redirect()->back()->with('error', 'Leave management has been disabled.');
    }
}
