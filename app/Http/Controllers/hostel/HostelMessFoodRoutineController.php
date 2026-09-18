<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\MessFoodRoutine;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelMessFoodRoutineController extends Controller

{
	public function messFoodRoutineAdd(Request $request)
	{

		if ($request->isMethod('post')) {
			$request->validate([


				'name' => 'required',

			]);

			$messHead = new MessFoodRoutine; //model name
			$messHead->name = $request->name;
			$messHead->frome_time = $request->frome_time;
			$messHead->to_time = $request->to_time;
			$messHead->save();

			return redirect::to('messFoodRoutineAdd')->with('message', 'Mess Food Category Added Successfully !');
		}
		$data = MessFoodRoutine::get();

		return view('hostel.mess_food_routine.add', ['data' => $data]);
	}

	public function messFoodRoutineEdit(Request $request, $id)
	{

		$data = MessFoodRoutine::find($id);
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

	public function messFoodRoutineDelete(Request $request)
	{

		$id = $request->delete_id;

		$building = MessFoodRoutine::find($id)->delete();

		return redirect::to('messFoodRoutineAdd')->with('message', 'Mess Food Category Deleted Successfully !');
	}
}
