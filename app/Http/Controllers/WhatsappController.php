<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator;
use App\Models\WhatsappApiResponse;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\SuccessMessages;
use App\Models\PermissionMessages;
use App\Models\Admission;
use Session;
use Hash;
use Str;
use Helper;
use File;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
class WhatsappController extends Controller
{
            
            
            
    

            public function validateOtpWhatsapp(Request $request){
                $otp_generated =  Crypt::decrypt(Session::get('otp_request'));
                $otp_requested =  $request->otp;
                if((int)$otp_generated == (int)$otp_requested){
                    $request->session()->put('otp_request','accepted');
                    return response()->json(['status' => true, 'message' => 'Otp Verification Successfully'], 200);
                }
                else{
                    return response()->json(['status' => false, 'message' => 'Otp Verification Failed'], 200);
                }
            }
        
    


    
          
          
              
                    
                  public function setCountSession(Request $request)
                        {
                         
                             if($request->isMethod('post')){
                                $adi_count = Admission::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->where('status',1)->count();
                                              
                
                $type = "check-balance-connection-status";

                                $baseUrl = 'https://whatsapp.rusofterp.in/api/send';
                                
                                $params = [
                                    'username' => env('whatsapp_userName'),
                                    'token'    => env('whatsapp_token'),
                                    'type'     => $type,
                                ];
                                
                                $url = $baseUrl . '?' . http_build_query($params);
                                
                                try {
                                    $ch = curl_init($url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // optional timeout
                                
                                    $response = curl_exec($ch);
                                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                
                                    if (curl_errno($ch)) {
                                        throw new \Exception('cURL Error: ' . curl_error($ch));
                                    }
                                
                                    curl_close($ch);
                                
                                    // Decode and check if response is valid
                                    $decoded = json_decode($response, true);
                               
                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                        throw new \Exception('JSON Decode Error: ' . json_last_error_msg());
                                    }
                                
                                  
                                
                                } catch (\Exception $e) {
                                   // dd('Error: ' . $e->getMessage());
                                }

                           /* if (isset($decoded['data']) && is_array($decoded['data']) && isset($decoded['data']['quota'])) {
                               
                                $credits_bal = $decoded['data']['quota'];
                            } else {
                                $credits_bal = 'Disconnected';
                            
                               
                            }*/

                         /* if ($decoded['data']['connected'] == false) {
                               
                                $connected = 'inactive';
                            } else {
                                $connected = 'active';
                            
                               
                            }*/
                    
                               $request->session()->put('student_count',$request['data']['student_count']);
                               $request->session()->put('branch_count',$request['data']['branch_count']);
                               $request->session()->put('user_count',$request['data']['user_count']);
                               $request->session()->put('registration_date',$request['data']['registration_date']);
                               $request->session()->put('emc_date',$request['data']['emc_date']);
                               $request->session()->put('domain_expire_date',$request['data']['domain_expire_date']);
                               $request->session()->put('client_name',$request['data']['name']);
                               $request->session()->put('client_email',$request['data']['email']);
                               $request->session()->put('client_mobile',$request['data']['mobile']);
                               $request->session()->put('token_no',$request['data']['token_no']);
                               $request->session()->put('register_student',$adi_count ?? '');
                               $request->session()->put('whatsapp_balance',$credits_bal ?? '');
                               $request->session()->put('whatsapp',$connected ?? ''); 
                               
                               
                             }
                             
                            // $this->checkBalance();
                                return response()->json([
                                    'status' => true,
                                    'message' => '',
                                    'registration_date' => date('d-m-Y', strtotime($request['data']['registration_date'])),
                                    'domain_expire_date' => date('d-m-Y', strtotime($request['data']['domain_expire_date'])),
                                    'emc_date' => date('d-m-Y', strtotime($request['data']['emc_date'])),
                                    'whatsapp_balance' => $credits_bal ?? '0',
                                    'register_student' => $adi_count ?? '0',
                                    'student_count' => $request['data']['student_count'] ?? '0'
                                ], 200);

                        }
                        
                        
                    

            
}
