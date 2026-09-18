@php
$getMessageType = Helper::getMessageType();
@endphp
@extends('layout.app') 
@section('content')
<style>
       
        .switch_check {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 25px;
            margin-top: 10px;
        }

      
        .switch_check .check_new {
            opacity: 0;
            width: 0;
            height: 0;
        }

      
        .slider_check {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 34px;
            transition: 0.4s;
        }

        
        .slider_check::before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 9px;
            bottom: 3px;
            background-color: white;
            border-radius: 50%;
            transition: 0.4s;
        }

        
        .check_new:checked+.slider_check {
            background-color: #2196F3;
        }

        .check_new:checked+.slider_check::before {
            transform: translateX(26px);
        }
    </style>
<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.Edit Message Contant') }} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add </a>-->
                    <a href="{{url('messageTemplate')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-eye"></i> {{ __('View') }}  </a>
                   <a href="{{url('messageDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                    </div>
                    
                    </div>                 
                <form id="quickForm" action="{{ url('messageTemplateEdit') }}/{{$data->id}}" method="post">
                    @csrf
                    <div class="row m-2">
                            <div class="col-md-3" >
                                <label class="text-danger">{{ __('master.Message Type') }}*</label>
                                <select class="form-control select2 @error('message_type_id') is-invalid @enderror" name="message_type_id" id="message_type_id">
                                    <option value="">{{ __('common.Select') }}</option>
                                  @if(!empty($getMessageType)) 
                                      @foreach($getMessageType as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ $type->id == old('message_type_id', $data->message_type_id) ? 'selected' : ''}}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                                @error('message_type_id')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
                            </div>
                            <div class="col-md-6">
                    			<label class="text-danger">{{ __('master.Subject/ Title Name') }}*</label>
                    			<input class="form-control @error('title') is-invalid @enderror" type="text" name="title" id="title" placeholder="{{ __('master.Message Title') }}" value="{{ $data->title ?? old('title')}}">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror                    			
                        	</div>
                        	<div class="col-md-12 pt-2">
                                <div class="form-group">
                                    <label class="text-danger"><b>{{ __('master.E-mail Content') }}*</b></label>
                                    <textarea type="text" class="form-control @error('email_content') is-invalid @enderror" id="editor1" name="email_content" placeholder="{{ __('master.E-mail Content') }}">{{ $data->email_content ??  old('email_content') }}</textarea>
                                    @error('email_content')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
            					    @enderror
            					    
            					    <div>
            					        <label><b>{{ __('master.Email Status') }}</b></label>
            					        <label class="switch_check">
                                            <input type="checkbox" class="check_new changeStatus" id="email_status" name="email_status" value="1" {{ ($data->email_status == 1)? 'checked' : '' }}>
                                            <span class="slider_check"></span>
                                        </label>
            					    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>{{ __('master.SMS Content') }}</b></label>
                                    <textarea type="text" class="form-control" id="whatsapp_content" name="sms_content" placeholder="Type Your Content" rows="5">{{ $data->sms_content ??  old('sms_content') }}</textarea>
                                    <div>
            					        <label><b>{{ __('master.Sms Status') }}</b></label>
            					        <label class="switch_check">
                                            <input type="checkbox" class="check_new changeStatus" id="sms_status" name="sms_status" value="1" {{ ($data->sms_status == 1)? 'checked' : '' }}>
                                            <span class="slider_check"></span>
                                        </label>
            					    </div>
                                </div>
                            </div>
                             <div class="col-md-4">
                                <label><b>{{ __('master.SMS Template Id') }}</b></label>
                                <input class="form-control " type="text" name="template_id" id="template_id" onkeypress="javascript:return isNumber(event)" placeholder=" {{ __('master.SMS Template Id') }}" value="{{ $data->template_id ?? old('template_id')}}">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>{{ __('master.Whatsapp Content') }}</b></label>
                                    <textarea type="text" class="form-control" id="whatsapp_content" name="whatsapp_content" placeholder="{{ __('master.Whatsapp Content') }}" rows="5">{{ $data->whatsapp_content ??  old('whatsapp_content') }}</textarea>
                                    <div>
            					        <label><b>{{ __('master.Whatsapp Status') }}</b></label>
            					        <label class="switch_check">
                                            <input type="checkbox" class="check_new changeStatus" id="whatsapp_status" name="whatsapp_status" value="1" {{ ($data->whatsapp_status == 1) ? 'checked' : '' }}>
                                            <span class="slider_check"></span>
                                        </label>
            					    </div>
                                </div>
                            </div>
                                              
                    </div>                        
        
                    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary ">{{ __('common.Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</section>
</div>  

<script>
$(document).ready(function(){
    var editor1 = new RichTextEditor("#editor1");
});

$(document).ready(function(){
    $('.changeStatus').click(function(){
            if ($(this).is(":checked")) {
                $(this).val("1");
              } else {
                $(this).val("0");
              }
        });
});
</script>
 @endsection    