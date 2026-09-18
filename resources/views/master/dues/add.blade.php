
@extends('layout.app') 
@section('content')

                                                                    
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h1 class="m-0">Dues</h1>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{url('master_dashboard')}}">Back</a></li>
                </ol>
                </div>
            </div>
        </div>
    </div>
    <hr class="bg-danger m-2">
<!--<div class="card">
    <form id="quickForm" action="" method="post" >
        @csrf
		<div class="row mb-2 m-2">
		    <div class="col-md-6">
				<div class="form-group">
					<label for="add_class">Role Add :</label>
					<input type="text" class="form-control @error('role') is-invalid @enderror " id="role" name="role" placeholder="Add Role" value="{{old('role')}}">
					@error('role')
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
    -->

    
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
      <form action="{{ url('role_delete') }}" method="post">
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

