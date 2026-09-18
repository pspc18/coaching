@php
$getSetting = Helper::getSetting();
$getUser = Helper::getUser();
$siblingsList = Helper::getSiblings();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}"> 
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>{{$getSetting->name ?? ''}}</title>
  <style>
body {
    max-width: 450px !important;
    width: 100% !important;
    margin: 0 auto !important;
    overflow-x: hidden !important;
    position: relative !important;
    padding-bottom: 96px !important;
}
.app-header,
.app-footer ,#switchBottomSheet,#photoBottomSheet,.modal{
    max-width: 450px !important;
    margin: 0 auto !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    right: auto !important;
}
.main-content{
    padding-bottom: 96px;
}
* {
    max-width: 100% !important;
}  
  </style>
 
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script>
    (function() {
      const savedTheme = localStorage.getItem("theme") || "dark";
      document.documentElement.setAttribute("data-theme", savedTheme);
    })();
  </script>
  <link rel="stylesheet" href="{{ asset('public/assets/student_login/css/style.css') }}?v={{ filemtime(public_path('assets/student_login/css/style.css')) }}">
</head>
<body>
   <div id="pageLoader" class="page-loader">
    <div class="loader-spinner"></div>
  </div>

   <div class="switch-bottom-sheet" id="switchBottomSheet">
    <div class="switch-sheet-content">
      <div class="sheet-handle"></div>
      <h5 class="sheet-title">Switch User</h5>
      <ul class="switch-option-list">

            <li data-value="{{Session::get('id')}}" class="active" style="color: greenyellow;font-weight:bolder;">
                {{Session::get('first_name')}} - {{$getUser['ClassTypes']['name']}}
            </li> 
            @foreach($siblingsList as $sib)
                <li data-value="{{ $sib->id }}"
                    class="{{ $sib->id == Session::get('id') ? 'active' : '' }}" onclick="window.location.href='{{ url('sibling/login/'.$sib->id) }}'">
                    {{ $sib->first_name }} - {{$sib['ClassTypes']['name']}}
                </li>
            @endforeach
        </ul>

      <div class="cancel-btn" id="cancelSwitch">Cancel</div>
    </div>
  </div>
  
  
    @include('student_login.layout.header')
    @include('student_login.layout.sidebar')
    @include('student_login.layout.student_modal')
    @include('student_login.layout.message')
  <main class="main-content">
    @yield('content')
  </main> 
   @include('student_login.layout.footer')
  @yield('scripts')
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   <script src="{{URL::asset('public/assets/student_login/js/main.js')}}?v={{ filemtime(public_path('assets/student_login/js/main.js')) }}"></script>
 @include('layout.csrf_refresh')
</body>
</html>
