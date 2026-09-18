@extends('layout.app') 
@section('content')
@php
$classType = Helper::examPanelClassType();
@endphp
<div class="content-wrapper">
<section class="content pt-3">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
               <div class="card-header bg-primary">
                  <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Fill Marks') }} </h3>
                  <div class="card-tools cl-6"> 
                   
                     <a href="{{ url('examination_dashboard') }}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }}  </a> 
                  </div>
               </div>
               <div class="card-body">
                  <form id="quickForm" action="{{ url('fill_marks_by_excel') }}" method="post"  class="was-validated" enctype="multipart/form-data">
                  <!--<form id="quickForm" action="{{ url('fill_marks_by_excel') }}" method="post"  class="was-validated" enctype="multipart/form-data">-->
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
                              <label style="color:red;">{{ __('messages.Exam Name') }}*</label>
                              <select class="select2 form-control @error('exam_id') is-invalid @enderror exam_id_" id="exam_id" name="exam_id" required >
                                 <option value="">{{ __('messages.Select') }}</option>
                               @if(!empty($examlist))
                                      @foreach($examlist as $item)
                                      
                                      <option value="{{ $item->exam_id ?? ''  }}" {{ ($item->exam_id == $search['exam_id']) ? 'selected' : '' }}>{{ $item->exam_name ?? ''  }}</option>
                                        @endforeach
                                      @endif
                               
                              </select>
                                 <div class="invalid-feedback">Please fill out this field.</div>
                           </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label style="color:red;">{{ __('student.Upload Excel') }}* </label>
                            <input class="form-control @error('excel') is-invalid @enderror" type="file" id="excel" name="excel" accept=".xlsx, .csv" required>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                     
                        <div class="col-md-1 col-6">
                           <label for="" class="text-white">Search</label>
                           <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                     </div>
                  </form>
                  @if(!empty($data1))    
                  <div class="row m-2">
                     <div class="col-12">
                        <form action={{url('fill_marks_submit')}} method="post">
                           @csrf
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
         <table class="table table-bordered table-striped dataTable dtr-inline">
                            <thead>
                                <tr>
                                    <th>S No</th>
                                    <th>Subject Name</th>
                                    <th>Maximum Marks</th>
                                    <th>Minimum Marks</th>
                                    <!--<th>Marks Add in Total</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                 
                                @if($data1->count() != 0)
                                 @if(!empty($data1)) 
                                 @foreach($data1 as $key => $item )
                                 <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                        @if($item->sub_name != '')
                                        {{$item->sub_name ?? ''}}
                                        
                                        @else 
                                        {{$item->name ?? ''}}
                                        @endif
                                        </td>
                                    <td>
                                     
                                     @php
                                     
                                     $old_value = DB::table('fill_min_max_marks')
                                     ->where('exam_id', $search['exam_id'] ?? '')
                                     ->where('class_type_id',$search['class_type_id'] ?? '')
                                     ->where('subject_id',$item->id ?? '')
                                     ->where('branch_id',Session::get('branch_id'))
                                     ->where('session_id',Session::get('session_id'))
                                     ->whereNull('deleted_at')->first();
                                     
                                   
                                     @endphp
                                     
                                     
                                       @if(!empty($old_value))
                                               <input type='hidden' name='fill_min_max_marks_id[]' value='{{$old_value->id ?? ''}}'/>
                                                 @else
                                                 <input type='hidden' name='fill_min_max_marks_id[]' value=''/>
                                              
                                            @endif
                                     
                                   <input type="hidden" name="subject_id[]" value="{{$item->id ?? ''}}" class="form-control" >
                                      
                                       <input type="text" name="exam_maximum_marks[]"  value="{{ $old_value->exam_maximum_marks ?? 100 }}" class="form-control" >
                                    </td>
                                    <td> <input type="text" name="exam_minimum_marks[]"  value="{{ $old_value->exam_minimum_marks ?? 30 }}" class="form-control"></td>
                                   
                                 </tr>
                                 @endforeach
                                 @endif
                                 @else
                                 <tr>
                                    <td class="text-center" colspan="12">No Data Found</td>
                                </tr>
                                @endif 
                                 <input type="hidden" value="{{ $search['exam_id'] ?? '' }}" name="exam_id">
                                 <input type="hidden" value="{{ $search['class_type_id'] ?? '' }}" name="class_type_id">
                              </tbody>
         </table>
      </div>
   </div>
