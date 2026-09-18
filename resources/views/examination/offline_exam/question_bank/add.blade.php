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
                    <h3 class="card-title"><i class="fa fa-check-square-o"></i> &nbsp; {{ __('examination.Add Questions') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('view/question')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                    <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('add/question') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">

                       <!--<div class="col-md-4">
            			<div class="form-group">
            				<label style="color:red;">Class*:</label>
            				<select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" value="{{old('class_type_id')}}">
                            <option value="" >Select</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == old('class_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
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
            		 -->        
                        <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('examination.Subject') }}</label>
            				<select class="form-control select2 @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" value="{{old('subject_id')}}">
                             @if(!empty($getsubject)) 
                                  @foreach($getsubject as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == old('subject_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
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
                                <label style="color:red;"> {{ __('examination.Question Type') }}</label>
                                <select class="form-control select2 @error('question_type_id') is-invalid @enderror" id="question_type_id" name="question_type_id">
        					    <option value="1" >Objective</option>
        					    <option value="2" >Descriptive </option>
                               
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
            				<label  style="color:red;">{{ __('examination.Question') }}</label>
            				<textarea class="form-control @error('name') is-invalid @enderror" id="name" name="name">{{ old('name') ?? '' }}</textarea>
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		 </div> 
            		   <div class="col-md-3 option_1">
        				       
        			<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-1 </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox" name="correct_ans" value="0" @if(old('correct_ans')=="0") checked @endif>
                            </span>
                            </div>
                          	<input type="text"  name="ans1" id="ans1" class="form-control @error('ans1') is-invalid @enderror checkAns" value="{{ old('ans1') }}" placeholder="{{ __('examination.Answer') }} 1">
                            </div>
                          	@error('ans1')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      
        		    </div>
        		
		        </div>
		         <div class="col-md-3 option_2">
        			<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-2 </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox"name="correct_ans" value="1" @if(old('correct_ans')=="1") checked @endif>
                            </span>
                            </div>
                          	<input type="text" name="ans2" id="ans2" class="form-control @error('ans2') is-invalid @enderror checkAns" value="{{ old('ans2') }}" placeholder="{{ __('examination.Answer') }} 2">
                            </div>
                          	@error('ans2')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      
        		    </div>
        		
		        </div>
		        
		       <div class="col-md-3 option_3">
        				<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-3 </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox"name="correct_ans" value="2" @if(old('correct_ans')=="2") checked @endif>
                            </span>
                            </div>
                          	<input type="text"  name="ans3" id="ans3" class="form-control @error('ans3') is-invalid @enderror checkAns" value="{{ old('ans3') }}" placeholder="{{ __('examination.Answer') }} 3">
                            </div>
                          	@error('ans3')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      
        		    </div>
        		
		        </div>
		          <div class="col-md-3 option_4">
        		      	<div class="form-group ">
        				<label style="color:red;">  {{ __('examination.Option') }}-4 </label>
        				 <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <input type="checkbox"name="correct_ans" value="3" @if(old('correct_ans')=="3") checked @endif>
                            </span>
                            </div>
                            <input type="text"  name="ans4" id="ans4" class="form-control @error('ans4') is-invalid @enderror checkAns" value="{{ old('ans4') }}" placeholder="{{ __('examination.Answer') }} 4">
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
                            <button type="submit" class="btn btn-primary ">{{ __('common.Submit') }}</button>
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
    // $(document).ready(function(){
    //   $('.checkAns').change(function(){
    //       var value = $(this).val();
    //       var length = $('.checkAns').length;
    //       var index = $('.checkAns').index(this);
    //       var array = [];
           
    //       $('.checkAns').each(function(){
    //           if($(this).val() != ""){
    //               array.push($(this).val());
    //           }else{
    //               array.push(null);
    //           }
    //       });
          
          
          
          
          
          
          
          
           
          
    //     //   if (array.indexOf($(this).val()) > -1) {
    //     //         alert("same value");   
    //     //     }else{
    //     //     }
    //   }); 
    // });
</script>

<script>
$(document).ready(function() { 
    $("#ans4").attr('required','true');
    $("#ans3").attr('required','true');
    $("#ans2").attr('required','true');
    $("#ans1").attr('required','true');
});
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
    $("#ans4").removeAttr('required');
    $("#ans3").removeAttr('required');
    $("#ans2").removeAttr('required');
    $("#ans1").removeAttr('required');        
    }    
    if(question_type_id == ''){
        $(".option_1").hide();
        $(".option_2").hide();
        $(".option_3").hide();
        $(".option_4").hide();
        
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
  var $box = $(this);
  if ($box.is(":checked")) {
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
});
</script>

@endsection