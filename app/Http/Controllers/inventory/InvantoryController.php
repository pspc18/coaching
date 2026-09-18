<?php

namespace App\Http\Controllers\inventory;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Setting;
use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\InventoryDetail;
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

class InvantoryController extends Controller
{
            public function invantoryDashboard(Request $request){
                return view("invantory.invantory_dashboard");
            }
        
            public function addInvantoryItem(Request $request){
                $data = InventoryItem::where("session_id", Session::get("session_id"));
                if (Session::get("role_id") > 1) {
                    $data = $data->where("branch_id", Session::get("branch_id"));
                }
                $data = $data->orderBy("id", "ASC")->get();
                if ($request->isMethod("post")) {
                    $request->validate([
                        "name" => "required",
                        "unit" => "required",
                        "mrp" => "required",
                    ]);
                    $addinvantory = new InventoryItem();
                    $addinvantory->name = $request->name;
                    $addinvantory->unit = $request->unit;
                    $addinvantory->hsn_code = $request->hsn_code;
                    $addinvantory->mrp = $request->mrp;
                    $addinvantory->user_id = Session::get("id");
                    $addinvantory->branch_id = Session::get("branch_id");
                    $addinvantory->session_id = Session::get("session_id");
                    $addinvantory->save();
                    return response()->json(['status' => 'success','message' => 'Invantory Item add Successfully.',]);  
                }
                return view("invantory.invantory_item.add", ["data" => $data]);
            }
        
            public function editInvantoryItem(Request $request, $id)
            {
                $data = InventoryItem::find($id);
                if ($request->isMethod("post")) {
                    $request->validate([
                        "name" => "required",
                        "unit" => "required",
                        "mrp" => "required",
                    ]);
                    $data->branch_id = Session::get("branch_id");
                    $data->session_id = Session::get("session_id");
                    $data->unit = $request->unit;
                    $data->name = $request->name;
                    $data->hsn_code = $request->hsn_code;
                    $data->mrp = $request->mrp;
                    $data->save();
                     return response()->json(['status' => 'success', 'message' => 'Invantory Item Update Successfully.','redirect' => url('invantory_item_add')]);
                }
                return view("invantory.invantory_item.edit", ["data" => $data]);
            }
        
            public function deleteInvantoryItem(Request $request){
                $id = $request->delete_id;
                $income = InventoryItem::find($id)->delete();
                return redirect::to("invantory_item_add")->with(
                    "message",
                    "Invantory Item Delete Successfully."
                );
            }
            
            public function deleteInvantoryDetail(Request $request){
                $deleteid = $request->delete_id;
                $id = $request->id;
                $income = InventoryDetail::find($deleteid)->delete();
                return redirect::to("invantory_edit/" . $id)->with(
                    "message",
                    "Invantory Delete Successfully."
                );
            }
        
            public function deleteInvantory(Request $request){
                $id = $request->delete_id;
                InventoryDetail::where('inventory_id', $id)->delete();
                Inventory::find($id)->delete();
                return redirect::to("invantory_view")->with(
                    "message",
                    "Invantory  Delete Successfully."
                );
            }
        
            public function addInvantory(Request $request){
                if ($request->isMethod("post")) {
                    $request->validate([
                        // "date" => "required",
                        "invoice_no" => "required",
                        "party_name" => "required",
                        "mobile" => "required",
                        // "gstin" => "required",
                        
                    ]);
                    //dd($request);  
                    $addinvantory = new Inventory(); //model name
                    $addinvantory->branch_id = Session::get("branch_id");
                    $addinvantory->session_id = Session::get("session_id");
                    $addinvantory->user_id = Session::get("id");
                    $addinvantory->invoice_no = $request->invoice_no;
                    $addinvantory->date = $request->date;
                    $addinvantory->party_name = $request->party_name;
                    $addinvantory->mobile = $request->mobile;
                    $addinvantory->gstin = $request->gstin;
                    $addinvantory->total_qty = $request->total_qty;
                    $addinvantory->total_amount = $request->net_amount;
                    $addinvantory->save();
                    $addinvantory_id = $addinvantory->id;
                    if (!empty($request->inventory_item_id)) {
                        foreach ($request->inventory_item_id as $key => $item_id) {
                            $addDetai = new InventoryDetail(); //model name
                            $addDetai->branch_id = Session::get("branch_id");
                            $addDetai->session_id = Session::get("session_id");
                            $addDetai->user_id = Session::get("id");
                            $addDetai->inventory_item_id = $request->inventory_item_id[$key];
                            $addDetai->inventory_id = $addinvantory_id;
                            $addDetai->qty = $request->qty[$key];
                            $addDetai->tax = $request->tax[$key];
                            $addDetai->amount = $request->amount[$key];
                            $addDetai->total_amount = $request->total_amount[$key];
                            $addDetai->date = $request->date;
                            $addDetai->save();
                            InventoryItem::where('id', $request->inventory_item_id[$key])->increment('available_stock', $request->qty[$key]);
                        }
                    }
                    /*
                    $template = MessageTemplate::Select(
                        "message_templates.*",
                        "message_types.slug"
                    )
                        ->leftjoin(
                            "message_types",
                            "message_types.id",
                            "message_templates.message_type_id"
                        )
                        ->where("message_types.status", 1)
                        ->where("message_types.slug", "InventoryItem")
                        ->first();
                    $branch = Branch::find(Session::get("branch_id"));
                    $setting = Setting::where(
                        "branch_id",
                        Session::get("branch_id")
                    )->first();
                    $inventory_item = Inventory::where("id", $request->name)->first();
                    $arrey1 = [
                        "{#admin_name#}",
                        "{#item_name#}",
                        "{#quantity#}",
                        "{#amount#}",
                        "{#school_name#}",
                        "{#support_no#}",
                    ];
                    $arrey2 = [
                        $setting->name,
                        $inventory_item->name,
                        $request->available_stock,
                        $request->total_amount,
                        $setting->name,
                        $setting->mobile,
                    ];
                    if ($template->status != 1) {
                        if ($setting->gmail != "") {
                            if ($branch->email_srvc != 0) {
                                if ($template->email_status != 0) {
                                    $message = str_replace(
                                        $arrey1,
                                        $arrey2,
                                        $template->email_content
                                    );
                                    $emailData = [
                                        "email" => $setting->gmail,
                                        "data" => $message,
                                        "subject" => $template->title,
                                    ];
                                    Helper::sendMail(
                                        "email_print.template_print",
                                        $emailData
                                    );
                                }
                            }
                        }
                        if ($branch->whatsapp_srvc != 0) {
                            if ($setting->mobile != "") {
                                if ($template->whatsapp_status != 0) {
                                    $whatsapp = str_replace(
                                        $arrey1,
                                        $arrey2,
                                        $template->whatsapp_content
                                    );
                                    Helper::sendWhatsappMessage(
                                        $setting->mobile,
                                        $whatsapp
                                    );
                                }
                            }
                        }
                        if ($branch->sms_srvc != 0) {
                            if ($setting->mobile != "") {
                                if ($template->sms_status != 0) {
                                    $sms = str_replace(
                                        $arrey1,
                                        $arrey2,
                                        $template->sms_content
                                    );
                                    Helper::SendMessage($setting->mobile, $sms);
                                }
                            }
                        }
                    }*/
                    return response()->json(['status' => 'success','message' => 'Invantory  add Successfully.',]);  
                }
                return view("invantory.invantory.add");
            }
        
