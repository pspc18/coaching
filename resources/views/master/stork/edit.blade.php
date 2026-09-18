@extends('layout.app') 
@section('content')
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; Edit Stork</h3>
							<div class="card-tools"> <a href="{{url('stork')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a> </div>
						</div>
						<form id="quickForm" action="{{ url('stork_edit') }}/{{($data->id)}}" method="post" >
						    @csrf						
					<div class="row m-2">
        
                        <div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Books *</label>
										<input class="form-control  @error('books') is-invalid @enderror" type="text" id="books" name="books" placeholder="Books " value="{{ $data->books ??  '' }}">
                                        @error('books')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Uniform *</label>
										<input class="form-control  @error('uniform') is-invalid @enderror" type="text" id="uniform" name="uniform" placeholder="Uniform " value="{{ $data->uniform ??  '' }}">
                                        @error('uniform')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Diary *</label>
										<input class="form-control  @error('diary') is-invalid @enderror" type="text" id="diary" name="diary" placeholder="Diary " value="{{ $data->diary ??  '' }}">
                                        @error('diary')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Furniture *</label>
										<input class="form-control  @error('furniture') is-invalid @enderror" type="text" id="furniture" name="furniture" placeholder="Furniture " value="{{ $data->furniture ??  '' }}">
                                        @error('furniture')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Duster *</label>
										<input class="form-control  @error('duster') is-invalid @enderror" type="text" id="duster" name="duster" placeholder="Duster " value="{{ $data->duster ??  '' }}"> 
                                        @error('duster')
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
		</div>
	</section>
</div>
@endsection