<?php

namespace App\Http\Controllers\inventory;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Setting;
use App\Models\Inventory;
use App\Models\Admission;
use App\Models\InventoryItem;
use App\Models\InventoryDetail;
use App\Models\InventorySale;
use App\Models\InventorySaleDetail;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use Session;
use Hash;
use Helper;
use Redirect;
use Str;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesInvantoryController extends Controller
{
 

 

    

    

    public function SalesEditInvantory(Request $request, $id)
    {
        $data = InventorySale::select('inventory_sales.*','admissions.first_name','admissions.last_name')
                                           ->leftjoin('admissions','admissions.id','inventory_sales.admission_id')->where('inventory_sales.id',$id)->first();
        $dataDetail = InventorySaleDetail::select('inventory_sale_details.*','inventory_items.name as inventory_item_name','admissions.first_name','admissions.last_name')
                                           ->leftjoin('inventory_items','inventory_items.id','inventory_sale_details.inventory_item_id')
                                           ->leftjoin('admissions','admissions.id','inventory_sale_details.admission_id')->where('inventory_sale_id',$id)->get();
        if ($request->isMethod("post")) {
            $request->validate([
                
            ]);
            
                $data->branch_id = Session::get("branch_id");
                $data->session_id = Session::get("session_id");
                $data->user_id = Session::get("id");
                $data->invoice_no = $request->invoice_no;
                $data->date = $request->date;
                $data->admission_id = $request->admission_id;
                $data->mobile = $request->mobile;
                $data->gstin = $request->gstin;
                $data->total_qty = $request->total_qty;
                $data->total_amount = $request->net_amount;
                $data->save();
                $addinvantory_id = $data->id;

                if(!empty($request->inventory_item_id)){            
                    foreach($request->inventory_item_id as $key => $item_id){
                        if(isset($request->inventory_sale_detail_id[$key])){
                            $addDetai =  InventorySaleDetail::find($request->inventory_sale_detail_id[$key]);
                            
                               InventoryItem::where('id',$request->inventory_item_id[$key])->decrement('available_stock',$request->qty[$key]);
                            
                        }else{
                           $addDetai = new InventorySaleDetail(); //model name 
                           InventoryItem::where('id',$request->inventory_item_id[$key])->increment('available_stock',$request->qty[$key]);
                        }
                        
                        $addDetai->branch_id = Session::get("branch_id");
                        $addDetai->session_id = Session::get("session_id");
                        $addDetai->user_id = Session::get("id");
                        $addDetai->inventory_item_id = $request->inventory_item_id[$key];
                        $addDetai->inventory_sale_id = $addinvantory_id;
                        $addDetai->admission_id = $request->admission_id;
                        $addDetai->qty = $request->qty[$key];
                        $addDetai->amount = $request->amount[$key];
                        $addDetai->date = $request->date;
                        $addDetai->save();
                        

                    }
                }


            return redirect::to("sale_inventory_view")->with(
                "message",
                "Invantory Sale Update Successfully."
            );
        }
        return view("invantory.sales_invantory.edit", ["data" => $data,"dataDetail"=>$dataDetail]);
    }

    public function SalesViewInvantory(Request $request)
    {
        $data = InventorySale::select('inventory_sales.*','admissions.admissionNo','admissions.first_name','admissions.last_name','admissions.class_type_id','class_types.name as class_name')
                                           ->leftjoin('admissions','admissions.id','inventory_sales.admission_id')
                                             ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
                                           ->where("inventory_sales.session_id",Session::get("session_id"));

        if (Session::get("role_id") > 1) {
            $invantoryItem = $data->where("inventory_sales.branch_id",Session::get("branch_id"));
        }

        $invantoryItem = $data->orderBy("inventory_sales.id", "DESC")->get();
        return view("invantory.sales_invantory.view", ["data" => $invantoryItem]);
    }
    
     public function getInvantoryItemQtyCheck(Request $request){
       $getitem = InventoryItem::find($request->inventory_item_id);
       
           if($getitem->available_stock > $request->qty){
            $response = 0;   
           }else{
            $response = 1;      
           }
    
       echo json_encode($response); 
   }
//      public function getAutoCompleteStudent(Request $request){
//        $getitem = Admission::where('first_name', 'LIKE','%'.$request->search. '%')->orwhere('last_name', 'LIKE','%'.$request->search. '%')->get(['id','last_name','first_name','mobile']);
         
//             foreach($getitem as $data){
         
//             $response[] = array("value"=>$data['name'],"label"=>$data['first_name'].' '.$data['last_name'],"id"=>$data['id'],"mobile"=>$data['mobile']);
//            }
    
//        echo json_encode($response);  
//    }



public function SalesAddInvantory(Request $request)
    {
        if ($request->isMethod("post")) {
            
            $addsale = new InventorySale();
            $addsale->branch_id = Session::get("branch_id");
            $addsale->session_id = Session::get("session_id");
            $addsale->user_id = Session::get("id");
            $addsale->invoice_no = $request->invoice_no;
            $addsale->date = $request->date;
            $addsale->admission_id = $request->admission_id;
            $addsale->mobile = $request->mobile;
            $addsale->gstin = $request->gstin;
            $addsale->total_qty = $request->total_qty;
            $addsale->total_amount = $request->net_amount;
            $addsale->save();
            $addinvantory_id = $addsale->id;

            // सिर्फ Check किये गए Items ही सेव होंगे
            if(!empty($request->inventory_item_id)){            
                foreach($request->inventory_item_id as $item_id){
                    $addDetai = new InventorySaleDetail();
                    $addDetai->branch_id = Session::get("branch_id");
                    $addDetai->session_id = Session::get("session_id");
                    $addDetai->user_id = Session::get("id");
                    $addDetai->inventory_item_id = $item_id;
                    $addDetai->inventory_sale_id = $addinvantory_id;
                    $addDetai->admission_id = $request->admission_id;
                    $addDetai->qty = $request->qty[$item_id] ?? 1;
                    $addDetai->amount = $request->amount[$item_id] ?? 0;
                    $addDetai->date = $request->date;
                    $addDetai->save();
                }
            }
            
            $url = '/sale_inventory_print/'.$addsale->id;
            echo "<script type='text/javascript'>window.open('$url', '_blank');</script>";
            return Redirect::to("sale_inventory_view")->with("message", "Sales Inventory added Successfully.");
        }

        // सभी Items को View में भेजें (यदि InventoryItem Model अलग नाम से है तो बदल लें)
        $inventoryItems = InventoryItem::get();

        return view("invantory.sales_invantory.add", compact('inventoryItems'));
    }

public function getStudentsForInventory(Request $request)
{
    $classId = $request->class_id;

    $students = Admission::select(
            'admissions.id', 
            'admissions.first_name', 
            'admissions.last_name', 
            'admissions.admissionNo', 
            'admissions.mobile',
            'admissions.father_name',
            'admissions.mother_name',
            'class_types.name as class_name'
        )
        ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
        ->where('admissions.class_type_id', $classId)
        ->get();

    return response()->json($students);
}

public function getStudentSaleHistory(Request $request)
    {
        $admissionId = $request->admission_id;

        $history = InventorySale::where('admission_id', $admissionId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($history);
    }

public function getAutoCompleteStudent(Request $request)
{
    $search = $request->search;
    $classId = $request->class_id; // AJAX से आ रही Class ID गेट करें

    // Query बिल्ड करना शुरू करें
    $query = Admission::select(
            'admissions.id', 
            'admissions.first_name', 
            'admissions.last_name', 
            'admissions.admissionNo', 
            'admissions.mobile', 
            'class_types.name as class_name'
        )
        ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
        ->where(function($q) use ($search) {
            $q->where('admissions.first_name', 'LIKE', '%' . $search . '%')
              ->orWhere('admissions.last_name', 'LIKE', '%' . $search . '%')
              ->orWhere('admissions.admissionNo', 'LIKE', '%' . $search . '%');
        });

    // अगर User ने Class Select की है, तो सिर्फ उसी Class का डेटा लाएं
    if (!empty($classId)) {
        $query->where('admissions.class_type_id', $classId);
    }

    $getitem = $query->get();

    $response = [];
    foreach ($getitem as $data) {
        $fullName = trim($data->first_name . ' ' . $data->last_name);
        $className = $data->class_name ?? 'N/A';
        $admissionNo = $data->admissionNo ?? 'N/A';

        $displayLabel = $fullName . ' (Ad. ' . $admissionNo . ')';

        $response[] = [
            "value" => $displayLabel, 
            "label" => $displayLabel, 
            "id" => $data->id,
            "mobile" => $data->mobile
        ];
    }

    return response()->json($response);
}
   
   
   public function SalesDeleteInvantory(Request $request)
    {
        $id = $request->delete_id;
                InventorySaleDetail::where('inventory_sale_id',$id)->delete();
                InventorySale::find($id)->delete();

        return redirect::to("sale_inventory_view")->with(
            "message",
            "Invantory Sale  Delete Successfully."
        );
    }
    
     public function sale_inventory_print(Request $request, $id)
    {
        $data = InventorySale::select('inventory_sales.*','admissions.first_name','admissions.last_name','admissions.admissionNo')
                                           ->leftjoin('admissions','admissions.id','inventory_sales.admission_id')->where('inventory_sales.id',$id)->first();
        $dataDetail = InventorySaleDetail::select('inventory_sale_details.*','inventory_items.name as inventory_item_name','admissions.first_name','admissions.last_name')
                                           ->leftjoin('inventory_items','inventory_items.id','inventory_sale_details.inventory_item_id')
                                           ->leftjoin('admissions','admissions.id','inventory_sale_details.admission_id')->where('inventory_sale_id',$id)->orderBy('id', 'DESC')->get();

        return view("print_file.invantory.sales_invantory", ["data" => $data,"dataDetail"=>$dataDetail]);
    }
}
