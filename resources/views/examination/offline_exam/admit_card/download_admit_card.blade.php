@extends('layout.app') 
@section('content')
@php

$classType = Helper::ClassType();
$setting = Helper::getSetting();
$studentexamview = Helper::studentexamview();
@endphp
<div class="content-wrapper">
<section class="content pt-3">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
               <div class="card-header bg-primary">
                  <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; Download Admit Card</h3>
                  <div class="card-tools d-flex align-item-center"> 
                                       <button class="btn btn-primary" data-toggle="modal" data-target="#noteModal"><i class="fa fa-pencil"></i> Note</button>
                     <a href="{{ url('examination_dashboard') }}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a> 
                  </div>
               </div>
               <div class="card-body">
                     <div class="row">
                        <div class='col-md-10'>
                        <form id="quickForm_find" action="{{ url('download_admit_card') }}" method="post">
                             @csrf 
                            <div class="row">
                            <div class="col-md-3">
                               <div class="form-group">
                                  <label class="text-danger">{{ __('messages.Class') }}*</label>
                                  <select class="select2 form-control @error('class_type_id') is-invalid @enderror " id="class_type_id" name="class_type_id">
                                     <option value="">{{ __('messages.Select') }}</option>
                                     @if(!empty($classType))
                                     @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                     @endforeach
                                     @endif
                                  </select>
                                  @error('class_type_id')
                                  <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                                  </span>
                                  @enderror
                               </div>
                            </div>
                            <div class="col-md-2">
                               <div class="form-group">
                                  <label class="text-danger">{{ __('messages.Exam Name') }}*</label>
                                  <select class="select2 form-control exam_id_" id="exam_id" name="exam_id" >
                                     <option value="">{{ __('messages.Select') }}</option>
                                     @if(!empty($exam)) 
                                     @foreach($exam as $type)
                                     <option value="{{ $type->exam_id}} " {{ ( $type->exam_id == $search['exam_id'] ? 'selected' : '' ) }}>{{ $type->exam_name ?? ''  }} </option>
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
                               <button type="submit" onClick="checkValidation(event)" class="btn btn-primary">{{ __('messages.Search') }}</button>
                            </div>
                            </div>
                        </form>
                        </div>
                     </div>
                     
                    @php
                  
                  $examination_shedule_id = DB::table('examination_schedules')
                   ->where("class_type_id", $search['class_type_id'] ?? '')
                                         ->where("exam_id", $search['exam_id'])
                                         ->where("session_id", Session::get("session_id"))
                                            ->where("branch_id", Session::get("branch_id"))
                                        ->whereNull('deleted_at')->first();
                                        
                  @endphp
                  
                  @if(!empty($examination_shedule_id))
                  @if(!empty($data1))    
                  <div class="row m-2">
                     <div class="col-12">
                        <form  action="{{url('SubmitAdmitCard')}}" method="post">
                           @csrf
                           <input type="hidden" value="{{$search['stream_id'] ?? ''}}" name="stream_id"/>
                            <div class="card p-0">
                               <div class="card-body p-0" style="display: block;">
                                  <div class="table-responsive">
                                     <table   class="table table-bordered dataTable dtr-inline">
                            <thead>
                                <tr>
                                    <th style="width: 68px;padding: 3px;"> <input style="width: 32px;height: 21px;" type="checkbox" id="view1"> Sr No</th>
                                    <th>Admission no.</th>
                                    <th>Name</th>
                                    <th>Mob no.</th>
                                    <th>Father's name</th>
                                    <th>Father's mob no.</th>
                                    <th>Roll no.</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                 $i = 1;
                                 $roll_no_count = 0;
                                @endphp
                                @foreach($data1 as $key=>$item)
                               
                                <input type="hidden" id="class_type_id" name="class_type_id" value="{{$item->class_type_id ?? ''}}">
                                <input type="hidden" id="exam_id" name="exam_id" value="{{ $exam_id ?? ''}}">
                                <input type="hidden" class="form-control" name="admission_id[]" id="admission_id" readonly value="{{ $item->id ?? ''}}">
                                <tr>
                                    <td style="width: 68px;padding: 8px;">
                                       
                                            <input style="width: 32px;height: 21px;" type="checkbox" data-value="view" value="{{ $item->id ?? ''}}" name='checked_admission_id[]'class="checkbox_admission viewcheck" /></td>
                                        
                                    </td>    
                                    <td>{{ $item->admissionNo ?? ''}}</td>
                                    <td>
                                       {{ $item->first_name ?? '' }} {{ $item->last_name ?? '' }}
                                    </td>  
                                    <td>
                                        <span>{{ $item->mobile ?? '' }}</span>
                                    </td>    
                                    <td>
                                        <span>{{ $item->father_name ?? '' }}</span>
                                    </td>    
                                    <td>
                                        <span>{{ $item->father_mobile ?? '' }}</span>
                                    </td>    
                                    <td>
                                     
                                        <span>{{ $item->roll_no ?? '-' }}</span>
                               
                              </td>
                                </tr> 
                                @endforeach
                            </tbody>
                            </table>
                                  </div>
                               </div>
                               
                                    <div class="row" >
                                        <div class="col-md-12 mt-3 mb-3 text-center">
                                            <button type="button"  data-class="{{ $search['class_type_id'] ?? ''}}" data-exam="{{ $search['exam_id'] ?? ''}}" 
                                                id="quickForm_find1" class="btn btn-success"><i class="fa fa-print" style="font-size: 22px;"></i> </button>
                                            <button type="button"  data-class="{{ $search['class_type_id'] ?? ''}}" data-exam="{{ $search['exam_id'] ?? ''}}" 
                                                id="quickForm_find2" class="btn btn-success">Without Subject Admit Card </button>
                                        </div>

                                    </div>
                              
                        </form>
                     </div>
                    </div>
                  @endif
                  @else
                  @if($search['exam_id'] != "")
                    <p class="text-center text-danger">Please Create Examination Schedule For This Exam First......</p>
                  @endif
                  @endif
               </div>
            </div>
         </div>
      </div>
