@extends('layout.app') 
@section('content')
@php
$classType = Helper::classType();
$studentexamview = Helper::studentexamview();

@endphp
<div class="content-wrapper">
<section class="content pt-3">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
               <div class="card-header bg-primary">
                  <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Scholastic Non-Scholastic perfomance marks') }} </h3>
                  <div class="card-tools cl-6"> 
                   
                     <a href="{{ url('examination_dashboard') }}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }}  </a> 
                  </div>
               </div>
               <div class="card-body">
                  <form id="quickForm" action="{{ url('performance_marks') }}" method="post"  class="was-validated">
                     @csrf 
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label style="color:red;">{{ __('messages.Class') }}*</label>
                              <select class="select2 form-control " id="class_type_id" name="class_name" required>
                                 <option value="">{{ __('messages.Select') }}</option>
                                 @if(!empty($classType))
                                 @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                 @endforeach
                                 @endif
                              </select>
                              
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label style="color:red;">{{ __('messages.Exam Name') }}*</label>
                              <select class="select2 form-control" id="term_id" name="term_id" required >
                                 <option value="">{{ __('messages.Select') }}</option>
                             

                                     @if(!empty($studentexamview)) 
                                     @foreach($studentexamview as $type)
                                     
                                     <option value="{{ $type->id}} " {{ ( $type->id == $search['term_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                                 
                                     @endforeach
                                     @endif
                                     @error('exam_id')
                                     <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                     </span>
                                     @enderror
                                  </select>
                                     
                             
                           </div>
                        </div>
                     
                        <div class="col-md-1 col-6">
                           <label for="" class="text-white">Search</label>
                           <button type="submit" class="btn btn-primary">{{ __('messages.Search') }}</button>
                        </div>
                     </div>
                  </form>
                  @if(!empty($data1))    
                  <div class="row m-2">
                     <div class="col-12">
                        <form action={{url('performance_marks_submit')}} method="post">
                           @csrf
                           
                         

                           <div class="col-md-5 col-12">

 @if(!empty($data1)) 
                                           <table class='text-center table table-bordered table-striped'>
                                               <thead>
                                                   <tr class='bg-danger'><th colspan='2'>Mark Check For Make Subject Non Co-Scholastic</th></tr>
                                               </thead>
                                         <tbody>
                                       @foreach($data1 as $key => $item )
                                       
                                       @php
                                       
                                       $check_for_mark = DB::table('performance_marks')
                                       ->where('class_type_id',$search['class_type_id'] ?? '')
                                       ->where('performance',1)
                                       ->where('term_id',$search['term_id'] ?? '')
                                       ->where('subject_id',$item->id)
                                       ->where('branch_id',Session::get('branch_id'))
                                     ->where('session_id',Session::get('session_id'))
                                       ->whereNull('deleted_at')->first();
                                    @endphp
                                             <tr>
                                                 @if(!empty($check_for_mark))
                                                 <td><input type='checkbox' name='performance[{{$item->id}}]' checked value="1"/></td>
                                                @else
                                                 <td><input type='checkbox' name='performance[{{$item->id}}]' value="1"/></td>
                                                @endif
                                                
                                                 <td>{{$item->name ?? ''}}<br></td>
                                             </tr>
                                        
                                          
                                      
                                      
                                       @endforeach
                                        </tbody>
                                     </table>
                                       @endif
</div>
                           <div class="col-12 mt-1">
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
                                            <input type="hidden" value="{{ $search['term_id'] ?? '' }}" name="term_id">
                                 <input type="hidden" value="{{ $search['class_type_id'] ?? '' }}" name="class_type_id">
                                       @foreach($data1 as $key => $item )
                                       
                                       <th style="text-transform: capitalize;"> 
                                          
                                        {{$item->name ?? ''}}
                                        <input type="hidden" name="subject_id[]" value="{{$item->id ?? ''}}" class="form-control" >
                                        </th>
                                       @endforeach
                                       @endif
                                    </tr>
                                 </thead>
                              
                                 <tbody>
                                     @if($data2->count() != 0)
                                    <!--<input type="text" value="{{$count ?? ''}}" name="sub_count" />-->
                                    @if(!empty($data2)) 
                                  
                                    @foreach($data2 as $key=> $item)
                                    
                                 @php
                                 $stream = [];
                                 
                                 if(!empty($item->stream_subject))
                                 {
                                 $stream = explode(',',$item->stream_subject);
                                 }
                                 
                                 @endphp
                                    <tr>
                                       <td>{{$key+1 ?? ""}}</td>
                                       <td>{{$item->admissionNo ?? ""}}</td>
                                       <td>{{$item->first_name ?? ""}} {{$item->last_name ?? ""}}</td>
                                       <td>{{$item->father_name ?? ""}}</td>
                                       <input type="hidden" name="admission_id[]" value="{{$item->id ?? ""}}"  class="form-control"  style="width:100px">
                                      @if(!empty($data1)) 
                                       @foreach($data1 as $key => $item1 )
                                       @php
                                         $old_marks = DB::table('performance_marks')
                                     ->where('term_id', $search['term_id'] ?? '')
                                     ->where('admission_id',$item->id)
                                      ->where('subject_id',$item1->id ?? '')
                                     ->where('branch_id',Session::get('branch_id'))
                                     ->where('session_id',Session::get('session_id'))
                                     ->whereNull('deleted_at')->first();
                                        @endphp
                                        <td>
                                            
                                            
                                             @php
                                            $class_order = DB::table('class_types')->where('id',$search['class_type_id'] ?? '')->whereNull('deleted_at')->first();
                                             
                                            @endphp
                                            
                                           
                                            
                                            @if($class_order->orderBy > 10)
                                            
                                            
                                            @if (in_array(($item1->id ?? ''), $stream))
                                             @if(!empty($old_marks))
                                             
                                           
                                            
                                               <input type='hidden' name='performance_marks_id[]' value='{{$old_marks->id ?? ''}}'/>
                                               @else
                                                 <input type='hidden' name='performance_marks_id[]' value=''/>
                                              
                                            @endif
                                              <input type='text' name='student_marks[]' value='{{$old_marks->student_marks ?? ''}}'/>
                                         <input type='hidden' name='check_null[]' value='{{$old_marks->student_marks ?? null}}'/>
                                        <input type='hidden' name='subject_id_fill[]' value='{{$item1->id ?? ''}}'/>
                                            
                                            @else
                                              <input type='hidden' name='performance_marks_id[]' value=''/>
                                          <input type='text' name='student_marks[]' value='' placeholder='Subject Not Assigned' readonly/>
                                         <input type='hidden' name='check_null[]' value='{{$old_marks->student_marks ?? null}}'/>
                                        <input type='hidden' name='subject_id_fill[]' value='{{$item1->id ?? ''}}'/>
                                                          @endif                                        
                                            
                                           
                                            
                                            
                                            @else
                                             @if(!empty($old_marks))
                                           
                                            
                                               <input type='hidden' name='performance_marks_id[]' value='{{$old_marks->id ?? ''}}'/>
                                               @else
                                                 <input type='hidden' name='performance_marks_id[]' value=''/>
                                              
                                            @endif
                                              <input type='text' name='student_marks[]' value='{{$old_marks->student_marks ?? ''}}'/>
                                         <input type='hidden' name='check_null[]' value='{{$old_marks->student_marks ?? null}}'/>
                                        <input type='hidden' name='subject_id_fill[]' value='{{$item1->id ?? ''}}'/>
                                            @endif
                                           
                                       
                                            
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
    	         	    $("#term_id").html(data);
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

