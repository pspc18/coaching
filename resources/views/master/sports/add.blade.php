@php
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
							<h3 class="card-title"><i class="fa fa-support"></i> &nbsp;Add Sports</h3>
							<div class="card-tools"> <a href="{{url('sports_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> view</a> </div>
						</div>
						<div class="card-body">

						<form id="quickForm" action="{{ url('sports') }}" method="post" enctype="multipart/form-data"> 
						    @csrf						
						
                          		 
            		<div class="col-md-6">
        			    <div class="form-group">
										<label style="color: red;">For Class*</label>
										<select class="form-control  @error('for_class') is-invalid @enderror" id="for_class" name="for_class">
										    <option value="">Select</option>
                                             @if(!empty($classType)) 
                                                  @foreach($classType as $type)
                                                     <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                                  @endforeach
                                              @endif
										</select>
                                        @error('for_class')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror										
								    </div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Photo*</label>
										<input class="form-control  @error('photo') is-invalid @enderror" type="file" id="photo" name="photo" placeholder="Sports Photo"> 
                                        @error('photo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
							
								<div class="col-md-12 text-center">
									<button type="submit" class="btn btn-primary">Submit</button>
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
@endsection