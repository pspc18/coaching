@extends('layout.app')
@section('content')


<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('hostel.Edit Head Expencese') }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{url('hostelExpensesView')}}" class="btn btn-primary  btn-sm"><i
                                        class="fa fa-eye"></i>{{ __('common.View') }} </a>
                                <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i
                                        class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>

                            </div>
                        </div>

                        <form id="quickForm" action="{{ url('hostelExpensesHeadeEdit') }}/{{$data['id']}}" method="post">
                            @csrf
                            <div class="row mb-2 m-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-danger">{{ __('hostel.Head Name') }}*</label>

                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$data['name'] ?? ''}}">
                                        	@error('name')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
										@enderror 
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-danger">{{ __('hostel.Head Description') }}*</label>
                                        <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" id="description" value="{{$data['description'] ?? ''}}">
	                                    @error('description')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
										@enderror
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary ">{{ __('common.Update')
                                    }}</button><br><br>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
</div>
@endsection