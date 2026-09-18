@php
$getstudents = Helper::getstudents();
$classType = Helper::classType();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getCountry = Helper::getCountry();
$getSetting=Helper::getSetting();

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
                            <h3 class="card-title"><i class="fa fa-certificate"></i> &nbsp;{{ __('master.Gate Pass Add') }} </h3>
                            <div class="card-tools">
                                <a href="{{url('gate_pass_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                                <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>

                        </div>
                        <form id="quickForm" action="#" method="post">
                            @csrf

                            <div class="row m-2">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="State" class="required">{{ __('master.Admission No.') }}</label>
                                        <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="{{ __('master.Admission No.') }}" value="{{ $search['admissionNo'] ?? '' }}">
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
                                    <label for="">&nbsp;</label>
                                    <button type="button" class="btn btn-primary " onclick="SearchValue()" style="margin-top:28px;">{{ __('common.Search') }}</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="student_list_show"></div>
                </div>
                <div class="col-md-12">

                    <div class="card m-3">
                        <div class="card-body">
                            <form id="gate_paas_add" action="{{ url('gate_pass_add') }}" method="post">
                                @csrf

                                <div class="row">

                                    <div class="col-md-3">
                                        <input type="hidden" name="admissionID" id="stuAdmissionNo" class="form-control" value="{{ old('admissionID') }}">

                                        <div class="form-group">
                                            <label style="color:red;">{{ __('master.Student Name') }} *</label>

                                            <input type="text" name="student_name" id="student_name" readonly onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control @error('student_name') is-invalid @enderror" placeholder="{{ __('master.Student Name') }}" value="{{ old('student_name') }}">

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
                                            <input type="text" name="father_name" id="father_name" readonly onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control @error('father_name') is-invalid @enderror" placeholder="{{ __('common.Fathers Name') }}" value="{{ old('father_name') }}">
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
                                            <input type="text" name="father_mobile" id="father_mobile" readonly class="form-control @error('father_mobile') is-invalid @enderror" placeholder="{{ __('common.Fathers Mobile') }}" value="{{ old('father_mobile') }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
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
                                            <input type="datetime-local" name="iessu_date" id="iessu_date" class="form-control @error('iessu_date') is-invalid @enderror" value="{{ now()->format('Y-m-d\TH:i') }}">
                                            @error('iessu_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>


                                <div class="col-md-6">
                                    <strong>{{ __('common.Fathers Mobile') }}</strong>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control rounded-0" name="father_mobile2" value="{{ old('father_mobile2') }}" readonly id="father_mobile2" placeholder="{{ __('common.Fathers Mobile') }}">
                                        <span class="input-group-append">
                                            <button type="button" id="otpSend" class="btn btn-info btn-flat">{{ __('master.OTP Send') }}</button>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <strong>{{ __('master.OTP') }}</strong>
                                    <input type="hidden" id="otpcheck">
                                    <input type="text" class="otpcheck_submit form-control rounded-0" name="otp" required maxlength="4" value="{{old('otp')}}" id="otp" placeholder="{{ __('master.OTP') }}">
                                    <p id="errormessage" class="text-danger mb-0"></p>
                                </div>

                                </div>
                                <h5>{{ __('master.Reciver Detail') }}</h5>

                                <div class="row m-2">
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group">
                                            <label style="color:red;">{{ __('master.Reciver Name') }} *</label>

                                            <input type="text" name="reciver_name" id="reciver_name" onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control invalid" placeholder="{{ __('master.Reciver Name') }}" value="{{ old('reciver_name') }}">
                                            
                                            <span class="invalid-feedback" id="reciver_name_invalid" role="alert">
                                                <strong>{{ __('master.Reciver name field is required') }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:red;">{{ __('master.Reciver Mobile') }} *</label>
                                            <input type="text" name="reciver_mobile" id="reciver_mobile" class="form-control invalid" placeholder=" {{ __('master.Reciver Mobile') }}" value="{{ old('reciver_mobile') }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
                                            <span class="invalid-feedback" id="reciver_mobile_invalid" role="alert">
                                                <strong>{{ __('master.Reciver mobile field is required') }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group">
                                            <label style="color:red;">{{ __('master.Relation') }} *</label>

                                            <input type="text" name="relation" id="relation" onkeydown="return /[a-zA-Z ]/i.test(event.key)" class="form-control invalid" placeholder="{{ __('master.Relation') }}" value="{{ old('relation') }}">
                                            <span class="invalid-feedback" id="relation_invalid" role="alert">
                                                <strong>{{ __('master.Relation field is required') }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-2">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" id="submit_check" class="btn btn-primary">{{ __('common.Submit') }}</button>
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

<script>

$('#submit_check').click(function(e){
    e.preventDefault();
    var all_filled_up = 2000;
    $('.invalid').each(function(){
        var this_value = $(this).val();
        var this_id = $(this).attr('id'); 
        if(this_value == ''){
            $('#' + this_id + '_invalid').css('display','block');
            all_filled_up = all_filled_up + ' && ' + this_value;
        }else{
            $('#' + this_id + '_invalid').css('display','none');
        }
    })
    if(all_filled_up == 2000){
        $('#gate_paas_add').trigger('submit');
    }else{
        $("html, body").animate({ scrollTop: 0 }, "slow");
    }
});


    function SearchValue() {
        var basurl = "{{url('/')}}";
        var class_type_id = $('#class_type_id :selected').val();
        var admissionNo = $('#admissionNo').val();
        var name = $('#name').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: basurl + '/search_gate_pass',
            data: {
                class_type_id: class_type_id,
                name: name,
                admissionNo: admissionNo,
            },
            //dataType: 'json',
            success: function(data) {
                $('.student_list_show').html(data);

            }
        });
    };
    
    $('#otpSend').click(function(){
        var basurl = "{{url('/')}}";
           var num =  $('#father_mobile2').val();
          
           if(num == ""){
               toastr.error("Father's number can not be left blank");
           }else if(num.length == 10){
                var digits = '0123456789';
                let OTP = '';
                for (let i = 0; i < 4; i++ ) {
                    OTP += digits[Math.floor(Math.random() * 10)];
                }
                                   toastr.success("OTP send Successfully ");

          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: basurl + '/gate_pass_otp',
            data: {
                OTP: OTP,
                num: num
            },

            success: function(data) {
                console.log('AJAX request successful:', data);
            },
           
        });
             $("#otpcheck").val(OTP);
                $(".otpcheck_submit").val("");
              //  toastr.success("Your One-Time password(OTP) :-" + " " + OTP);
                
           }else{
              toastr.error("Invalid Numbers");
           }
        });
        
    $('.otpcheck_submit').change(function(){
            var getotp = $('#otpcheck').val();
            var enterotp = $(this).val();
            if(enterotp == ""){
                $('#errormessage').html("Please enter your otp");
                $('#submit_check').prop('disabled', true);
            }else if(enterotp.length < 4){
                $('#errormessage').html("Invalid Otp");
                $('#submit_check').prop('disabled', true);
            }else if(enterotp != getotp){
                $('#errormessage').html("Invalid Otp");
                $('#submit_check').prop('disabled', true);
            }else{
                $('#submit_check').prop('disabled', false);
                $('#errormessage').html("");
                toastr.success("Success");
            }
        })
</script>

@endsection