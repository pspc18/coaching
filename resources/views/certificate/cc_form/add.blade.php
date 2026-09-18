@php
$getstudents = Helper::getstudents();
$classType = Helper::classType();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getCountry = Helper::getCountry();
$getSetting = Helper::getSetting();
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
                            <h3 class="card-title"><i class="fa fa-certificate"></i> &nbsp; {{ __('Achievement Certificate') }} </h3>
                            <div class="card-tools">
                                <a href="{{url('cc/form/index')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(5)->view ? '' : 'd-none' }}"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                                <a href="{{url('certificate_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>

                        </div>
                        <form id="quickForm" action="#" method="post">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="State" class="required">{{ __('certificate.Admission No.') }}</label>
                                        <input type="text" class="form-control" id="admission_no" name="admissionNo" placeholder="Admission No." value="{{ $search['admissionNo'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('common.Class') }}</label>
                                        <select class="select2  form-control" id="class_type_id" name="class_type_id">
                                            <option value="">{{ __('common.Select') }}</option>
                                            @if(!empty($classType))
                                            @foreach($classType as $type)
                                            <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-1 ">
                                    <label for="">&nbsp;</label><br>
                                    <button type="button" class="btn btn-primary " onclick="SearchValue()">{{ __('common.Search') }}</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="certificate_list_show"></div>
                </div>
               <div class="col-md-12">
                    <div class="card m-2">
                    <div class="card-body">
                        <form id="quickForm" action="{{ url('cc/form/add') }}" method="post">
                            @csrf
                    
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('certificate.Admission No.') }} *</label>
                                            <input type="text" name="admissionNo" id="admissionNo" class="form-control" placeholder="{{ __('certificate.Admission No.') }}" readonly="readonly" value="{{ old('admission_id') }}">
                                            <input type="hidden" name="admission_id" id="admission_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Student Name</label>
                                        <input type="text" id="student_name" readonly="readonly" class="form-control" placeholder="{{ __('certificate.Student Name') }}" value="{{ old('name') }}">
                                    </div>
                                </div>
                    
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('certificate.Date') }} *</label>
                                        <input type="date" name="iessu_date" id="iessu_date" class="form-control @error('iessu_date') is-invalid @enderror" value="{{date('Y-m-d') }}">
                                        @error('iessu_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('Class') }} </label>
                                        <input type="text" name="class_name" id="class_name" class="form-control" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('Achievement For') }} </label>
                                        <input type="text" name="achievement_for" id="achievement_for" class="form-control" placeholder="{{ __('Achievement For') }}" value="{{ old('achievement_for') }}">
                                        @error('achievement_for')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row m-2">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">{{ __('common.submit') }} </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
       </div>
    </div>
</section>
</div>

<script>
    function SearchValue() {
        var class_type_id = $('#class_type_id :selected').val();
        var admissionNo = $('#admission_no').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: '/student_search_certificate',
            data: {
                class_type_id: class_type_id,
                admissionNo: admissionNo
            },
            //dataType: 'json',
            success: function(data) {
                $('.certificate_list_show').html(data);

            }
        });
    };
</script>

@endsection