            public function editInvantory(Request $request, $id){
                $data = Inventory::find($id);
                $dataDetail = InventoryDetail::select('inventory_details.*', 'inventory_items.name as inventory_item_name')->leftjoin('inventory_items', 'inventory_items.id', 'inventory_details.inventory_item_id')
                    ->where('inventory_id', $id)->get();
                if ($request->isMethod("post")) {
                    $request->validate([
                        "invoice_no" => "required",
                        "party_name" => "required",
                        "mobile" => "required",
                    ]);
                    $data->branch_id = Session::get("branch_id");
                    $data->session_id = Session::get("session_id");
                    $data->user_id = Session::get("id");
                    $data->invoice_no = $request->invoice_no;
                    $data->date = $request->date;
                    $data->party_name = $request->party_name;
                    $data->mobile = $request->mobile;
                    $data->gstin = $request->gstin;
                    $data->total_qty = $request->total_qty;
                    $data->total_amount = $request->net_amount;
                    $data->save();
                    $addinvantory_id = $data->id;
                    if (!empty($request->inventory_item_id)) {
                        foreach ($request->inventory_item_id as $key => $item_id) {
                            if (isset($request->inventory_detail_id[$key])) {
                                $addDetai = InventoryDetail::find($request->inventory_detail_id[$key]);
                                if ($addDetai->qty >= $request->qty[$key]) {
                                    $qty = $addDetai->qty - $request->qty[$key];
                                    InventoryItem::where('id', $request->inventory_item_id[$key])->decrement('available_stock', $qty);
                                } else {
                                    InventoryItem::where('id', $request->inventory_item_id[$key])->increment('available_stock', $request->qty[$key]);
                                }
                            } else {
                                $addDetai = new InventoryDetail(); //model name 
                                InventoryItem::where('id', $request->inventory_item_id[$key])->increment('available_stock', $request->qty[$key]);
                            }
                            $addDetai->branch_id = Session::get("branch_id");
                            $addDetai->session_id = Session::get("session_id");
                            $addDetai->user_id = Session::get("id");
                            $addDetai->inventory_item_id = $request->inventory_item_id[$key];
                            $addDetai->inventory_id = $addinvantory_id;
                            $addDetai->qty = $request->qty[$key];
                            $addDetai->tax = $request->tax[$key];
                            $addDetai->amount = $request->amount[$key];
                            $addDetai->total_amount = $request->total_amount[$key];
                            $addDetai->date = $request->date;
                            $addDetai->save();
                        }
                    }
                     return response()->json(['status' => 'success','message' => 'Invantory  Updated Successfully.',]);  
                }
                return view("invantory.invantory.edit", ["data" => $data, "dataDetail" => $dataDetail]);
            }
        
            public function viewInvantory(Request $request){
                $data = Inventory::where("session_id", Session::get("session_id"));
                if (Session::get("role_id") > 1) {
                    $invantoryItem = $data->where("branch_id", Session::get("branch_id"));
                }
                $invantoryItem = $data->orderBy("id", "ASC")->get();
                return view("invantory.invantory.view", ["data" => $invantoryItem]);
            }
        
            public function getAutoCompleteInvantoryItem(Request $request){
                $getitem = InventoryItem::where('name', 'LIKE', '%' . $request->search . '%')->get(['id', 'name', 'mrp']);
                $stock = 0;
                foreach ($getitem as $data) {
                    $stock = 0;
                    if (!empty($data)) {
                        $purchased = InventoryDetail::where('inventory_item_id', $data['id'])->sum('qty');
                        $sold = InventorySaleDetail::where('inventory_item_id', $data['id'])->sum('qty');
                        $stock = $purchased - $sold;
                    }
                    $response[] = array("stock" => $stock, "value" => $data['name'], "label" => $data['name'], "item_id" => $data['id'], "mrp" => $data['mrp']);
                }
                echo json_encode($response);
            }
}
