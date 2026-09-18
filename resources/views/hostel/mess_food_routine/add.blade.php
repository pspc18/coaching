@php
    $getHostel = Helper::getHostel();
    use Carbon\Carbon;
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-12 pl-0">
                <div class="card card-outline card-orange ml-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-inbox"></i> &nbsp;{{ __('hostel.Mess Food Routine Add') }}</h3>
                <div class="card-tools">
                       <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i
                                        class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                </div>
                 </div>    
                         <form id="quickForm" action="{{ url('messFoodRoutineAdd') }}" method="post" enctype="multipart/form-data">   
                        @csrf
                            <div class="row m-2">
                                <div class="col-md-3">
                        			<label style="color:red;">{{ __('hostel.Routine name') }}*</label>
                    				<input type="text" name="name" id="name" class="form-control" Placeholder="Routine Name">
                            	</div>    
                                <div class="col-md-3">
                        			<label style="color:red;">{{ __('hostel.Routine Time Frome') }}*</label>
                    				<input type="time" name="frome_time" id="frome_time" class="form-control" >
                            	</div>                 	
                                <div class="col-md-3">
                        			<label style="color:red;">{{ __('hostel.Routine Time To') }}*</label>
                    				<input type="time" name="to_time" id="to_time" class="form-control" >
                            	</div>                 	
                              
                            </div>
                            <div class="row m-2">
                                <div class="col-md-12 text-center" id="floor_list_show">
                                <button type="submit" class="btn btn-primary ">{{ __('common.submit') }}</button>

                                </div>
                            </div>
                            </form>
                </div> 
                
                
            </div>
                
              <div class="col-md-12 pl-0">
                <div class="card card-outline card-orange ml-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-building"></i> &nbsp;{{ __('hostel.Routine List') }} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                    </div>
                     </div>                 
                    <div class="row m-2">
                        <div class="col-md-12">
                    	</div>
                        <div class="col-md-12">
                           <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                <thead class="bg-primary">   
                                    <tr role="row">
                                        <th>{{ __('common.SR.NO') }}</th>
                                        <th>{{ __('hostel.Routine Name') }}</th>
                                        <th>{{ __('hostel.Time Frome') }}</th>
                                        <th>{{ __('hostel.Time To') }}</th>
                                        <th>{{ __('common.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @if(!empty($data))
                                    @php
                                       $i=1;
                                    @endphp
                                        @foreach($data as $item)     
                                        @php
                                            $dateTime = Carbon::parse($item['frome_time']);
                                            $dateto = Carbon::parse($item['to_time']);
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['name'] ??'' }}</td>
                                            <td>{{ $dateTime->format('h:i A') }}</td>
                                            <td>{{ $dateto->format('h:i A') }}</td>
                                            <td>
                                    <a href="{{url('messFoodRoutineEdit',$item->id)}}" class="btn btn-primary  btn-xs ml-3 tooltip1" title1="Edit Student Registration"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData btn btn-primary  btn-xs ml-3 tooltip1" title1="Delete Student Registration"><i class="fa fa-trash-o"></i></a>  
                                               
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
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('messFoodRoutineDelete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('common.Delete') }}</button>
         </div>
       </form>
    </div>
  </div>
</div>

@endsection      