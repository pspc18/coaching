@extends('layout.app') 
@section('content')
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('invantory.Edit Invantory') }}</h3>
							<div class="card-tools"> <a href="{{url('invantory_item_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a> </div>
						</div>
						<form id="form-submit-edit" action="{{ url('invantory_item_edit') }}/{{($data->id)}}" method="post" enctype="multipart/form-data"> 
						    @csrf						
								<div class="row col-12">
							    
								<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">{{ __('common.Name') }}*</label>
										<input class="form-control  @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="{{ __('common.Name') }}" value="{{ $data->name ??  '' }}"> 
                                      								    
								    </div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Hsn Code</label>
										<input class="form-control  @error('hsn_code') is-invalid @enderror" type="text" id="hsn_code" name="hsn_code" placeholder="Hsn Code" value="{{ $data->hsn_code ??  '' }}"> 
                                       							    
								    </div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Unit*</label>
										<input class="form-control  @error('unit') is-invalid @enderror" type="text" id="unit" name="unit" placeholder="Unit" value="{{ $data->unit ??  '' }}"> 
                                      							    
								    </div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color: red;">Mrp*</label>
										<input class="form-control  @error('mrp') is-invalid @enderror" type="text" id="mrp" name="mrp" placeholder="Mrp" value="{{ $data->mrp ??  '' }}"> 
                                      							    
								    </div>
								</div>
							
								</div>
							
								
							
							 <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary btn-submit">{{ __('common.Update') }}</button>
                        </div>
                    </div>
					        
					    </form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection