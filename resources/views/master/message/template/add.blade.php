
@extends('layout.app') 
@section('content')
@php
$getMessageType = Helper::getMessageType();
$getPermission = Helper::getPermission();
@endphp
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
        <div class="col-md-12">
            <div class="card collapsed-card card-outline card-orange">
                <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-whatsapp"></i> &nbsp; {{ __('master.Add Message Template') }} </h3>
                <div class="card-tools">
                <button type="button" class="btn btn-tool {{ Helper::permissioncheck(9)->add ? '' : 'd-none' }}" data-card-widget="collapse"><i class="fa fa-plus text-white"></i>
                </button>
                <a href="{{url('messageDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                </div>
                
                </div>
                
                    <div class="card-body">
                        <form id="quickForm" action="{{ url('messageTemplate') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-3" >
                                <label class="text-danger">{{ __('master.Message Type') }} *</label>
                                <select class="form-control select2 @error('message_type_id') is-invalid @enderror" name="message_type_id" id="message_type_id">
                                    <option value="">{{ __('common.Select') }}</option>
                                  @if(!empty($getMessageType)) 
                                      @foreach($getMessageType as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ $type->id == old('message_type_id') ? 'selected' : ''}}>{{ $type->name ?? ''  }}</option>
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
                    			<input class="form-control @error('title') is-invalid @enderror" type="text" name="title" id="title" placeholder="{{ __('master.Message Title') }}" value="{{ old('title') }}">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror                    			
                        	</div>
                        	
                            <div class="col-md-12 pt-2">
                                <div class="form-group">
                                    <div>
                                    <label class="text-danger"><b>{{ __('master.E-mail Content') }}*</b></label>
                                    </div>
                                    <textarea type="text" class="form-control @error('email_content') is-invalid @enderror" id="editor1" name="email_content" value="" placeholder="{{ __('master.E-mail Content') }}"></textarea>
                                    @error('email_content')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
            					    @enderror
            					    <div>
            					        <label><b>{{ __('master.Email Status') }}</b></label>
            					        <label class="switch_check">
                                            <input type="checkbox" class="check_new changeStatus" id="email_status" name="email_status" value="1" checked>
                                            <span class="slider_check"></span>
                                        </label>
            					    </div>
                                        
                                </div>
                            </div>
                          
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>{{ __('master.SMS Content') }}</b></label>
                                    <textarea type="text" class="form-control" id="sms_content" name="sms_content" value="" placeholder="{{ __('master.SMS Content') }}" rows="5"></textarea>
                                    <div>
            					        <label><b>{{ __('master.Sms Status') }}</b></label>
            					        <label class="switch_check">
                                            <input type="checkbox" class="check_new changeStatus" id="sms_status" name="sms_status" value="1" checked>
                                            <span class="slider_check"></span>
                                        </label>
                					</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label><b>{{ __('master.SMS Template Id') }}</b></label>
                                <input class="form-control" type="text" name="template_id" id="template_id" onkeypress="javascript:return isNumber(event)" placeholder="{{ __('master.SMS Template Id') }}" value="">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>{{ __('master.Whatsapp Content') }}</b></label>
                                    <textarea type="text" class="form-control" id="whatsapp_content" name="whatsapp_content" value="" placeholder="{{ __('master.Whatsapp Content') }}" rows="5"></textarea>
                                    <div>
            					        <label><b>{{ __('master.Whatsapp Status') }}</b></label>
            					        <label class="switch_check">
                                            <input type="checkbox" class="check_new changeStatus" id="whatsapp_status" name="whatsapp_status" value="1" checked>
                                            <span class="slider_check"></span>
                                        </label>
                					</div>
                                </div>
                            </div>                    	
                        </div>
         
                        <div class="row m-2">
                            <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">{{ __('common.submit') }} </button>
                            </div>
                        </div>
                        </form>
                    </div>
                    </div>
                    </div>

        <div class="col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-whatsapp"></i> &nbsp;{{ __('master.View Message Template') }} </h3>
                <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fa fa-minus text-white"></i>
                </button>
                </div>
                
                </div>
                
                    <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped  dataTable">
                          <thead class="bg-primary">
                            <tr role="row">
                                    <th>{{ __('common.SR.NO') }}</th>
                                    <th>Type</th>
                                    <th>{{ __('master.Title') }}</th>
                                    <th>{{ __('master.Content') }}  </th>
                                    @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                        <th>{{ __('common.Action') }}</th>
                                    @endif
                             </tr>
                          </thead>
                          <tbody>
                              
                              @if(!empty($data))
                                @php
                                   $i=1
                                @endphp
                                @foreach ($data  as $item)
                                    @php
                                        $type = DB::table('message_types')->where('id',$item->message_type_id)->first()
                                    @endphp
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $type->name ?? ''  }}</td>
                                    <td>{{ $item['title'] ?? ''  }}</td>
                                    <td><small><u class="text-danger">Email :</u> <span>{{ Str::limit($item->email_content, 30) }}</span> <br><u class="text-primary">SMS :</u> {{ Str::limit($item->sms_content, 30) ?? ''  }}<br><u class="text-success">Whatsapp :</u> {{ Str::limit($item->whatsapp_content, 30) ?? ''  }}</small></td>
                                    <td>
                                     @if($getPermission->edit == 1)
                                          <a href="{{url('messageTemplateEdit',$item['id'])}}" class="btn btn-primary  btn-xs {{ Helper::permissioncheck(9)->edit ? '' : 'd-none' }}" title="Edit Content"><i class="fa fa-edit"></i></a>
                                    @endif
                                    @if($getPermission->deletes == 1)
                                        <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#deleteModal"  class="deleteData btn btn-primary  btn-xs ml-3 {{ Helper::permissioncheck(9)->delete ? '' : 'd-none' }}" title="Delete Content"><i class="fa fa-trash-o"></i></a>
                                    @endif
                                    </td>
                            </tr>
                              @endforeach
                        @endif
                    </tbody>
                  </table>
                    </div>
            
            </div>
            </div>
            
        
        </div>
    </div>
    </section>
</div>
    
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
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


  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
  
</script>

<!-- The Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('messageTemplateDelete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
         </div>
       </form>
    </div>
  </div>
</div>
@endsection      