</div>
                           <div class="col-12 mt-5">
                               <div class="card">
   <div class="card-header border-transparent">
      <div class="card-tools mb-1">
         <button type="button" class="btn btn-primary btn-tool" data-card-widget="collapse">
         <i class="fa fa-minus"></i>
         </button>
      </div>
   </div>
   <div class="card-body p-0" style="display: block;">
      <div class="table-responsive">
         <table class="table table-bordered table-striped m-0">
                                 <thead> 
                                    <tr>
                                       <th>S No</th>
                                       <th>Adm Number</th>
                                       <th>Student's Name</th>
                                       <th>Father's Name</th>
                                       @php
                                       $count =0;
                                       @endphp
                                       @if(!empty($data1)) 
                                       @foreach($data1 as $key => $item )
                                       <th style="text-transform: capitalize;">@if($item->sub_name != '')
                                        {{$item->sub_name ?? ''}}
                                        
                                        @else
                                        {{$item->name ?? ''}}
                                        @endif</th>
                                       @endforeach
                                       @endif
                                    </tr>
                                 </thead>
                              
                                 <tbody>
                                     @if($data2->count() != 0)
                                    <!--<input type="text" value="{{$count ?? ''}}" name="sub_count" />-->
                                    @if(!empty($data2)) 
                                  
                                    @foreach($data2 as $key=> $item)
                                    <tr>
                                       <td>{{$key+1 ?? ""}}</td>
                                       <td>{{$item->admissionNo ?? ""}}</td>
                                       <td>{{$item->first_name ?? ""}} {{$item->last_name ?? ""}}</td>
                                       <td>{{$item->father_name ?? ""}}</td>
                                       <input type="hidden" name="admission_id[]" value="{{$item->id ?? ""}}"  class="form-control"  style="width:100px">
                                      @if(!empty($data1)) 
                                       @foreach($data1 as $key => $item1 )
                                       @php
                                         $old_marks = DB::table('fill_marks')
                                     ->where('exam_id', $search['exam_id'] ?? '')
                                     ->where('admission_id',$item->id)
                                      ->where('subject_id',$item1->id ?? '')
                                     ->where('branch_id',Session::get('branch_id'))
                                     ->where('session_id',Session::get('session_id'))
                                     ->whereNull('deleted_at')->first();
                                        @endphp
                                        <td>
                                            
                                             @php
                                            $stream = [];
                                           
                                           if($classOrderBy > 10)
                                           {
                                           $stream  = explode(",", $item->stream_subject ?? '');
                                           }
                                           
                                            @endphp

                                            @if(!empty($old_marks))
                                               <input type='hidden' name='fill_marks_id[]' value='{{$old_marks->id ?? ''}}'/>
                                               @else
                                                 <input type='hidden' name='fill_marks_id[]' value=''/>
                                              
                                            @endif
                                            
                                            
                                             @if($classOrderBy > 10)
                                         @if(in_array($item1->id, $stream) )
                                            <input type='text' name='student_marks[]' value='{{$old_marks->student_marks ?? ''}}' />
                                         @else
                                         <input type='text' name='student_marks[]' value='{{$old_marks->student_marks ?? ''}}' placeholder='Not Assigned' readonly/>
                                         @endif
                                         
                                         @else
                                           <input type='text' name='student_marks[]' value='{{$old_marks->student_marks ?? ''}}' />
                                         @endif

                                      
                                         <input type='hidden' name='check_null[]' value='{{$old_marks->student_marks ?? null}}'/>
                                        <input type='hidden' name='subject_id_fill[]' value='{{$item1->id ?? ''}}'/>
                                            
                                       </td>
                                       @endforeach
                                       
                                       @endif
                                    </tr>
                                    @endforeach
                                    @endif
                                    
                                    <tr >
                                       <td colspan="30">
                                          <center><input type="submit" name="finish" value="Submit" class=" m-3 btn   btn-success"></center>
                                       </td>
                                    </tr>
                                    @else
                                     <tr>
                                        <td class="text-center" colspan="12">No Data Found</td>
                                    </tr>
                                    @endif
                                 </tbody>
                              </table>
      </div>
   </div>
</div>
                              
                           </div>
                        </form>
                     </div>
                  </div>
                  @endif
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

<script>
$(document).ready(function(){

        $('#class_type_id').on('change', function(e){
            
                $("#stream_id").val("");
                $("#stream_subject").html("");
                $("#stream_id_div").css("display","none");
                $("#stream_subject_div").css("display","none");
                
                $(".div_stream_id_").css("display","none");
                $(".div_subject_id").css("display","block");
                $('#subject_id').prop('required',true);
                $('#stream_subject_id').prop('required',false);
                $('#stream_subject_id').val('')
                $('#stream_id_').prop('required',false);
                $('#stream_id_').val('');

                var baseurl = "{{ url('/') }}";
            	var class_type_id = $(this).val();
            	  
                $.ajax({
                    headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            	    url: baseurl + '/examData/' + class_type_id,
            	    success: function(data){
    	         	    $("#exam_id").html(data);
            	    }
            	});
        });       
        
        $('.numbers').keyup(function(){
  if ($(this).val() > 100){
      window.toastr.options = {
          "toastClass": "toast-success-create-campaign",
          "closeButton": false,
          "debug": false,
          "newestOnTop": true,
          "progressBar": false,
          "positionClass": "toast-bottom-right",
          "onclick": null,
          "showDuration": "100",
          "hideDuration": "100",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut",
          "maxOpened":1,
          "preventOpenDuplicates": true
}
     toastr.error("No numbers above 100");
    $(this).val('100');
  }
});
    });
</script>
@endsection

