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
                  <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Subject Wise Report') }} </h3>
                  <div class="card-tools d-flex align-item-center"> 
                     <a href="{{ url('examination_dashboard') }}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a> 
                  </div>
               </div>
               <div class="card-body">
                     <div class="row">
                        <div class='col-md-10'>
                        <form id="quickForm_find" action="{{ url('green_sheet_report') }}" method="post" target="_blank">
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
                           
                            
                            <div class="col-md-2 col-6">
                               <label for="" class="text-white">Search</label>
                               <button type="submit" onClick="checkValidation(event)" class="btn btn-primary">{{ __('View Report') }}</button>
                            </div>
                            </div>
                        </form>
                        </div>
                     </div>
                
            </div>
</section>
     </div>
@endsection
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>

<script>
$(document).ready(function(){
    
        $('#class_type_id').on('change', function(e){
            


                var baseurl = "{{ url('/') }}";
            	var class_type_id = $(this).val();
            	  
                $.ajax({
                    headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            	    url: baseurl + '/examData/' + class_type_id,
            	    success: function(data){
    	         	    $("#exam_id").html(data);
    	         	    subjectGetData();
            	    }
            	});
        });
        function   subjectGetData(){
            

                var baseurl = "{{ url('/') }}";
            	var class_type_id = $(this).val();
            	  
                $.ajax({
                    headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            	    url: baseurl + '/subjectGetData/' + class_type_id,
            	    success: function(data){
    	         	    $("#subject_id").html(data);
            	    }
            	});
        };
});
    </script>
