@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Edit Complaint')
@section('page_title', 'EDIT COMPLAINT')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2 border-0">
     <div class="leave-form-box p-3">
					<form id="quickForm" action="{{ url('complaintEditStudent') }}/{{($data->id)}}" method="post" enctype="multipart/form-data"> 

						    @csrf						
							
							
									<div class="form-group">
										<label style="color: red;">{{ __('common.Subject') }}*</label>
										<input class="leave-input @error('subject') is-invalid @enderror" type="text" id="subject" name="subject" placeholder="{{ __('common.Subject') }}" value="{{ $data->subject ??  '' }}"> 
                                        @error('subject')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
							
								
									<div class="form-group">
										<label style="color: red;">{{ __('dashboard.Description') }}*</label>
										<textarea class=" leave-input @error('description') is-invalid @enderror" type="text" id="description" name="description" placeholder="{{ __('dashboard.Description') }}" value="">{{ $data->description ??  '' }}</textarea>  
                                        @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
							
							
							<button type="submit" class="btn-leave-send">SUBMIT</button>
                  
					        
					    </form>
				</div>
			</div>
		
	</section>
@endsection