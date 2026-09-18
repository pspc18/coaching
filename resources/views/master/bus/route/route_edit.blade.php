@php

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
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; {{ __('bus.Edit Route') }}</h3>
							<div class="card-tools">
								<a href="{{url('busRouteAdd')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('messages.View') }} </a>
								<a href="{{url('busDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('Back') }} </a>

							</div>
						</div>

						<form id="form-submit-edit" action="{{ url('busRouteEdit') }}/{{($data->id)}}" method="post">
							@csrf
							<div class="row mb-2 m-2">
								<div class="col-md-6">
									<div class="form-group">
										<label style="color:red;">{{ __('messages.Name') }}*</label>
										<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="Name" value="{{ $data->name ??  '' }}">
									
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label>{{ __('messages.Description') }}</label>
										<textarea class="form-control" type="text" name="description" id="description" placeholder="{{ __('messages.Description') }}">{{ $data->description ??  '' }}</textarea>
									</div>
								</div>

							</div>
							<div class="col-md-12 text-center">
								<button type="submit" class="btn btn-primary btn-submit">{{ __('messages.Update') }}</button><br><br>
							</div>
						</form>
					</div>
				</div>
			</div>
	</section>
</div>>

@endsection