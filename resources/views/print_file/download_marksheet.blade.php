@php
$classType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')

<style>
    .pdf_size{
        margin-top: 2%;
        margin-left: -11%;
    }
</style>
<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
                    
                            <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Download Marks') }} </h3>
                       
							<div class="card-tools cl-6"> 
							
							   
							    <a onclick="history.back()" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a> 
                            </div>
						</div>
						
					
						<div class="card-body">
   
                        <div class="row">
                            <div class="col-md-8">
                            <form id="quickForm" action="{{ url('download_marksheet') }}" method="post" class="was-validated" >
                                @csrf
                                <div class="row">
                                <div class="col-md-3">
                        			<div class="form-group">
                        				<label style="color:red;">{{ __('messages.Class') }}*</label>
                        				<select class="select2 form-control @error('class_name') is-invalid @enderror " id="class_type_id" name="class_name" required>
                                <option value="">{{ __('messages.Select') }}</option>
                                                                @if(!empty($classType))
                                                                  @foreach($classType as $type)
                                                                  <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                                                  @endforeach
                                                                @endif
                                        </select>
                                           <div class="invalid-feedback">Please fill out this field.</div>
                        		    </div>
      </div>
                        		<div class="col-md-3">
                        			<div class="form-group">
                        				<label>{{ __('messages.Section') }}</label>
                        					<select class="select2 form-control @error('section_id') is-invalid @enderror section_id" id="section_id_" name="section_id">
                        				   <option value="" >{{ __('messages.Select') }}</option>
                                                 @if(!empty($section))
                                                         @foreach($section as $section)
                                                         <option value="{{ $section->section_id ?? ''  }}" {{ ($section->section_id == $search['section_id']) ? 'selected' : '' }}>{{ $section['Section']['name'] ?? ''  }}</option>
                                                         @endforeach
                                                         @endif
                                        </select>
                        		         </div>
              </div>
                                <div class="col-md-3 div_stream_id_" style="display:{{($search['class_type_id'] < 10) ? 'none' : 'block'}}">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('Stream') }}*</label>
                                        <select class="form-control section_search_id @error('stream_id') is-invalid @enderror stream_id" id="stream_id_" name="stream_id" >
                                        	<option value="">{{ __('messages.Select') }}</option>
                                                     <option value="Arts">Arts</option>
                                                     <option value="Science">Science</option>
                                                     <option value="Commerce">Commerce</option>
                                        </select>
                                       <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                <div class="form-group">
                                                			<label style="color:red;">{{ __('messages.Exam Name') }}*</label>
                                                			<select class="select2 form-control @error('exam_id') is-invalid @enderror exam_id_" id="exam_id" name="exam_id" required>
                                                		
                                                              <option value="" >{{ __('messages.Select') }}</option>
                                                              @if(!empty($examlist))
                                                              @foreach($examlist as $item)
                                                              
                                                              <option value="{{ $item->exam_id ?? ''  }}" {{ ($item->exam_id == $search['exam_id']) ? 'selected' : '' }}>{{ $item->exam_name ?? ''  }}</option>
                                                                @endforeach
                                                              @endif
                                                             
                                                            </select>
                                                              <div class="invalid-feedback">Please fill out this field.</div>
                                                	    </div>
                             </div>
                                <div class="col-md-1 col-6">
                                    <lable>&nbsp;</lable><br>
                                <button type="submit" class="btn btn-primary">{{ __('messages.Search') }}</button>
                                </div>
                            </div>
                            </form>
                            </div>
                       <!--  <div class="col-md-4">
                        @if(!empty($data))
                              
                        <div class="col-md-2 col-6">
                        <form id="quickForm_find1" action="{{ url('exam_report_card') }}" method="post" >
                            @csrf 
    
                            <input type="hidden"  name="class_type_id" value="{{ $search['class_type_id'] ?? ''}}">
                            <input type="hidden"  name="exam_id" value="{{ $search['exam_id'] ?? ''}}">
                            <input type="hidden"  name="student_id" value="{{ $search['student_id'] ?? ''}}">
                            <input type="hidden"  name="section_id" value="{{ $search['section_id'] ?? ''}}">
                            <label for="" class="text-white">Pdf</label>
                            
                            <button type="submit" {{($data->count() == 0) ? 'disabled' : ''}}  class="btn btn-primary">{{ __('PDF') }}</button>
                        </form>
                        </div>
                        
                        @endif
                        </div>-->
    </div>
               
	               
                
                
        <div class="row m-2">
         <div class="col-12 mt-5">
             <div class="card">
   <div class="card-header border-transparent">
      <div class="card-tools">
         <button type="button" class="btn btn-primary btn-tool" data-card-widget="collapse">
         <i class="fa fa-minus"></i>
         </button>
      </div>
   </div>
   <div class="card-body p-0" style="display: block;">
      <div class="table-responsive">
           <table class="table table-bordered table-striped">
                <thead>
                <tr>
				<th>S No</th>
                <th>Adm Number</th>
                <th>Student's Name</th>
                 <th>Father's Name</th>
                 <th>Action</th>
                </tr>
                </thead>
				<input type="hidden" name="sub_count" value="5" />
			
				<tbody>
				<input type="hidden" value="{{$count ?? ''}}" name="sub_count" />
				@if(!empty($data)) 
				@if($data->count() != 0)
                @foreach($data as $key=> $item)
                <tr>
                    <td>{{$key+1 ?? ""}}</td>
                    <td>{{$item->admissionNo ?? ""}}</td>
                    <td>{{$item->name ?? ""}}</td>
                    <td>{{$item->father_name ?? ""}}</td>
                    <td>
                         <div class="d-flex">
                          <form action="{{url('print_report_card')}}" target="blank" method="post">
                            @csrf
                                <input type="hidden" name="student_id" value="{{$item->admissionNo ?? ""}}" />
                                <input type="hidden" name="exam_id" value="{{$search['exam_id'] ?? ''}}" />
                                <input type="hidden" name="class_type_id" value="{{$search['class_type_id'] ?? ''}}" />
                                <input type="hidden" name="section_id" value="{{$search['section_id'] ?? ''}}" />
                                <button type="submit" class="btn btn-secondary" > <i class="fa fa-print"></i> </button> 
                                <!--<button type="submit" name="pdf"  value="pdf" class="btn btn-success"> <i class="fa fa-download"></i> </button>-->
                            </form>
                         </div>
                    </td>

                         <input type="hidden" name="admission_id[]" value="{{$item->admissionNo ?? '' }}"  class="form-control"  style="width:100px">
                                                      
                        </tr>
                        @endforeach
                        
                       
                        @else
                        <tr>
                            <td class="text-center" colspan="12">No Data Found</td>
                        </tr>
                        @endif
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