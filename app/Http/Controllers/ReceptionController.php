<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CallLog;
use App\Models\Master\EnquiryStatus;
use Illuminate\Validation\Rule;
use Auth;

class ReceptionController extends Controller
{
    public function receptionfile()
    {
        return view('reception.reception_dashboard');
    }

   public function createCallLog(Request $request)
{
    if ($request->isMethod('post')) {
        // agar POST aaya to save karega
        $rules = [
            'call_type' => ['required', Rule::in(['Outgoing', 'Incoming'])],
            'calling_purpose_id' => [
                'required',
                Rule::exists('enquiry_status', 'id')->where(function ($query) {
                    $query->where('type', 'calling_purpose');
                })
            ],
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|digits:10',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'follow_up_date' => 'nullable|date|after_or_equal:date',
            'note' => 'nullable|string|max:2000'
        ];

        $validated = $request->validate($rules);
        $validated['created_by'] = Auth::id();
        CallLog::create($validated);

        return redirect('callLog/add')->with('message', 'Call log saved successfully.');
    }

    // agar GET aaya to form + list show karega
    $callingPurposes = EnquiryStatus::where('type', 'calling_purpose')->orderBy('name')->get();
    $callLogs = CallLog::with('callingPurpose')->latest()->get();

    return view('reception.call_log.add', compact('callingPurposes', 'callLogs'));
}


    
    
    
    public function callLogDelete(Request $request)
{
    $callLog = CallLog::find($request->delete_id);
    $callLog->delete(); // soft delete karega
    return redirect('callLog/add')->with('message', 'Call log saved successfully.');
}

}
