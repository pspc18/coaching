@php
    $getHostel = Helper::getHostel();
    $getFoodCategory = Helper::getFoodCategory();
    
@endphp
@extends('layout.app') 
@section('content')


<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12"> 
            <div class="card  card-outline card-orange">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp; {{ __('hostel.Mess Menu List') }}</h3>
            <div class="card-tools">
            <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}</a>
            </div>
            
            </div>                 
       
 <form id="quickForm" action="{{ url('messFoodMenuAdd') }}" method="post" enctype="multipart/form-data">   
                        @csrf
        <div class="row m-2">
          <div class="col-12">
                 <table id="example1" class="table table-bordered table-striped">
                <thead class="bg-primary">
                <tr>
				  <!-- <th>S No</th> -->
				  <th>{{ __('hostel.Day Name') }}</th>
				  @php
                     $data =  DB::table('mess_food_routine')->wherenull('deleted_at')->get();
                  
                @endphp
				  				 @if(!empty($data))
                                        @foreach($data as $item)    
                                                      	
                                            <th>{{ $item->name ??'' }} [{{ date('h:i A', strtotime($item->frome_time ))  }} {{ date('h:i A', strtotime($item->to_time )) }}]</th>
                                          
                                    @endforeach
                                    @endif
				                  </tr>
                </thead>
                <tbody>
                    @php
                    
                
    	$day = 1;
			@endphp	  
            @if(!empty($monthDate))
                                        @foreach($monthDate as $week)  
                                        <tr>

                                            <td>{{$week}}</td>
                                            <!-- <td>Monday</td> -->
                                            @if(!empty($data))
                                            <input type="hidden" name="day[]" value="{{$week}}">
                                                @foreach($data as $item)  
                                                @php
                                                $sunData = DB::table('food_menu_lists')->where('mess_food_routine_id','=',$item->id)->where('day_name','=',$week)->first();
                                                @endphp
                                                    <input type="hidden"  name="mess_food_routine[{{$week}}][]" class="form-control" value="{{$item->id ?? ''}}">
                                                    <td><input type="text" name="names[{{$week}}][]" class="form-control" value="{{$sunData->name ?? ''}}"></td>
                                                @endforeach
                                            @endif

                                                

                                            </tr>                                          
                                    @endforeach
                                    @endif
      
       	<td colspan="6"><center><input type="submit"  value="Save" class="btn btn-primary"></center></td>
       	</tr>
       	</tbody>
                </table>
           </div>
      </div>
      </div>
      </div>
      </div>
      </div>
    </section>
</div>



<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('hostelExpensesHeadeDelete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('messages.Are you sure you want to delete') }}  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
         </div>
       </form>
    </div>
  </div>
</div>

@endsection      