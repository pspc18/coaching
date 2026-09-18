@extends('layout.app') 
@section('content')
<div class="content-wrapper">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card card-outline card-orange">
                    <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fa fa-certificate"></i> &nbsp; Certificate Management</h3>
                    </div>               
                </div>
            </div> 
        </div>
        
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @if($character != "")
                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-header text-center text-white p-2">
                                <h5 class="mb-0">Character Certificate</h5>
                            </div>
                            <div class="card-footer text-center p-4">
                                <a href="{{url('character_print')}}/{{$character->id ?? ''}}" target="blank"><button class="btn btn-primary btn-xl">Print</button></a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($study != "")
                    <div class="col-md-3">
                        <div class="card bg-secondary">
                            <div class="card-header text-center text-white p-2">
                                <h5 class="mb-0">Study Certificate</h5>
                            </div>
                            <div class="card-footer text-center p-4">
                                 <a href="{{url('study_print')}}/{{$study->id ?? ''}}" target="blank"><button class="btn btn-primary btn-xl">Print</button></a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($fees != "")
                    <div class="col-md-3">
                        <div class="card bg-danger">
                            <div class="card-header text-center text-white p-2">
                                <h5 class="mb-0">Fees Certificate</h5>
                            </div>
                            <div class="card-footer text-center p-4">
                                 <a href="{{url('fees_print')}}/{{$fees->id ?? ''}}" target="blank"><button class="btn btn-primary btn-xl">Print</button></a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($tc_certificate != "")
                    <div class="col-md-3">
                        <div class="card bg-info">
                            <div class="card-header text-center text-white p-2">
                                <h5 class="mb-0">Tc Certificate</h5>
                            </div>
                            <div class="card-footer text-center p-4">
                                 <a href="{{url('tc_print')}}/{{$tc_certificate->id ?? ''}}" target="blank"><button class="btn btn-primary btn-xl">Print</button></a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($sports != "")
                    <div class="col-md-3">
                        <div class="card bg-secondary">
                            <div class="card-header text-center text-white p-2">
                                <h5 class="mb-0">Sport Certificate</h5>
                            </div>
                            <div class="card-footer text-center p-4">
                                 <a href="{{url('sport_print')}}/{{$sports->id ?? ''}}" target="blank"><button class="btn btn-primary btn-xl">Print</button></a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>    
            </div>
        </section>
    </div>
</div>    
@endsection