@php
  $classType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">View Fees Structure</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
            <div class="card">
                    <form id="quickForm" action="#" method="post" >
                        @csrf 
            <div class="row m-2">
                 
                <div class="col-md-4">
            		<div class="form-group">
            			<label>Class</label>
            			<select class="form-control" id="class_type_id" name="class_type_id" >
            			<option value="">Select</option>
                         @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>

                <div class="col-md-1 ">
                     <label for="">Search</label>
            	    <button type="button" class="btn btn-primary" onclick="SearchValue()">Search</button>
            	</div>
            			
            </div>
        </form>
</div>
        <div class="row ">
          <div class="col-12">
            <div class="card">
                <div class="card-body">
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                  <thead>
                  <tr role="row">
                      <th>Sr.No.</th>
                      <th>Class</th>
                       <th>Title</th>
                      <th>Fees</th>
                      <th>Action</th>
                      
                  </thead>
                  <tbody class="product_list_show">
                 
                  </tbody>
                  </table>
              </div>
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
      <form action="{{ url('fees_structure_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">Are you sure you want to delete  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
         </div>
       </form>

    </div>
  </div>
</div>


<script>
        function SearchValue() {
            var class_type_id = $('#class_type_id :selected').val();
         var basurl = "{{ url('/') }}";
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: basurl+'/fees_structure_data',
                data: {class_type_id:class_type_id},
                 //dataType: 'json',
                success: function (data) {
                   
                  
                    $('.product_list_show').html(data);
                   
                }
              });
                         
        };

</script>
@endsection    
