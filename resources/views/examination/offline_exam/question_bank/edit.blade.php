@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();;
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('examination.Edit Question') }} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('view/question')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-eye"></i> View </a>-->
                    <a href="{{url('view/question')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('edit/question') }}/{{ $data->id ?? '' }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">

                       <!--<div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">Class*:</label>
            				<select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" value="{{old('class_type_id')}}">
                            <option value="" >Select</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $data['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
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
            		<div class="col-md-3">
            			<div class="form-group">
            				<label>Section</label>
            					<select class="form-control @error('section_id') is-invalid @enderror section_id" id="section" name="section_id" >
            				   <option value="" >Select</option>
                             @if(!empty($getSection)) 
                                  @foreach($getSection as $type)
                                     <option value="{{ $type->id }}" {{ ($type->id == $data['section_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('section_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		 </div>-->         
                        <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('examination.Subject') }}*:</label>
            				<select class="select2 form-control @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" value="{{old('subject_id')}}">
                             <option value="">{{ __('common.Select') }}</option> 
                             @if(!empty($getsubject)) 
                                  @foreach($getsubject as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $data['subject_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('subject_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		</div>          
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;"> {{ __('examination.Question Type') }}*</label>
                                <select class="select2 form-control @error('question_type_id') is-invalid @enderror" id="question_type_id" name="question_type_id">
        					    <option value="" >{{ __('common.Select') }} </option>
        					    <option value="1" {{ (1 == $data['question_type_id']) ? 'selected' : '' }}>Objective</option>
        					    <option value="2" {{ (2 == $data['question_type_id']) ? 'selected' : '' }}>Descriptive </option>
                               
                            </select>
                             @error('question_type_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                          
        						      				
        				</div>
        			</div>
        
            		<div class="col-md-12">
            			<div class="form-group">
            				<label  style="color:red;">{{ __('examination.Question') }}*</label>
            				<textarea class="form-control @error('name') is-invalid @enderror" id="name" name="name">{{ $data['name'] ?? '' }}</textarea>
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		 </div> 
            		   <div class="col-md-3 option_1" style="display:none;">
        				       
        			<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-1* </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox"name="correct_ans" value="0" {{ ( 0 == $data['correct_ans'] ??  old('correct_ans')) ? 'checked' : '' }}>
                            </span>
                            </div>
                          	<input type="text"  name="ans1" id="ans1" class="form-control @error('ans1') is-invalid @enderror" value="{{ $data['ans1'] }}" placeholder="{{ __('examination.Answer') }} 1">
                            </div>
                          	@error('ans1')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      
        		    </div>
        		
		        </div>
		         <div class="col-md-3 option_2" style="display:none;">
        			<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-2* </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox"name="correct_ans" value="1" {{ ( 1 == $data['correct_ans'] ??  old('correct_ans')) ? 'checked' : '' }}>
                            </span>
                            </div>
                          	<input type="text"  name="ans2" id="ans2" class="form-control @error('ans2') is-invalid @enderror" value="{{ $data['ans2'] }}" placeholder="{{ __('examination.Answer') }} 2">
                            </div>
                          	@error('ans2')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      
        		    </div>
        		
		        </div>
		        
		       <div class="col-md-3 option_3" style="display:none;">
        				<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-3* </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox"name="correct_ans" value="2" {{ ( 2 == $data['correct_ans'] ??  old('correct_ans')) ? 'checked' : '' }}>
                            </span>
                            </div>
                          	<input type="text"  name="ans3" id="ans3" class="form-control @error('ans3') is-invalid @enderror" value="{{ $data['ans3'] }}" placeholder="{{ __('examination.Answer') }} 3">
                            </div>
                          	@error('ans3')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      
        		    </div>
        		
		        </div>
		          <div class="col-md-3 option_4" style="display:none;">
        		      	<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-4* </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox"name="correct_ans" value="3" {{ ( 3 == $data['correct_ans'] ??  old('correct_ans')) ? 'checked' : '' }}>
                            </span>
                            </div>
                            <input type="text"  name="ans4" id="ans4" class="form-control @error('ans4') is-invalid @enderror" value="{{ $data['ans4'] }}" placeholder="{{ __('examination.Answer') }} 4">
                            </div>
                          	@error('ans4')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      
        		    </div>
        </div>
        </div>
                    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary ">{{ __('common.Update') }}</button>
                        </div>
                    </div>
                
            
            </form>
</div>
</div>
</div>
</div>
</section>
</div>

<script>
$('#question_type_id').on('change', function(e){
  
	var question_type_id = $(this).val();
    if(question_type_id == 1){
        $(".option_1").show();
        $(".option_2").show();
        $(".option_3").show();
        $(".option_4").show();
        $("#ans4").attr('required','true');
        $("#ans3").attr('required','true');
        $("#ans2").attr('required','true');
        $("#ans1").attr('required','true');
        
    }
    if(question_type_id == 2){
        $(".option_1").hide();
        $(".option_2").hide();
        $(".option_3").hide();
        $(".option_4").hide();
    }    
    if(question_type_id == ''){
        $(".option_1").hide();
        $(".option_2").hide();
        $(".option_3").hide();
        $(".option_4").hide();
    } 
    
}); 

$( window ).on( "load", function() {

    if($('#question_type_id').val() == 1){
        $(".option_1").show();
        $(".option_2").show();
        $(".option_3").show();
        $(".option_4").show();
        $("#ans4").attr('required','true');
        $("#ans3").attr('required','true');
        $("#ans2").attr('required','true');
        $("#ans1").attr('required','true');
        
    } 

});
//select all checkboxes
    $("#{{ $data->id ?? ''  }}").change(function () {  
        $(".checkbox").prop('checked', $(this).prop("checked")); 
    });

//".checkbox" change 
    $('.checkbox').change(function () {
        if (false == $(this).prop("checked")) { 
            $("#{{ $data->id ?? ''  }}").prop('checked', false);
        }
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $("#{{ $data->id ?? ''  }}").prop('checked', true);
        }
    });
</script>
<script>
    $("input:checkbox").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
});
</script>

@endsection