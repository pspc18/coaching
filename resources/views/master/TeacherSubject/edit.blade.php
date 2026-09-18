@php
$getSubject = Helper::getSubject();
$classType = Helper::classType();
$getAllTeachers = Helper::getAllTeachers();
$getTimePeriod = Helper::getTimePeriod();
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-image"></i> {{ __('master.Edit Subject Teacher') }}</h3>
                            <div class="card-tools">
                                <!--<a onclick="history.back()" class="btn btn-primary  btn-sm" title="View Gallery"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>-->
                                <a href="{{url('teacher_subject_add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-eye"></i> {{ __('View') }}</a>
                                <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                            </div>

                        </div>
                        <form id="form-submit-edit" action="{{ url('teacher_subject_edit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('common.Class') }}*</label>
                                        <select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id " name="class_type_id">
                                            <option value="">{{ __('common.Select') }}</option>
                                            @if(!empty($classType))
                                            @foreach($classType as $type)
                                            <option value="{{ $type->id ?? ''  }}"{{ ( $type['id'] == $data['class_type_id'] ??  old('class_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                       
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('common.Subject') }}*</label>
                                        <select class="form-control subject_id @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id">
                                            <option value="">{{ __('common.Select') }}</option>
                                            @if(!empty($getSubject))
                                            @foreach($getSubject as $type)
                                            <option value="{{ $type->id ?? ''  }}"{{ ( $type['id'] == $data['subject_id'] ??  old('subject_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('master.Teacher') }}*</label>
                                        <select class="form-control @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id">
                                            <option value="">{{ __('common.Select') }}</option>
                                            @if(!empty($getAllTeachers))
                                            @foreach($getAllTeachers as $type)
                                            <option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $data['teacher_id'] ??  old('teacher_id')) ? 'selected' : '' }}>{{ $type->first_name ?? ''  }} {{ $type->last_name ?? ''  }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('master.Time') }}</label>
                                        <select class="form-control subject_id @error('time_period_id') is-invalid @enderror" id="time_period_id" name="time_period_id">
                                            <option value="">{{ __('common.Select') }}</option>
                                            @if(!empty($getTimePeriod))
                                            @foreach($getTimePeriod as $type)
                                            <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['time_period_id'] ?? old('time_period_id')) ? 'selected' : '' }}>{{ date('h:i:s A', strtotime($type->from_time)) ?? ''  }} To {{ date('h:i:s A', strtotime($type->to_time)) ?? ''  }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                       
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