@extends('layout.app') 
@section('content')
@php
$getPermission = Helper::getPermission();
@endphp
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-bar-chart-o"></i> &nbsp;{{ __('Refund Fees') }}</h3>
                            <div class="card-tools">
                                <a href="{{url('fee_dashboard')}}" class="btn btn-primary btn-sm" title="Back"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                            </div>
                        </div>

                        

                        <div class="row m-2">
                            <div class="col-md-12">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



@endsection
