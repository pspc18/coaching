@extends('layout.app') 
@section('content')


 
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; View Penalty Form</h3>
							<div class="card-tools"> <a href="{{url('penalty/add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-aro-left"></i> Back</a> </div>
						</div>
					
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead class="bg-primary">
          <tr role="row">
                      <th>Sr. No.</th>
                            
                            <th>Class</th>
                            <th>Student Section</th>
                            <th>Student Name</th>
                            <th>Student Roll No.</th>
                            <th>Pelanty Amount</th>
                            <th>Pelanty Reason</th>
                            <th>Pelanty Remark</th>
                            <th>Action</th>
                             
                      
                  </thead>
                  <tbody>
                      
                      @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                                
                                <td>{{ $item['class']  }}</td>
                                <td>{{ $item['student_section']  }}</td>
                                <td>{{ $item['name']  }}</td>
                                <td>{{ $item['student_no']  }}</td>
                                <td>{{ $item['pelanty_amount']  }}</td>
                                <td>{{ $item['pelanty_reason']  }}</td>
                                <td>{{ $item['pelanty_remark']  }}</td>
                                
                                <td>
                                    <a  class="p-4" data-toggle="dropdown" aria-expanded="false">
                                     <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu" style="">
                                      <a href="{{url('penalty/edit',$item->id)}}"><li class="dropdown-item text-primary">Edit</li></a>
                                      <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData"><li class="dropdown-item text-danger">Delete</li></a>
                                       
                                    </ul>
                                </td>
                            </tr>
                      @endforeach
                @endif
            </tbody>
                  </table>
                  
              </div>
              
            </div>
           
        
        
      </div>
      
    </section>
    
  </div>
    
</div>
   
   
     <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>


<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>

 The Modal 
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

       Modal Header 
      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

       Modal body 
      <form action="{{ url('penalty/delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">Are you sure you want to delete  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
         </div>
       </form>
</div>
	</div>
		</div>
	</div>
		</div>
	</div>
	</section>
</div>
@endsection