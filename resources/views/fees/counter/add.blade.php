@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-bandcamp"></i> &nbsp;{{ __('fees.Counter Add') }}</h3>
                            <div class="card-tools">
                                <a href="{{url('feesCounterView')}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                                <a href="{{url('fee_dashboard')}}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>
                        </div>
                        <form id="quickForm" action="{{ url('feesCounterAdd') }}" method="post">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-3">
                                    <label>{{ __('common.Name') }} <span style="color: red;">*</span></label>
                                    <input class="form-control @error('name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" type="text" name="name" id="name" placeholder="{{ __('common.Name') }}" value="{{ old('name') }}" />
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label>{{ __('common.Password') }} <span style="color: red;">*</span></label>
                                    <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="{{ __('common.Password') }}" value="{{ old('password') }}" />
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row m-2">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">{{ __('common.submit') }}</button>
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
