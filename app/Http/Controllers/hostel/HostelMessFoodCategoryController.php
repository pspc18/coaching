<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\MessFoodCategory;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelMessFoodCategoryController extends Controller

{


	public function messFoodCategoryAdd(Request $request)
	{

		if ($request->isMethod('post')) {
			$request->validate([
				
				'name' => 'required',

			]);

			$messHead = new MessFoodCategory; //model name
			$messHead->name = $request->name;
			$messHead->save();

			return redirect::to('messFoodCategoryAdd')->with('message', 'Mess Food Category Added Successfully !');
		}
		$data = MessFoodCategory::get();

		return view('hostel.messfoodcategory.add', ['data' => $data]);
	}

	public function messFoodCategoryEdit(Request $request, $id)
	{

		$data = MessFoodCategory::find($id);
		if ($request->isMethod('post')) {
			$request->validate([
				'name' => 'required',

			]);


			$data->name = $request->name;
			$data->save();

			return redirect::to('messFoodCategoryAdd')->with('message', 'Mess Food Category Updated Successfully !');
		}

		return view('hostel.messfoodcategory.edit', ['data' => $data]);
	}

	public function messFoodCategoryDelete(Request $request)
	{

		$id = $request->delete_id;

		$building = MessFoodCategory::find($id)->delete();

		return redirect::to('messFoodCategoryAdd')->with('message', 'Mess Food Category Deleted Successfully !');
	}
}
