<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator;
use App\Models\Account;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
            public function dashboard(){
                return view('master.dashboard.dashboard');
            }
        
            public function add(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'account_name' => 'required',
                        'bank_name' => 'required',
                        'branch_name' => 'required',
                        'ifsc_code' => 'required',
        
                    ]);
                    // dd($request);
                    $addaccount = new Account;//model name
                    $addaccount->user_id = Session::get('id');
                    $addaccount->name = $request->name;
                    $addaccount->account_name = $request->account_name;
                    $addaccount->bank_name = $request->bank_name;
                    $addaccount->branch_name = $request->branch_name;
                    $addaccount->ifsc_code = $request->ifsc_code;
                    $addaccount->save();
                    return redirect::to('account_list')->with('message', 'Account add Successfully.');
                }
                return view('account.add_bank');
            }
        
            public function account_list(Request $request){
                $allaccount = Account::all();
                return view('account.account_list', ['data' => $allaccount]);
            }
        
            public function edit_bank(Request $request, $id){
                // dd($request);
                $data = Account::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'account_name' => 'required',
                        'bank_name' => 'required',
                        'branch_name' => 'required',
                        'ifsc_code' => 'required',
                    ]);
                    $data->name = $request->name;
                    $data->account_name = $request->account_name;
                    $data->bank_name = $request->bank_name;
                    $data->branch_name = $request->branch_name;
                    $data->ifsc_code = $request->ifsc_code;
                    $data->save();
                    return redirect::to('account_list')->with('message', 'Account Update Successfully.');
                }
                return view('account.edit_bank', ['data' => $data]);
            }
        
            public function delete(Request $request){
                $id = $request->delete_id;
                $students = Account::find($id)->delete();
                return redirect::to('account_list')->with('message', 'Account Delete Successfully.');
            }
}