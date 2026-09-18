@php
   $classType = Helper::ClassType();
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary flex_items_toggel">
                    <h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp; {{ __('Marksheet Generate') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> <span class="Display_none_mobile">{{ __('messages.Back') }} </span> </a>
                    </div>
                   
                    </div>        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="marksheet_text">Marksheet</p>
                                </div>
                                <div class="col-md-6">
                                    <form id="search_class_type" action="{{url('bulk_marksheet')}}" method="post" >
                                                @csrf
                                         			<div class="form-group">
                                    		
                                    				<select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id_submit" name="class_type_id" value="{{old('class_type_id')}}">
                                                    <option value="" >{{ __('messages.Select') }}</option>
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
                                    		       </form>
                                </div>
                                
                                <hr>
                                
                                @if( !empty($exam))
                                <div class="col-md-12">
                                    <form action="{{url('bulk_marksheet_generate')}}" method="post">
                                        @csrf
                                        <input type="hidden"value="{{$search['class_type_id'] ?? ''}}" name="class_type_id"/>
                                        <div class="col-md-12 mb-3">
                                            <span class="text-danger">Note: System auto convert marks to grade.</span>
                                            <br>
                                            <span class="notes_green mt-3">Select Subjects for grade - </span>
                                        </div>
                                
                                        <div class="row">
                                            @foreach($subject as $item)
                                            <div class="col-md-4 col-12 mt-2">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                    <input type="checkbox" value="{{$item->id ?? ''}}" name="subjects[]" class="checkbox"/> {{$item->name ?? ''}} 
                                                    </div>
                                                
                                                    <div class="col-md-6 col-6">
                                                        <input class="w-50 checkbox_{{$item->id ?? ''}} box1"type="text"  disabled="true" name="subject_array[{{$item->id ?? ''}}]"  required  value="{{$item->sort_by ?? ''}}"/>
                                                    </div>
                                                </div>
                                            </div>
                                                
                                            @endforeach
                                        </div>
                                
                                        <div class="col-md-12 mt-3">
                                            <span class="text-success">Select Exam - </span>
                                        </div>
                                
                                @if(!empty($exam))
                                <div class="row">
                                @foreach($exam as $item)
                                <div class="col-md-4 mt-2">
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <input type="checkbox" value="{{$item->exam_id ?? ''}}" name="exam[]" class="checkbox1"/> {{$item->exam_name ?? ''}} 
                                        </div>
                                    
                                        <div class="col-md-6 col-6">
                                            <input class="w-50 checkbox1_{{$item->exam_id ?? ''}}  box2"  type="text" disabled="true"  name="exam_array[{{$item->exam_id ?? ''}}]"  required/>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                </div>
                                @endif
                               @if (isset($exam)) 
                               <hr>
                                @if (!empty($student_list)) 
                               <div class="row">
                                <div class="col-md-6 text-center mt-3 d-flex">
                                    	<div class="form-group">
                              <b> Select Student :  &nbsp;&nbsp;</b>
                                <select class="select2" name="single_student[]" multiple>
                                      
                                    
                                    @foreach($student_list as $item)
                                    
                                    <option value="{{$item->id ?? ''}}">{{$item->first_name ?? ''}}</option>
                                    @endforeach
                                 
                                </select>
                                  </div>
                                   </div>
                                   <div class="col-md-3 mt-3">
                                       <div class="form-group">
                                            Result Date
                                    <input type="date"  class="form-control"name="result_date" />  
                                    </div>
                                  </div>
                                <!--<div class="col-md-3 text-right mt-3">
                                   <b>With Grade System :    </b> <input type="checkbox" value="1" class=" mt-1"name="make_grade"/>  
                                  </div>-->
                                <div class="col-md-2 text-left mt-3">
                                  <button class="btn btn-primary" id="gen_id_1" style="visibility:hidden">Generate Marksheet</button>
                                  <button type="button" id="gen_id"class="btn btn-primary">Generate Marksheet</button>
                                  
                                </div>
                                </div>
                                  @endif
                             @endif
                                    </form>
                                </div>
                                @endif
                                
                            </div>
                        </div> 
                </div>
        </div>
</div>
</section>
</div>
<script type="text/javascript">

var count =0;
$("#class_type_id_submit").change(function(){
$('#search_class_type').submit();
}); 
$(".checkbox").click(function(){
       var value = $(this).val();
  if ($(this).is(":checked")) {

     $('.checkbox_'+value).prop( "disabled", false );
    }
    else{
          $('.checkbox_'+value).prop( "disabled", true );
    }
}); 
$(".checkbox1").click(function(){
       var value = $(this).val();

  if ($(this).is(":checked")) {

     $('.checkbox1_'+value).prop( "disabled", false );
    }
    else{
          $('.checkbox1_'+value).prop( "disabled", true );
    }
}); 


$("#gen_id").click(function(){
    count =0;
    count1 =0;
    $( ".box1" ).each(function( index ) {
 if($(this).val() != '')
 {
     count++;
 }
});
  
    $( ".box2" ).each(function( index ) {
 if($(this).val() != '')
 {
     count1++;
 }
});
  
 if(count > 0 && count1 > 0)
 {
     $('#gen_id_1').trigger('click');
 }
 else
 {
     toastr.error("Please Select atleast one subject and one exam")
 }
});   
    
    

</script>
@endsection