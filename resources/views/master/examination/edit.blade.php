@php
   $getSection = Helper::getSection();
  
   $classType = Helper::classType();
 
    
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
							<h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; Edit Examination</h3>
							<div class="card-tools"> <a href="{{url('examination_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> view</a> </div>
						</div>
						<form id="quickForm" action="{{ url('examination_edit') }}" method="post" enctype="multipart/form-data"> 
						    @csrf						
						 <div class="row m-2"> 
                    <div class="col-md-4">
            			<div class="form-group">
								<label style="color:red;">Class*</label>
            				<select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" value="{{old('class_type_id')}}">
                            <option value="" >Select</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
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
            		
            			<div class="col-md-4">
            			<div class="form-group">
            				<label  style="color:red;">Section*</label>
            					<select class="form-control @error('section_id') is-invalid @enderror section_id" id="section" name="section_id" value="{{old('section')}}">
            				   <option value="" >Select</option>
                             @if(!empty($getSection)) 
                                  @foreach($getSection as $type)
                                     <option value="{{ $type->id }}">{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('section_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		 </div>
            		
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Max Marks	*</label>
										<input class="form-control  @error('max_marks') is-invalid @enderror" type="text" id="max_marks" name="max_marks" placeholder="Max Marks" value="{{old('max_marks')}}"> 
                                        @error('max_marks')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Student Name*</label>
										<input class="form-control  @error('student_name') is-invalid @enderror" type="text" id="student_name" name="student_name" placeholder="Student Name" value="{{old('student_name')}}"> 
                                        @error('student_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Father Name	*</label>
										<input class="form-control  @error('father_name	') is-invalid @enderror" type="text" id="father_name" name="father_name" placeholder="Father Name" value="{{old('father_name')}}"> 
                                        @error('father_name	')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Roll No*</label>
										<input class="form-control  @error('roll_no') is-invalid @enderror" type="text" id="roll_no" name="roll_no" placeholder="Roll No" value="{{old('roll_no')}}"> 
                                        @error('roll_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Subject Name*</label>
										<input class="form-control  @error('subject_name') is-invalid @enderror" type="text" id="subject_name" name="subject_name" placeholder="Subject Name" value="{{old('subject_name')}}"> 
                                        @error('subject_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Exam Type*</label>
										<input class="form-control  @error('exam_type') is-invalid @enderror" type="text" id="exam_type" name="exam_type" placeholder="Exam Type " value="{{old('exam_type')}}"> 
                                        @error('exam_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Min Marks*</label>
										<input class="form-control  @error('min_marks') is-invalid @enderror" type="text" id="min_marks" name="min_marks" placeholder="Min Marks" value="{{old('min_marks')}}"> 
                                        @error('min_marks')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								
								</div>
							
								    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary ">Update</button>
                        </div>
                    </div>
					        
					    </form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection