@php
  $classType = Helper::classType();
  $studentexamview = Helper::studentexamview();
  $getStudents = Helper::getStudents();
  $date = date('Y-m-d');

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
                    
                            <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('messages.My Examinations') }} </h3>
                       
							<div class="card-tools cl-6"> 
							
							    <a href="{{url('add_exam_result')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('messages.Add') }} </a> 
							    <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a> 
                          

                                
                            </div>
						</div>
						<div class="card-body">

                    <form id="quickForm" action="{{ url('view_exam_result') }}" method="post" >
                        @csrf 
                    <div class="row">
                        <div class="col-md-2">
                        		<div class="form-group">
                        			<label>{{ __('messages.Exam Name') }}</label>
                        			<select class="select2 form-control" id="exam_id" name="exam_id" >
                        			<option value="">{{ __('messages.Select') }}</option>
                                     @if(!empty($studentexamview)) 
                                          @foreach($studentexamview as $type)
                                          <option value="{{ $type->id}} " {{ ( $type->id == $search['exam_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                                             
                                          @endforeach
                                      @endif
                                    </select>
                        	    </div>
                        	</div>
                        <div class="col-md-2">
                        		<div class="form-group">
                        			<label>{{ __('messages.Student Name') }}</label>
                        			<select class="select2 form-control" id="stu_id" name="stu_id" >
                        			<option value="">{{ __('messages.Select') }}</option>
                                     @if(!empty($getStudents)) 
                                          @foreach($getStudents as $type)
                                             <option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == 3 ?? '' ) ? 'hidden' : '' }} >{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                        	    </div>
                        	</div>
            	<!--	<div class="col-md-4">
            			<div class="form-group">
            				<label>{{ __('messages.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="Search by keywords" value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>     -->                	
                        <div class="col-md-1 col-6">
                             <label for="" class="text-white">Search</label>
                    	    <button type="submit" class="btn btn-primary">{{ __('messages.Search') }}</button>
                    	</div>
                        <div class="col-md-1 col-6">
                            <label for="" class="text-white">Print</label>
                         <a href="{{url('exam_print')}}" class="btn btn-success  btn-sm" class="media"><i class="fa fa-print"></i>Print  </a> 

                    	</div>
                    			
                    </div>
                </form> 

                
                
        <div class="row m-2">
          <div class="col-12">
               <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                  <tr role="row">
                      <th>{{ __('messages.Sr.No.') }}</th>
                      <th class="text-center">{{ __('messages.Admission. No') }}</th>
                      <th >{{ __('messages.Name') }}</th>
                      <th>{{ __('messages.Exam Name') }}</th>
                      <th>{{ __('messages.Total Marks') }}</th>
                      <th>{{ __('messages.Obtain Marks') }}</th>
                      <th>{{ __('messages.Percentage') }}</th>
                      <th>{{ __('messages.Rank') }}</th>
                      <th>{{ __('messages.Action') }}</th></tr>
                  </thead>
                  <tbody id="product_list_show">
                  
                  @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr >
                                <td >{{ $i++ }}</td>
                                <td class="text-center">{{ $item['student_id'] ?? ''}}</td>
                                <td class="myBtn" style="cursor:pointer;">{{ $item['name'] ?? '' }} </td>
                                <td>{{ $item['exam_name'] ?? ''}}</td>
                                <td>{{ $item['marks'] ?? ''}}</td>
                                <td>{{ $item['total_marks'] ?? ''}}</td>
                                <td>{{ $item['percentage'] ?? ''}}%</td>
                                <td>{{ $item['rank'] ?? ''}}</td>
                               
                                
                                
                                <td>
                                       

                                          <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData"><li class="dropdown-item text-danger" title="Delete"><i class="fa fa-trash-o text-danger"></i> </li></a>                                        
                                        
                                        

                                </td>
                                
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
		</div>
	</section>
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
        <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('delete_exam_result') }}" method="post">
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