@extends('layout.app') 
@section('content')

<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary flex_items_toggel">
							<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;{{ __('staff.Drop Teacher List') }}</h3>
							<div class="card-tools"> <a href="{{url('staff_file')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> <span class="Display_none_mobile">{{ __('common.Back') }} </span></a> </div>
						</div>
				
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline padding_table">
          <thead>
                  <tr role="row">
                      <th>{{ __('common.S.NO') }}</th>
                            <th>{{ __('common.Name') }}</th>
                            <th>{{ __('common.Contact No') }}</th>
                            <th>{{ __('common.E-Mail') }}</th>
                            <th>{{ __('common.Updated At') }}</th>
                            <th>{{ __('common.Action') }}</th>
                    </tr>
          </thead>
                  <tbody>
                      
                      @if(!empty($data))
                      
                      
                        @php
                      
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                <td>{{ $item['mobile']  }}</td>
                                <td>{{ $item['email']  }}</td>
                                <td>{{date('d-M-Y', strtotime($item['updated_at'])) ?? '' }}</td>
                               
                                
                                
                                 <td>
                                <a href="javascript:;"  data-id='{{$item->teacher_id}}' data-bs-toggle="modal" data-bs-target="#Modal_id1"  class="Re_join btn btn-xs btn-primary"  title="Re-Join">Re-Join</a> 
                                <a href="{{url('drop_teacher_letter')}}/{{$item->teacher_id}}" target="blank"><i class="fa fa-print btn btn-xs btn-primary"></i> </li></a>

                               
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
      
    </section>
    
  </div>
    
</div>
        
        
 <style>
     .padding_table thead tr{
    background: #002c54;
    color:white;
    }
        
    .padding_table th, .padding_table td{
         padding:5px;
         font-size:14px;
    }
 </style>  
 
 
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
  
  $('.Re_join').click(function() {
  var drop_status = $(this).data('id'); 
  
  $('#staf_id').val(drop_status); 
  } );
 </script>


<!-- The Modal -->
<div class="modal" id="Modal_id1">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">Re-Join Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('drop_teacher') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type="hidden" id="staf_id" name="staf_id">
              <input type="hidden" id="drop_status" name="drop_status" value="1">
              <h5 class="text-white">Are you sure you want to make Re-Join  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Ok</button>
         </div>
       </form>

    </div>
  </div>
</div>   

@endsection 