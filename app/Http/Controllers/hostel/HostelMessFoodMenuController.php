<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\FoodMenuList;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelMessFoodMenuController extends Controller
{
            public function messFoodMenuAdd(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        //'name' => 'required', 
                    ]);
                    if (!empty($request->day)) {
                        foreach ($request->day as $item) {
                            $mess_food_routine_id = $request->mess_food_routine[$item];
                            $mess_name = $request->names[$item];
                            foreach ($mess_food_routine_id as $key => $food_routine_id) {
                                $sunData = FoodMenuList::where('mess_food_routine_id', '=', $food_routine_id)->where('day_name', '=', $item)->first();
                                if (!empty($mess_name[$key])) {
                                    if (!empty($sunData)) {
                                        $data = $sunData;
                                    } else {
                                        $data = new FoodMenuList;
                                    }
                                    $data->name = $mess_name[$key];
                                    $data->mess_food_routine_id = $food_routine_id;
                                    $data->day_name = $item;
                                    $data->save();
                                } else {
                                    FoodMenuList::where('mess_food_routine_id', '=', $food_routine_id)->where('day_name', '=', $item)->update([
                                        'name' => null
                                    ]);
        
                                }
                            }
                        }
                    }
                    return redirect::to('messFoodMenuAdd')->with('message', 'Mess Food Category Updated Successfully !');
                }
                //   $year = date('Y');	
                //	$month = date('m');	
                //$monthDate = date('D', mktime(0, 0, 0, $month, 1, $year)); 
                $monthDate = array();
                $monthDate[] = 'Monday';
                $monthDate[] = 'Tuesday';
                $monthDate[] = 'Wednesday';
                $monthDate[] = 'Thursday';
                $monthDate[] = 'Friday';
                $monthDate[] = 'Saturday';
                $monthDate[] = 'Sunday';
                return view('hostel.mess_food_menu.add', ['monthDate' => $monthDate]);
            }
            public function messFoodRoutineEdit(Request $request, $id){
                $data = MessFoodMenu::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                    ]);
                    $data->name = $request->name;
                    $data->frome_time = $request->frome_time;
                    $data->to_time = $request->to_time;
                    $data->save();
                    return redirect::to('messFoodRoutineAdd')->with('message', 'Mess Food Category Updated Successfully !');
                }
                return view('hostel.mess_food_routine.edit', ['data' => $data]);
            }
            public function messFoodRoutineDelete(Request $request){
                $id = $request->delete_id;
                $building = MessFoodMenu::find($id)->delete();
                return redirect::to('messFoodRoutineAdd')->with('message', 'Mess Food Category Deleted Successfully !');
            }


}
























