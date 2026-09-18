<!DOCTYPE html>
<html lang="en">
@php
$sidebar = Helper::getSiderbar();
$subsidebar  = DB::table('sidebar_sub')->groupBy('sidebar_id')->orderBy('sidebar_id','ASC')->get();
$roleType = Helper::roleType();
    $getSession=Helper::getSession();

@endphp
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set Permissions</title>
  <style>
  body{
  display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        padding:30px;
}
.main{
    margin-top:30px;
    width:80%;
    border-radius:20px;
    /*background:#f2f2f2cf;*/
}
 .bg_image{
        background-image: url('{{ env('IMAGE_SHOW_PATH').'default/Icon_images/rm347-porpla-01.jpg' }}');
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }
     .main_logo {
      position: fixed;
      top: 10px;
      right: 40px;
      filter: drop-shadow(6px 5px 3px #7d7d7d);
    }
    .heading {
  font-weight: 600;
  font-size: 18px;
  font-family: Georgia;
  letter-spacing: 3px;
  margin-bottom: 10px;
  text-transform: capitalize;
  text-shadow: 5px 5px 4px gray;
 text-align:center;
 margin-bottom:30px;
}
    .heading2 {
  font-weight: 600;
  font-size: 15px;
  font-family: Georgia;
  text-transform: capitalize;
  text-align: left;
  margin-top: 30px;
}  

.notice_text{
    margin-top: 1rem;
    display: flex;
    align-items: first baseline;
}
.notice_text b{
    white-space:nowrap;
    margin-right:10px;
}

</style>
</head>

<body class="bg_image">
<img src="https://www.rukmanisoftware.com/public/assets/img/header-logo.png" alt="Company Logo" class="main_logo" width="100px">
     
    <div class="main" style='overflow:auto;height:100%'>
           <h2 class='heading'>Allocation Of Modules For New User</h2>
        <form action="{{ url('allowSidebar') }}" method="post">
            @csrf
			
			<h2 class='heading2'>Branch Details: - </h2>
                		<div class="row ">
                		    		<div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;">{{ __('user.Role') }}*</label>
                                <select class="select2 form-control @error('role_id') is-invalid @enderror" name="role_id" id="role_id" required>
                                    <option value="">{{ __('common.Select') }}</option>
                                    @if(!empty($roleType))
                                    @foreach($roleType as $item)
                                    <option value="{{ $item->id ?? ''  }}" {{$item->id == 1 ? 'selected' : ''}}>{{ $item->name ?? ''  }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                 
                            </div>
                        </div> 
                		    <div class="col-md-2">
                					<label style="color:red;" for="branch_code">Branch Code*</label>
                					<input type="text" class="form-control " id="branch_code" name="branch_code" placeholder="Branch Code" value="{{old('branch_code')}}">
                			</div>
                			<div class="col-md-2">
                					<label style="color:red;" for="branch_name" class="required">Branch Name*</label>
                					<input type="text" class="form-control @error('branch_name') is-invalid @enderror" id="branch_name" name="branch_name" placeholder="Branch Name" value="{{old('branch_name')}}">
                			</div>
							 <div class="col-md-2">
                				
                					<label style="color:red;" for="branch_code">Branch Count*</label>
                					<input type="text" class="form-control " id="branch_code" name="branch_count" placeholder="Branch Count" value="{{old('branch_count')}}">
                				
                			</div>
                				<div class="col-md-2">
							     <div class="form-group">
									 <label style="color:red;" >{{ __('Current Active Session') }}*</label>
								  <select class="form-control " id="session_id" name="session_id">
										 @if(!empty($getSession)) 
											  @foreach($getSession as $type)
												 <option value="{{ $type->id ?? ''  }} " >{{ $type->from_year ?? ''  }} - {{ $type->to_year ?? ''  }}</option>
											  @endforeach
										  @endif
										 </select>
										@error('current_active_session_id')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
								@enderror
								</div>
							</div>
                			</div>
                			
                				<h2 class='heading2'>Personal Details: - </h2>
                			<div class="row">
                			<div class="col-md-2">
                					<label style="color:red;" for="contact_person" class="required">Director*</label>
                					<input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" placeholder="Contact Person" value="{{old('contact_person')}}" required>
                			</div>
							<div class="col-md-2">
                					<label style="color:red;" for="contact_person" class="required">Mobile*</label>
                					<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" value="{{old('mobile')}}" required>
                			</div>
                		   	<div class="col-md-2">
                					<label for="emailid" class="required">Email Id</label>
                					<input type="text" class="form-control" id="email" name="email" placeholder="Email ID" value="{{old('email')}}">
                			</div>
                			<div class="col-md-2">
                					<label for="address" class="required">Address</label>
                					<input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Address" value="{{old('address')}}">
                			</div>
					
                			<div class="col-md-2">
                				
                					<label style="color:red;" for="Username" class="required">User Name*</label>
                					<input type="text" class="form-control @error('user_name') is-invalid @enderror" id="user_name" name="user_name" placeholder="User Name" value="{{old('user_name')}}" required>
                					
                				
                			</div>
                			<div class="col-md-2">
                				 
                					<label style="color:red;" for="Password" class="required">Password*</label>
                					<input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" value="{{old('password')}}" required>
                				
                				
                			</div>
                		
							</div>
                		  
                		  
                		  	<h2 class='heading2'>Service's Details: - </h2>
                			<div class="row">
                			<div class="col-md-2">
                					<label for="Whatsapp status" class="required">Whatsapp Packs</label>
                						<select name="whatsapp_status" id="whatsapp_status" class="form-control">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                			</div>
							<div class="col-md-2">
                					<label for="Whatsapp status" class="required">SMS Packs</label>
                						<select name="sms_status" id="sms_status" class="form-control">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                			</div>
							<div class="col-md-2">
                					<label for="Whatsapp status" class="required">Email Packs</label>
                						<select name="email_status" id="email_status" class="form-control">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                			</div>
                			
                			<div class="col-md-12">
            			    	<p class="notice_text"><b class="text-danger">Note :- </b>If you are selecting whatsapp option then you have to submit it's all required fields like instance id, access token and api url in branch table in their respective field manually.</p>
                			</div>
                		</div>
                		
                		
                        
        <div class="row mt-5">
            
           
<div class="col-md-12 col-12">
    <div class="row">

               <div class="col-md-6 col-12">
               <div class="row">
                   
                    @if(!empty($sidebar))
                        @foreach($sidebar as $data)
                        <div class="col-md-6 col-6 text-left">
                        <div class="custom-control custom-checkbox">
                        <input name="sidebar_id[]" class="custom-control-input custom-control-input-primary custom-control-input-outline checkbox pointer"  type="checkbox"  data-name="{{$data['name']?? ''}}" id="{{ $data->id ?? ''  }}" value="{{ $data->id ?? ''  }}">
                        <label for="{{ $data->id ?? ''  }}" class="custom-control-label pointer"><p>{{$data['name'] ?? ''}}</p></label>
                        </div>
                        </div>
                        @endforeach
                        @endif
               </div>
               </div>
                <div class="col-md-6 col-6">
                            
                            
                        <div class="col-md-12">
                                    <div class="form-group">
                                        <label > 	<h2 class='heading2' style='margin-top: -40px;'>Alloted Sub Panel's List : -</h2> </label>
                                        <select class="" multiple="multiple" name="sidebar_sub_id[]" id="sidebar_sub_id" style="width: 100%;height: 21pc;">
                                            <option value="">Select</option>
                                           
                                            @if(!empty($subsidebar))
                                                 @foreach($subsidebar as $sub)
                                                  <optgroup label="{{$sub->sidebar_name ?? ''}}" style="display:none" class="sidebar_{{$sub->sidebar_id ?? '' }} local-link">
                                                     @php
                                                     $sidebar2  = DB::table('sidebar_sub')->where('sidebar_id',$sub->sidebar_id)->orderBy('id','ASC')->get();
                                                     @endphp
                                                     @if(!empty($sidebar2))
                                                       @foreach($sidebar2 as $sub1)
                                                        <option value="{{$sub1->id ?? '' }}" >{{$sub1->name ?? '' }} </option>
                                                        @endforeach
                                                    @endif

                                                 </optgroup>
                                                @endforeach
                                            @endif

                                             
                                        </select>
                                       
                                    </div>
                                </div>
                       
                        
                </div>
               
               </div>
            </div>
           
        </div>
        <center><button type="submit" class="refresh-button">Submit</button></center>
        </form>
        <br><br>
    </div>

<style>

.local-link::before {
  display: inline-block;
  margin-left: 12px;
}
/* Reset default browser styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* Set basic styles */
/*body {*/
/*  font-family: Arial, sans-serif;*/
/*  background-color: white;*/
/*  justify-content: center;*/
/*  align-items: center;*/
/*  min-height: 100vh;*/
/*}*/

.refresh-button {
  display: inline-block;
  padding: 12px 24px;
  background-color: #6639b5;
  color: #fff;
  border: 1px solid transparent;
  border-color: #6639b5;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-decoration:none;
}

.refresh-button:hover {
    color: #ff5722;
    background-color: #6639b500;
    border-color: #ff5722;
}

.connectionimg{max-width: 400px;}
.d-none{display:none;}
.pointer{cursor:pointer;}
section{
    position: absolute;
    top: 0;
    width: 100%;
    display: flex;
    height: 100%;
    
    text-align: center;
    justify-content: center;
}

</style>
<link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-theme.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/school/css/icheck-bootstrap.min.css') }}">
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script>
    $("#select_all").change(function () {  
        $(".checkbox").prop('checked', $(this).prop("checked")); 
    });
    
    $(".checkbox").click(function(){
        var id = $(this).attr('id');
        var name = $(this).attr('data-name');
      if ($(this).is(':checked')) {
     $('.sidebar_'+id).show();
      }else {
         $('.sidebar_'+id).hide();
      }
}); 
</script>
</body>
</html>
