@extends('layout.app') 
@section('content')
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-list-alt"></i> &nbsp;{{ __('dashboard.Add Complaint') }} </h3>
							<div class="card-tools"> <a href="{{url('complaint_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a> </div>
						</div>
						<form id="quickForm" action="{{ url('complaint_add') }}" method="post" enctype="multipart/form-data"> 
						    @csrf						
							<div class="row col-12">
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">{{ __('common.Subject') }}*</label>
										<input class="form-control  @error('subject') is-invalid @enderror" type="text" id="subject" name="subject" placeholder="{{ __('common.Subject') }}"> 
                                        @error('subject')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-8">
									<div class="form-group">
										<label style="color: red;">{{ __('dashboard.Description') }}*</label>
										<textarea class="form-control  @error('description') is-invalid @enderror" type="text" id="description" name="description" placeholder="{{ __('dashboard.Description') }}"></textarea> 
                                        @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								</div>
							
							 <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary ">{{ __('common.submit') }}</button>
                        </div>
                    </div>

					        
					    </form>
					    </div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection