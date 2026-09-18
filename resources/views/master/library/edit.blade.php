@extends('layout.app') 
@section('content')
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; Edit Book</h3>
							<div class="card-tools"> <a href="{{url('library_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a> </div>
						</div>
						<form id="quickForm" action="{{ url('library_edit') }}/{{($data->id)}}" method="post" enctype="multipart/form-data"> 
						    @csrf						
							<div class="row m-2">
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Book Name*</label>
										<input class="form-control  @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="Book Name" value="{{ $data->name ??  '' }}"> 
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
														
																
																<div class="col-md-12 text-center">
									<button type="submit" class="btn btn-primary">update</button>
								</div>
								</div>
					    </form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection