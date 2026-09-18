<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>School | Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-theme.css') }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @yield('content')

        <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var URL = "{{ url('/') }}";
        </script>
        <script src="{{URL::asset('public/assets/school/js/jquery-ui.min.js')}}"></script>
        <script src="{{URL::asset('public/assets/school/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{URL::asset('public/assets/school/js/jquery.validate.js')}}"></script>
        <script src="{{URL::asset('public/assets/school/js/additional-methods.js')}}"></script>
        <script src="{{URL::asset('public/assets/school/js/arise-theme.js')}}"></script>

        <script>
        $('#country_id').on('change', function(e){
            var country_id = $(this).val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                url: '/countryData/' + country_id,
                success: function(data){
                    $("#state_id").html(data);
                }
            });
        });

        $('#state_id').on('change', function(e){
            var state_id = $(this).val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                url: '/stateData/' + state_id,
                success: function(data){
                    $("#city_id").html(data);
                }
            });
        });
        </script>
        <script type="text/javascript">
            function isNumber(evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57))
                    return false;
                return true;
            }
        </script>

        @include('layout.csrf_refresh')
    </div>
</body>
</html>
