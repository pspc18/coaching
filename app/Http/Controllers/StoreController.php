<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\StoreItem;
use App\Models\StoreItemRequest;
use App\Models\BillCounter;
use App\Models\StoreBillingDetail;
use Session;
use Hash;
use Helper;
use File;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends Controller

{
    public function storeDashboard(){
        return view('store_management.storeDashboard');
    }
    
   public function addStoreItem(Request $request)
{
    $data = StoreItem::get();

    if ($request->isMethod('post')) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
        ]);

        if ($request->id) {
            // Update existing item
            $item = StoreItem::find($request->id);
        } else {
            // Create new item
            $item = new StoreItem();
        }

        $item->name = $validatedData['name'];
        $item->rate = $validatedData['rate'];
        $item->qty = $validatedData['qty'];
        $item->session_id = Session::get('session_id');
        $item->branch_id = Session::get('branch_id');
        $item->user_id = Session::get('id');
        $item->save();

        return response()->json(['id' => $item->id]);
    }

    return view('store_management.addStoreItem', ['data' => $data]);
}
    
    public function viewStoreRequest(){
        
       
       $data = StoreItemRequest::leftJoin('admissions', 'store_item_student_requests.admission_id', '=', 'admissions.id')
    ->leftJoin('class_types', 'store_item_student_requests.class_type_id', '=', 'class_types.id')
    ->select('store_item_student_requests.*', 'admissions.first_name', 'admissions.mobile', 'class_types.name as class_name');
    
    if(Session::get('role_id')  == 3)
    {
        $data = $data->where('admission_id',Session::get('id'));
    }
    
    $data = $data->groupBy('receipt_no')->get();
        return view('store_management.viewStoreRequest',['data'=>$data]);
    }
    
    public function addStationaryRequest(Request $request){
        
        $search['admissionNo'] = $request->admissionNo ?? '';
        
       
        if ($request->isMethod('post')) {
            if($request->submit =='search'){
            }else{
                 $BillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('type', 'StoreReceipt')->first();
                $BillCounterNo = null;
                if (!empty($BillCounter)) {
                    $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                    $BillCounterNo = $counter + 1;
                }
            foreach ($request->items as $item) {
                $new = new StoreItemRequest;
                $new->session_id = Session::get('session_id'); 
                $new->branch_id = Session::get('branch_id');
                $new->user_id = Session::get('id');
                $new->admission_id = $request->admission_id;
                $new->class_type_id =$request->class_type_id;
                $new->store_item_id = $item['item'];
                $new->qty = $item['quantity'];
                $new->price = $item['price'];
                $new->date = date('Y-m-d');
                $new->receipt_no = $BillCounterNo;
                $new->save();
                $decrement = StoreItem::find($item['item']);
                $decrement->qty -=  $item['quantity'];
                $decrement->save();
            }
            
            $enteredAmount = $request->enteredAmount ?? 0;
             
            if($enteredAmount > 0){
           $pay = new StoreBillingDetail;
   $pay->user_id = Session::get('id');
                $pay->session_id =Session::get('session_id');
                $pay->branch_id = Session::get('branch_id');
                $pay->fees_counter_id = Session::get('counter_id');
    $pay->admission_id = $request->admission_id;
// $pay->class_type_id =$request->class_type_id;
   $pay->receipt_no = $BillCounterNo;
   $pay->amount = $enteredAmount;
   $pay->date = date('Y-m-d');
   $pay->save();
            }
             $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                $BillCounter->counter = $BillCounterNo;
                $BillCounter->save();
            return response()->json(['success' => true, 'message' => 'Data saved successfully']);
        }
        }
        return view('store_management.addStationaryRequest',['search'=>$search]);
    }

    public function editInvoiceInventory(Request $request,$receipt_no){
        
        $data = StoreBillingDetail::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('receipt_no',$receipt_no)->get();
        
        
           if ($request->isMethod('post')) {
               
               if(!empty($request->id))
               {
                   foreach($request->id as $key=>$id)
                   {
                       
               $row = StoreBillingDetail::find($id);
               $row->date = $request->date[$key];
               $row->amount = $request->amount[$key];
               $row->save();
                   }
               }
               return redirect::to('editInvoiceInventory/' . $receipt_no)->with('message', 'Inventory Invoice Updated.');
           }
         return view('store_management.editInvoice',['data'=>$data,'receipt'=>$receipt_no]);
         
         
        
    }
    public function deleteInvoiceInventory(Request $request){
        
        $delete_id = $request->delete_id ?? '';
        
        $data = StoreBillingDetail::where('id',$delete_id)->first()->delete();
        
        return redirect()->back();
        
    }
    public function deleteReceiptInventory(Request $request){
        
        $delete_receipt= $request->delete_receipt?? '';
        
       StoreItemRequest::where('receipt_no',$delete_receipt)->delete();
       StoreBillingDetail::where('receipt_no',$delete_receipt)->delete();
        
        return redirect()->back();
        
    }
    public function storeReceipt(Request $request,$receipt_no){
        
        
        $data = StoreItemRequest::leftJoin('admissions', 'store_item_student_requests.admission_id', '=', 'admissions.id')
    ->leftJoin('class_types', 'store_item_student_requests.class_type_id', '=', 'class_types.id')
    ->select('store_item_student_requests.*', 'admissions.admissionNo' ,'admissions.first_name','admissions.last_name','admissions.father_name', 'admissions.mobile', 'class_types.name as class_name')->where('receipt_no',$receipt_no)->get();
        
        return view('print_file.store_print.store_receipt',['data'=>$data]);
    }
    public function deleteStoreItem(Request $request){
        StoreItem::where('id',$request->id)->delete();
        return response()->json(['id' => $request->id]);
    }
}