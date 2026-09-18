<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,            
            'message' => $message,
        ];		

        return response()->json($response, 200);
    }
	public function sendResponseData($result, $message)
    {
    	$response = [
            'success' => true,            
            'message' => $message,
            'data' => $result,
        ];		

        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error,$message)
    {
    	$response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }
	
}