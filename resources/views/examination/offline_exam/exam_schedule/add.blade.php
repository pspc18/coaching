@extends('layout.app') 
@section('content')
@php
$classType = Helper::ClassType();
$setting = Helper::getSetting();

@endphp
<div class="content-wrapper">
<section class="content pt-3">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
               <div class="card-header bg-primary">
                  <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Examination Schedule') }} </h3>
                  <div class="card-tools cl-6"> 
                     <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a> 
                  </div>
               </div>
               <div class="card-body">
                  <form id="quickForm_find" action="{{ url('add/examination_schedule') }}" method="post" >
                     @csrf 
                     <div class="row">
                        <div class="col-md-2 col-4">
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
                        <div class="col-md-2 col-4">
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
                        <div class="col-md-1 col-2">
                           <label for="" class="text-white">Search</label>
                           <button type="submit" onClick="checkValidation(event)" class="btn btn-primary">{{ __('messages.Search') }}</button>
                        </div>
                     </div>
                  </form>
                  
                  <div class="row m-2">
                     <div class="col-12">
                    @if(count($data) > 0)   
                        <form id="form-submit" action={{url('SubmitSchedule')}} method="post">
                           @csrf
                            <div class="card p-0">
                               <div class="card-body p-0" style="display: block;">
                                  <div class="table-responsive card">
                                     <table class="table table-bordered table-striped dataTable dtr-inline card-outline card-orange">
                            <thead class="bg-primary">
                                <tr>
                                    <th>S No</th>
                                    <th>Subject Name</th>
                                    <th>Date</th>
                                    <th>From Time</th>
                                    <th>To Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                 $i = 1;
                                @endphp
                                 <input type="hidden" id="class_type_id" name="class_type_id" value="{{$search['class_type_id'] ?? ''}}">
                                <input type="hidden" id="exam_id" name="exam_id" value="{{$search['exam_id'] ?? ''}}">
                                
                                @foreach($data as $item)
                                  @php
                                      $examsc = DB::table('examination_schedules')
                                                        ->where(['exam_id' => $search['exam_id']])
                                                        ->where('class_type_id',$search['class_type_id'])
                                                        ->where('branch_id',Session::get('branch_id'))
                                                        ->where('session_id',Session::get('session_id'))
                                                        ->where('subject_id',$item->id)->whereNull('deleted_at')->first();
                                  @endphp
                                  @if(!empty($examsc->id))
                               <input type="hidden" class="form-control" name="examination_schedule_id[]" id="examination_schedule_id"  value="{{ $examsc->id ?? ''}}">
                               @else
                                <input type="hidden" class="form-control" name="examination_schedule_id[]" id="examination_schedule_id"  value="">
                               @endif
                                <tr>
                                    <td>{{ $i++ }}.</td>
                                    <td><input type="hidden" class="form-control" name="subject_id[]" id="subject_id" readonly value="{{  $item->id ?? ''}}">{{ $item->name  ?? ''}}</td>
                                    <td><input type="date" name="date[]" id="date" class="form-control" value="{{ $examsc->date ?? ''}}" ></td> 
                                    <td><input type="time" name="from_time[]" id="from_time" class="form-control" value="{{ $examsc->from_time ?? ''}}"></td>
                                    <td><input type="time" name="to_time[]" id="to_time" class="form-control" value="{{ $examsc->to_time ?? ''}}"></td>
                                </tr>
                                @endforeach
                            </tbody>
                            </table>
                                  </div>
                               </div>
                               
                                    <div class="card-footer mt-2" style="display: block;">
                                  <div class="col-md-12 mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                           <div class="form-group">
                                                <label>Exam Center</label>
                                                <input type="text" class="form-control" name="exam_center" id="exam_center" value="{{ $setting->address ?? ''}}" placeholder="Exam Center">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 mt-3 mb-3 text-center">
                                           <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                                        </div>
                                    </div>
                        </div>
                               </div>
                               
                        </form>
                    @else
                    <div class="row">
                        <div class="col-md-2" style="text-align: center;font-size: 22px;border: 1px solid;padding: 3px; background-color: #dd3545; color: white; border-radius: 8px;">
                             data not found 
                            </div>
                            
                            
                        </div>
                    @endif
                     </div>
                  </div>
                  
               </div>
            </div>
         </div>
      </div>
</section>
</div>


@endsection

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
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script>
$(document).ready(function(){
    
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

