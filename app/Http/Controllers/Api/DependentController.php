<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Validator;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class DependentController extends BaseController
{

	public function getCountry(Request $request)
	{
	    try{
	     $getCountry = Country::orderBy('id', 'ASC')->get();
           
           
            $data = array();
            foreach ($getCountry as $key => $item) {
                $data[] = array(
                    
                    'label' => $item->name,
                    'value' => $item->id,
                );
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}
	public function getState(Request $request,$country_id)
	{
	    try{
	      $getstate = State::where('country_id',$country_id)->get();
           
           
            $data = array();
            foreach ($getstate as $key => $item) {
                $data[] = array(
                    
                    'label' => $item->name,
                    'value' => $item->id,
                );
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}
	public function getCity(Request $request,$state_id)
	{
	    try{
	       $getcitie = City::where('state_id',$state_id)->get();
           
           
            $data = array();
            foreach ($getcitie as $key => $item) {
                $data[] = array(
                    
                    'label' => $item->name,
                    'value' => $item->id,
                );
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}





}