</section>
</div>
</div>
	</section>
</div>
<div class="modal fade" id="noteModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content"> 
        <div class="modal-header">
          <h4 class="modal-title">Admit Card Note</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form id="form" action="{{ url('admit_card_notes') }}" method="post" >
        @csrf 
        <div class="modal-body">
            @php
            $notes = Helper::getNote();
            @endphp
                          <input type="hidden" name="id" value="{{$notes['id'] ?? '' }}">

          <div class="input-group mb-3">
           <textarea type="text" class="form-control" id="note"name="note" placeholder="Your Note">{{$notes['note'] ?? '' }}</textarea>
        </div>
        </div>
       
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Submit</button>
        </div>
        </form>
      </div>
    </div>
</div>
@endsection
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script>

    function checkValidation(event){
        event.preventDefault()
        var classval = $('#class_type_id').val();
        var examVal = $('#exam_id').val();
        if(classval == ""){
           toastr.error('Please select a class ');
        }else if(examVal == ""){
            toastr.error('Please select a exam ');
        }else{
            $('#quickForm_find').trigger('submit');
        }
    }
</script>

<script>

$(document).ready(function(){
      $("#quickForm_find1").hide();
                $("#quickForm_find2").hide();
    
      $(".checkbox_admission").on("change", function(){
          var count =0;
     $(".checkbox_admission").each(function( index ) {
         
            if ($(this).prop("checked")){
                count++;
     
            }
          
          
});
if(count > 0){
               $("#quickForm_find1").show();
               $("#quickForm_find2").show();
          }
          else
          {
                $("#quickForm_find1").hide();
                $("#quickForm_find2").hide();
          }
});
     $("#quickForm_find2").on("click", function(){
        var baseurl = "{{ url('/') }}";
        
         var stream= $(this).data('stream') ;
         if(stream == '')
         {
             stream="null";
         }
         var classs = $(this).data('class');
         var exam= $(this).data('exam');
         
         var arr ="";
        $(".checkbox_admission").each(function( index ) {
            if ($(this).prop("checked")){
                arr = arr +","+$(this).val();
        }
});

var myString = arr.substring(1);



window.open(baseurl+'/without_subject_admit_card/'+exam+"/"+classs+"/"+myString, '_blank');
         	});


     $("#quickForm_find1").on("click", function(){
        var baseurl = "{{ url('/') }}";
        
         var stream= $(this).data('stream') ;
         if(stream == '')
         {
             stream="null";
         }
         var classs = $(this).data('class');
         var exam= $(this).data('exam');
         
         var arr ="";
        $(".checkbox_admission").each(function( index ) {
            if ($(this).prop("checked")){
                arr = arr +","+$(this).val();
        }
});

var myString = arr.substring(1);



window.open(baseurl+'/exam_admit_card/'+exam+"/"+classs+"/"+myString, '_blank');

     }); 
    
    
});



</script>
<script>
$(document).ready(function(){
        $("#view1").click(function(){
            if ($(this).is(':checked')) {
                $(".viewcheck").attr('checked', false);
                $(".viewcheck").attr('checked', true);
            }else{
                $(".viewcheck").attr('checked', false);
            }
        });
        $('#class_type_id').on('change', function(e){
            

                $(".div_subject_id").css("display","block");
                $('#subject_id').prop('required',true);

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
    
});
</script>
