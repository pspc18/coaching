
@extends('layout.app') 
@section('content')

<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-book"></i> &nbsp; {{ __('library.Add Books Category') }}  </h3>
							<div class="card-tools"> 
                                    <a href="{{url('book_category_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }}  </a> 
							    <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
                        
                        <form action="{{ url('book_category_add') }}" method="post">
                            @csrf
                            <div class="row m-2">
                               <div class="col-md-3">
                                    <label class="text-danger">{{ __('library.Books Category') }}*</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="{{ __('library.Category Name') }}" value="{{ old('name') }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror                               
                                </div>
                              
                                
                                
                                
                              <div class="col-md-12 text-center mt-5">
                                    <button class="btn btn-primary" type="submit">{{ __('common.Submit') }}</button>
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