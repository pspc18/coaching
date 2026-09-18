@extends('layout.app')
@section('content')
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.Edit Enquiry') }} </h3>
                            <div class="card-tools"> <a href="{{url('gate_pass_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a> </div>
                        </div>
                        <form id="quickForm" action="{{ url('gate_pass_edit') }}/{{($data->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row p-3">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.Student Name') }} *</label>

                                        <input type="text" name="student_name" id="student_name" onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control @error('student_name') is-invalid @enderror" placeholder="{{ __('Student Name') }}" value="{{ $data['student_name'] ?? '' }}">

                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('common.Fathers Name') }} *</label>
                                        <input type="text" name="father_name" id="father_name" onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control @error('father_name') is-invalid @enderror" placeholder="{{ __('common.Fathers Name') }}" value="{{ $data['father_name'] ?? '' }}">
                                        @error('father_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('common.Fathers Mobile') }} *</label>
                                        <input type="text" name="father_mobile" id="father_mobile" class="form-control @error('father_mobile') is-invalid @enderror" placeholder="{{ __('common.Fathers Mobile') }}" value="{{ $data['father_mobile'] ?? '' }}">
                                        @error('father_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('common.Date') }} *</label>
                                        <input type="date" name="iessu_date" id="iessu_date" class="form-control @error('iessu_date') is-invalid @enderror" value="{{ $data['iessu_date'] ?? '' }}">
                                        @error('iessu_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>




                            </div>
                            <h5 class="p-3">{{ __('master.Reciver Detail') }}</h5>

                            <div class="row m-2">
                                <div class="col-md-3 pl-0">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.Reciver Name') }}*</label>

                                        <input type="text" name="reciver_name" id="reciver_name" onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control @error('reciver_name') is-invalid @enderror" placeholder="{{ __('master.Reciver Name') }}" value="{{ $data['reciver_name'] ?? '' }}">

                                        @error('reciver_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.Reciver Mobile') }} *</label>
                                        <input type="text" name="reciver_mobile" id="reciver_mobile" class="form-control @error('reciver_mobile') is-invalid @enderror" placeholder="{{ __('master.Reciver Mobile') }}" value="{{ $data['reciver_mobile'] ?? '' }}">
                                        @error('reciver_mobile')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3 pl-0">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.Relation') }}*</label>

                                        <input type="text" name="relation" id="relation" onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control @error('relation') is-invalid @enderror" placeholder="{{ __('master.Relation') }}" value="{{ $data['relation'] ?? '' }}">

                                        @error('relation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row m-2">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">{{ __('common.Submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
</div>
@endsection