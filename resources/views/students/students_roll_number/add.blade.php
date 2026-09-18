@extends('layout.app') 
@section('content')
    
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Class</h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{url('admin_dashboard')}}">Back</a></li>
                </ol>
                </div>
            </div>
        </div>
    </div>
    <hr class="bg-danger m-2">
<div class="card">
    <form id="quickForm" action="{{ url('students/roll/number/add') }}" method="post" >
        @csrf
		<div class="row mb-2 m-2">
		    <div class="col-md-4">
				<div class="form-group">
					<label for="class" style="color:red;"> Class :*</label>
					<input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" placeholder=" Class" value="{{old('name')}}">
					@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="section">section :</label>
					<input type="text" class="form-control @error('section') is-invalid @enderror " id="section" name="section" placeholder="section" value="{{old('section')}}">
					@error('section')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

		</div>
	
	
	
        <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-info ">Submit</button><br><br>
		</div>
    	</form>
    </div>
    
<div class="card">
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>Sr. No.</th>
                    <th> Class </th>
                    <th>section </th>
                    
                     <th>Edit/Delete</th>
          </thead>
          <tbody>
              
              @if(!empty($class))
                @php
                   $i=1
                @endphp
                @foreach ($class  as $item)
                <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item['name']  }}</td>
                        <td>{{ $item['section']  }}</td>

                       
                        <td><a class=" text-center" href="{{url('students/roll/number/edit',$item->id)}}" ><i class="fa fa-edit"></i></a>
                   
                   <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData" ><i style= "color:red;"class="fa fa-trash-alt"></i></a> 
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
    
    
    
    
 <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
        
        
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>
  <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>-->

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('roll_number_delete') }}" method="post">
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
    
    

@endsection    