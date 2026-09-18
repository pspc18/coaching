@extends('layout.app') 
@section('content')
@php
$classType = Helper::classType();

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
                   
                     <a onclick="history.back()" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a> 
                  </div>
               </div>
               <div class="card-body">
                  <form id="quickForm" action="{{ url('fill_marks') }}" method="post"  class="was-validated">
                     @csrf 
                     <div class="row">
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                           <div class="form-group">
                              <label >{{ __('messages.Section') }}</label>
                              <select class="select2 form-control @error('section_id') is-invalid @enderror section_id" id="section_id_" name="section_id" >
                                 <option value="" >{{ __('messages.Select') }}</option>
                                 @if(!empty($section))
                                 @foreach($section as $section)
                                 <option value="{{ $section->section_id ?? ''  }}" {{ ($section->section_id == $search['section_id']) ? 'selected' : '' }}>{{ $section['Section']['name'] ?? ''  }}</option>
                                 @endforeach
                                 @endif
                              </select>
                               <div class="invalid-feedback">Please fill out this field.</div>
                           </div>
                        </div>
                          <div class="col-md-3 div_stream_id_" style="display: {{($search['class_type_id'] < 10) ? 'none;' : 'block;'}}">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('Stream') }}*</label>
                                        <select class="form-control section_search_id @error('stream_id') is-invalid @enderror stream_id" id="stream_id_" name="stream_id" >
                                        	<option value="">{{ __('messages.Select') }}</option>
                                                     <option value="Arts" {{ ("Arts" == $search['stream_id']) ? 'selected' : '' }}>Arts</option>
                                                     <option value="Science" {{ ("Science" == $search['stream_id']) ? 'selected' : '' }}>Science</option>
                                                     <option value="Commerce" {{ ("Commerce" == $search['stream_id']) ? 'selected' : '' }}>Commerce</option>
                                        </select>
                                       <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                </div>
                               
                        <div class="col-md-2">
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
                       <!-- <div class="col-md-2">
                           <div class="form-group">
                              <label>{{ __('Subject Type') }}</label>
                              <select class="select2 form-control" id="subject_type" name="subject_type" >
                                 <option value="0">All</option>
                              </select>
                           </div>
                        </div>-->
                        <div class="col-md-1 col-6">
                           <label for="" class="text-white">Search</label>
                           <button type="submit" class="btn btn-primary">{{ __('messages.Search') }}</button>
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
                                 <input type="hidden" name="stream_id" value="{{$search['stream_id'] ?? ''}}" class="form-control" >
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
                                       @if(!$data_null)
                                       <input type="hidden" name="fill_min_max_id[]" value="{{$item->id ?? ''}}" class="form-control" required="">
                                       @endif
                                       <input type="hidden" name="subject_id[]" value="{{$item->id ?? ''}}" class="form-control" required="">
                                      
                                       <input type="text" name="exam_maximum_marks[]"  value="{{$item->exam_maximum_marks ?? '100'}}" class="form-control" readonly>
                                    </td>
                                    <td> <input type="text" name="exam_minimum_marks[]"  value="{{$item->exam_minimum_marks ?? '30'}}" class="form-control" readonly></td>
                                    <!--<td>-->
                                    <!--   <select class="form-control"  name="exam_mark_add[]" required="">-->
                                    <!--      <option value="Yes">Yes</option>-->
                                    <!--      <option value="No">No</option>-->
                                    <!--   </select>-->
                                    <!--</td>-->
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
                                 <input type="hidden" value="{{ $search['section_id'] ?? '' }}" name="section_id">
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
                                    <input type="hidden" value="{{$count ?? ''}}" name="sub_count" />
                                    @if(!empty($data2)) 
                                  
                                    @foreach($data2 as $key=> $item)
                                    <tr>
                                       <td>{{$key+1 ?? ""}}</td>
                                       <td>{{$item->admissionNo ?? ""}}</td>
                                       <td>{{$item->name ?? ""}}</td>
                                       <td>{{$item->father_name ?? ""}}</td>
                                       <input type="hidden" name="admission_id[]" value="{{$item->admissionNo ?? ""}}"  class="form-control"  style="width:100px">
                                       @if(!$data_null && count(Helper::getMarks($search['exam_id'] ?? '',$search['class_type_id'] ?? '' , $item->admissionNo ?? "")) >0) 
                                       @foreach(Helper::getMarks($search['exam_id'] ?? '',$search['class_type_id'] ?? '' ,$item->admissionNo ?? "") as $marks)
                                       <td>
                                          <input type="hidden" name="fill_marks_id[]" value="{{$marks->id ?? ''}}"  style="width:100px">
                                          <input type="hidden" name="sub_id[]" value="{{$marks->subject_id ?? ''}}"  style="width:100px">
                                          <input type="text" name="student_marks[]" value="{{$marks->student_marks ?? ''}}" class="form-control numbers"  style="width:100px">
                                       </td>
                                       @endforeach
                                       @else          
                                       @if(!empty($data1)) 
                                       @foreach($data1 as $key => $item )
                                       <td>
                                          <input type="hidden" name="sub_id[]" value="{{$item->id ?? ''}}"  style="width:100px">
                                          <input type="text" name="student_marks[]" value=""  class="form-control"  style="width:100px">
                                       </td>
                                       @endforeach
                                       @endif
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
    $(document).ready(function(){
       
        
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

