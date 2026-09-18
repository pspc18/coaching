@php
  $classType = Helper::classType();
  $getSection = Helper::getSection();
  $getsubject = Helper::getSubject();
@endphp

@extends('layout.app') 
@section('content')
   
    
<div class="content-wrapper">
   
    <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">   
        <div class="card card-outline card-orange">
               <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp; Add Homework</h3>
            <input type="hidden" id="role_id" value="{{ Session::get('role_id') ?? '' }}"> 
            <div class="card-tools">
                         <a href="{{url('homework/index')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> View </a>
<!--        <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a>
-->                
   
           
            </div>
            
            </div>   
             <div class="card-body">
                 <form id="quickForm" action="{{ url('homework/add') }}" method="post" enctype="multipart/form-data">
                      @csrf
                <div class="row"> 
                
                        	<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Homework Title*</label>
										<input class="form-control  @error('title') is-invalid @enderror" type="text" id="title" name="title" placeholder="Homework Title" value="{{ old('title') ?? '' }}"> 
                                        @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
                
                    <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">Class:*</label>
            				<select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id">
                            <option value="" >Select</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == Session::get('class_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
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
            				<label  style="color:red;">Section*</label>
            					<select class="form-control @error('section_id') is-invalid @enderror section_id" id="section" name="section_id" >
            				   <option value="" >Select</option>
                             <!--@if(!empty($getSection)) 
                                  @foreach($getSection as $type)
                                     <option value="{{ $type->id }}" {{ ($type->id == Session::get('section_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif-->
                            </select>
                             @error('section_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		 </div> 
                    <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">Subject:*</label>
            				<select class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject">
                             @if(!empty($getsubject)) 
                                  @foreach($getsubject as $type)
                                     <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
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
        				<label style="color:red;">Homework Date:*</label>
        				
        					<input type="date" class="form-control @error('homework_date') is-invalid @enderror" id="homework_date" name="homework_date"value="{{date('Y-m-d')}}">
        				   
                         @error('homework_date')
        					<span class="invalid-feedback" role="alert">
        						<strong>{{ $message }}</strong>
        					</span>
        				 @enderror
        		        </div>
        		    </div>
            		<div class="col-md-3">
        			    <div class="form-group">
        				<label style="color:red;">Submission Date:*</label>
        					<input type="date" class="form-control @error('submission_date') is-invalid @enderror" id="submission_date" name="submission_date" value="{{ old('submission_date') ?? '' }}">
                         @error('submission_date')
        					<span class="invalid-feedback" role="alert">
        						<strong>{{ $message }}</strong>
        					</span>
        				 @enderror
        		        </div>
        		    </div>    
                    <div class="col-md-3">
            			<label>Content File</label>
                        <div class="input file form-control">
                            <input type="file" name="content_file" id="content_file" value="{{ old('content_file') ?? '' }}">
                        </div>                    
                	</div>          		    
        		   <div class="col-md-12">
        			   <div class="form-group">
        				<label style="color:red;">Description:*</label>
        					<textarea type="text" class="form-control @error('description') is-invalid @enderror" id="compose-textarea" name="description" placeholder="Please submit before last date.">{{ old('description') ?? '' }}</textarea>
                         @error('description')
        					<span class="invalid-feedback" role="alert">
        						<strong>{{ $message }}</strong>
        					</span>
        				 @enderror
        		        </div>
        		    </div>
       
              </div>
              <div class="row m-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary ">Submit</button>
                    </div>
                </div>
                </form>
                </div>                 
            </div> 
            </div> 
            </div> 
            </div> 
            </section>
        </div>
    
    
        
    
<script>
/*$( document ).ready(function() {
    var role_id = $('#role_id').val();
   
   if( role_id == 2 ) { 
        $("#class_type_id").attr('disabled', 'disabled');
        $("#section").attr('disabled', 'disabled');
   }else{
   }     
});*/
</script>
@endsection                