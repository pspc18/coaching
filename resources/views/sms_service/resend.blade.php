@php
$getRole = Helper::roleType();
$classType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')

                                                                    
<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
    <div class="card card-outline card-orange">
          	<div class="card-header bg-primary">
                        @if(Session::get('') == 3)
                            <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Send Message') }}</h3>
                        @else						    
							<h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp;{{ __('Send Message') }}</h3>
						@endif
							<div class="card-tools"> 
							@if(Session::get('role_id') !== 3)
							    <a href="{{url('send_message_terminal')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('common.Back') }}</span>  </a> 
                            @endif

                                
                            </div>
						</div>
						       
       
        <section>
         <form id="sendSms" action="{{ url('resend_message') }}" method="post">   
            @csrf
    
                <div class="row m-2">
                    <div class="col-md-12" id="student_list_show" style="overflow-y: scroll;">
               <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                            <thead class="bg-primary">
                                <tr role="row">
                                    <th>
                                        Sr.No.
                                        <!--<input class="ml-3" type="checkbox" id="all_checkbox" checked></th>-->
                                    <th>Mobile/GroupId</th>
                                    <!--<th>Type</th>-->
                                    <th>Sender Message</th>
                                    <th>Media Url</th>
                                    <th>Date of Faliure</th>
                                    <th>Error</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
 
                                @if(!empty($data))
                                @php
                                    $i=1
                                @endphp

                                @foreach ($data  as $item)
                                <tr>
                                    <td>
                                    {{ $i++ }}
                                    <input class="ml-3 checkbox" type="checkbox" id="checkbox" name="ids[]" value="{{ $item['id'] ?? ''  }}" checked>
                                    </td>
                                    <td>{{$item['mobile'] ?? '' }}{{$item['group_id'] ?? '' }}
                                     <input type='hidden' name ='mobile[]' value="{{$item['mobile'] ?? ''}}" /> 
                                    </td>
                                  
                                 <!--<td>{{$item['type'] ?? '' }}</td>-->
                                    
                                    
                                    <td>{{ $item['sender_message'] ?? '' }}
                                    <input type='hidden' name ='text[]' value="{{$item['sender_message'] ?? ''}}" /> 
                                    </td>
                                    <td>
                                    @if( !empty ($item['media_url'] ?? ''))
                                    <a target='_blank' href="{{$item['media_url'] ?? '' }}" >Click for view</a>
                                    
                                    @else
                                    
                                    @endif
                                    
                                    <input type='hidden' name ='media_url[]' value="{{$item['media_url'] ?? ''}}" />
                                    </td>
                                    <td>{{date('d/m/Y', strtotime($item['created_at']))}}</td>
                                    <td class='text-danger'>{{ $item['message'] ?? '' }}
                                      <!--<input type='hidden' name ='resend_status[]' value="{{$item['resend_status'] ?? ''}}" />-->
                                    </td>
                                    <td class='text-danger'>
                                          <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData btn btn-danger btn-xs m-1 tooltip1 {{ Helper::permissioncheck(13)->delete ? '' : 'd-none' }}" title1="Delete"> &nbsp;<i class="fa fa-trash"></i> </a>
                                     
                                    </td>
                                </tr>
                                @endforeach
                                     @endif
                             
                            </tbody>
                        </table>
                        
                    </div>
                       @if(count($data)>0)
                 <div class="col-md-12 text-center pb-2">
                    <button type="submit" id="submit" class="btn btn-primary">Send Message</button>
                </div>
                
                @endif
                </div>
         </section>
        
        </form>
    </div>
</div>
</div>
</div>
</div>
</section>
</div>



<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('failed_messages_delete') }}" method="post">
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

<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>


@endsection

