

@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">

        <div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-support"></i> &nbsp; {{ __('master.Add Shop Detail') }}</h3>
							<div class="card-tools">
                            <a href="{{url('books_uniform_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> {{ __('View') }} </a>
                            <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                            </div>
						</div>
						<div class="row">
                          <div class="col-12">
                           <form id="form-submit" action="{{ url('books_uniform_add') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row m-2">
                                         <div class="col-md-2">
                                            <div class="form-group">
                                               <label class="text-danger">{{ __('master.Category') }}*</label>
                                               <select class="form-control @error('category') is-invalid @enderror select2" id="category" name="category">
                                                   <option value="">{{ __('common.Select') }}</option>
                                                   <option value="Books" {{("Books") == old("category") ? 'selected' : ''}} >{{ __('master.Books') }}</option>
                                                   <option value="Uniform" {{("Uniform") == old("Uniform") ? 'selected' : ''}}>{{ __('master.Uniform') }}</option>
                                               </select>
                                           
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                               <label class="text-danger">{{ __('master.Shop Name') }}*</label>
                                               <input type="text" id="shop_name" value="{{old('shop_name')}}" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror" placeholder="{{ __('master.Shop Name') }}">
                                            
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                               <label class="text-danger">{{ __('common.Address') }}*</label>
                                               <textarea type="text" id="address" value="{{old('address')}}" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="{{ __('common.Address') }}"></textarea>
                                           
                                            </div>
                                        </div>
                                        
                                         <div class="col-md-2">
                                            <div class="form-group">
                                               <label>{{ __('master.Shopkeeper No') }}</label>
                                               <input maxlength="10" onkeypress="javascript:return isNumber(event)" type="text" value="{{old('shop_keeper_no')}}"  id="shop_keeper_no" name="shop_keeper_no" class="form-control" placeholder="{{ __('master.Shopkeeper No') }}">
                                            </div>
                                        </div>
                                        
                                         <div class="col-md-2">
                                            <div class="form-group">
                                               <label>{{ __('master.Live Location') }}</label>
                                               <input type="text" value="{{old('live_location')}}"  id="live_location" name="live_location" class="form-control" placeholder="{{ __('master.Live Location') }}">
                                            </div>
                                        </div>
                                        
                                   
                                        </div>
                                        <div class="row m-2">
                                     
                                         <div class="col-md-12 mt-3 mb-3 text-center">
                                            <button type="submit" class="btn btn-primary btn-submit">{{ __('common.Submit') }}</button>
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