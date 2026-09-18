<?php

namespace App\Http\Controllers\master\message;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller

{
    
            public function messageDashboard(){
                return view('master.message.dashboard');
            }

            public function messageTemplateAdd(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        //'email_content' => 'required', 
                      //  'message_type_id' => 'required', 
                       // 'title' => 'required',    
                    ]);
                    $sms_status = $request->sms_status == 1 ? 1 : 0;
                    $email_status = $request->email_status == 1 ? 1 : 0;
                    $whatsapp_status = $request->whatsapp_status == 1 ? 1 : 0;
                    $template = new MessageTemplate;//model name
                    $template->user_id = Session::get('id');
                    $template->session_id = Session::get('session_id');
                    $template->branch_id = Session::get('branch_id');
                    $template->message_type_id = $request->message_type_id;
                    $template->title = $request->title;
                    $template->email_content = $request->email_content;
                    $template->sms_content = $request->sms_content;
                    $template->template_id = $request->template_id;
                    $template->whatsapp_content = $request->whatsapp_content;
                    $template->sms_status = $sms_status;
                    $template->email_status = $email_status;
                    $template->whatsapp_status = $whatsapp_status;
                    $template->save();
                    return redirect::to('messageTemplate')->with('message', 'Template Added Successfully !');
                }
                $content = MessageTemplate::orderBy('id', 'DESC')->get();
                return view('master.message.template.add',['data'=>$content]);
            }    
    
            public function messageTemplateEdit(Request $request,$id){
                $data = MessageTemplate::find($id);
                if($request->isMethod('post')){
                    $request->validate([
                        //'email_content' => 'required',
                       // 'message_type_id' => 'required', 
                       // 'title' => 'required',    
                    ]);
                    $sms_status = $request->sms_status == 1 ? 1 : 0;
                    $email_status = $request->email_status == 1 ? 1 : 0;
                    $whatsapp_status = $request->whatsapp_status == 1 ? 1 : 0;
                    $data->user_id = Session::get('id');
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->message_type_id = $request->message_type_id;
                    $data->title = $request->title;
                    $data->email_content = $request->email_content;
                    $data->sms_content = $request->sms_content;
                    $data->template_id = $request->template_id;
                    $data->whatsapp_content = $request->whatsapp_content;
                    $data->sms_status = $sms_status;
                    $data->email_status = $email_status;
                    $data->whatsapp_status = $whatsapp_status;
                    $data->save();
                    return redirect::to('messageTemplate')->with('message', 'Content Updated Successfully !');
                }
                return view('master.message.template.edit',['data'=>$data]);
            }   

            public function messageTemplateDelete(Request $request){
                $data = MessageTemplate::find($request->delete_id)->delete();
                return redirect::to('messageTemplate')->with('message', 'Message Template Deleted Successfully!');
            }
    
            public function messageTypeAdd(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        'name' => 'required|unique:message_types,slug',
                    ]);
                    $add = new MessageType;
                    $add->user_id = Session::get('id');
                    $add->session_id = Session::get('session_id');
                    $add->branch_id = Session::get('branch_id');
                    $add->name = $request->name;
                    $add->slug = Str::slug($request->name);
                    $add->save();
                    return redirect()->to('messageType')->with('message','Message Type Add Successfully.');
                }
                $data = MessageType::orderBy('id','DESC')->get();
                return view('master.message.type.add', ['data'=>$data]);
            }
    
            public function messageTypeEdit(Request $request, $id){
                $data = MessageType::find($id);
                if($request->isMethod('post')){
                    $request->validate([
                        'name' => 'required|unique:message_types,slug',
                    ]);
                    $data->user_id = Session::get('id');
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->name = $request->name;
                    $data->save();
                    return redirect()->to('messageType')->with('message','Message Type Update Successfully.');
                }
                return view('master.message.type.edit', ['data'=>$data]);
            }
    
            public function messageTypeDelete(Request $request){
                $data = MessageType::find($request->delete_id)->delete();
                return redirect()->to('messageType')->with('message','Message Type Deleted Successfully.');
            }
    
            public function messageTypeStatus(Request $request){
                if(!empty($request->status_id)){
                    $data = MessageType::find($request->status_id);
                    $data->status = $request->status;
                    $data->save();
                }
                return redirect()->to('messageType')->with('message','Status Changed Successfully');
            }
}
