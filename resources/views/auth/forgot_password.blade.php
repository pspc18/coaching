@php
$setting = DB::table('settings')->get()->first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
@if(!empty($setting)) 
    <title>{{ $setting->name ?? '' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ env('IMAGE_SHOW_PATH').'setting/left_logo/'.$setting->left_logo ?? ''}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/mini_logo.png' }}'">
@endif

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-theme.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .login-card-body,
    .register-card-body {
      background-color: #00000000;
      border-top: 0;
      color: #fff;
      padding: 20px;
      box-shadow: 12px 12px 6px #a5a5a5;
    }

    .card1 {
      position: relative;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
      -webkit-flex-direction: column;
      -ms-flex-direction: column;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #1b6899 !important;
      background-clip: border-box;
      border: 0 solid rgba(0, 0, 0, .125);
      border-radius: .25rem;
    }

    span {
      color: #09c5f2;
    }

    .btn.btn-primary {
      background: #09c5f2;
      border: 1px solid #09c5f2;
      color: #fff;
    }

    .btn.btn-primary:hover {
      border: 1px solid #ffffff;
      background: transparent;
      color: #ffffff;
    }
    
        i {
      color: white;
    }

    .main_logo {
      position: fixed;
      top: 10px;
      right: 40px;
      filter: drop-shadow(6px 5px 3px #7d7d7d);
    }

    .title {
      font-weight: 600;
      font-size: 18px;
      font-family: Georgia;
      letter-spacing: 3px;
      margin: -60px auto;
      margin-bottom: 1px;
      text-transform: capitalize;
      text-shadow: 5px 5px 4px gray;
    }
    
    .bg_image{
        background-image: url('{{ env('IMAGE_SHOW_PATH').'default/Icon_images/rm347-porpla-01.jpg' }}');
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }
    
    .title_card{
        margin-bottom: 20px;
        text-align: center;
    }
    
    .f_18{
        font-size:18px;
    }
    
    
    @media only screen and (max-width: 600px) {
            .bg_image{
                background-color: #e8e4d9;
                background-image:none;
            }
            
            .main_logo {
                top:50px;
                right: auto;
            }
            
            body{
                overflow:hidden;
            }
        }
  </style>
</head>
<body class="hold-transition login-page bg_image">
    @if(!empty($setting))
        <img src="{{ env('IMAGE_SHOW_PATH').'setting/left_logo/'.$setting->left_logo ?? ''}}" alt="Company Logo" class="main_logo" width="100px">
    @endif
    <div class='title'>@if(!empty($setting)) {{ $setting->name ?? '' }}@endif</div>
    
<div class="login-box">

    <div class="card card1 mt-4">
      <div class="card-body login-card-body">
        <div>
            <h4 class="pt-3 pb-3 text-center f_18">Please Enter Your Registered User Name</h4>
            
         

        <form action="{{ url('forgot_password') }}" method="post">
            @csrf
            @include('layout.message')
          <div class="input-group mb-3">
            <input type="text" name="email" class="form-control @error('email') is-invalid  @enderror" placeholder="User Name" value="{{old('email')}}">
           
            <div class="input-group-append">
              <div class="input-group-text">
                <i class="fa fa-envelope"></i>
              </div>
            </div>
             @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div> 
            @enderror
          </div>

          <div class="row">

            <div class="col-4"></div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Send</button>
            </div>
            <div class="col-4"></div>
         
            <div class="col-12 text-right"><a href="{{ url('login') }}" class="text-white"><i class="fa fa-arrow-circle-o-left"></i> Back</a></div>
          </div>
        </form>


      </div>
    </div>
  </div>
 </div>
 <!-- jQuery -->
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{URL::asset('public/assets/school/js/bootstrap.bundle.min.js')}}"></script>
<!-- Arise Theme App -->
<script src="{{URL::asset('public/assets/school/js/arise-theme.js')}}"></script>
</body>
</html>