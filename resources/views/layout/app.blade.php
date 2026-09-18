@php
$setting = Helper::getSetting();
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <title>{{ $setting->name ?? '' }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset($setting->left_logo ?? '') }}" width="42px" height="42px">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-theme.css') }}">    
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/tempusdominus-bootstrap-4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/icheck-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/mobilescreen.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/daterangepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/common.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-modals.css') }}?v=1789536729">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">     
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/select2-bootstrap4.min.css') }}">
        <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
        <script src="{{URL::asset('public/assets/school/js/form/form_save.js')}}"></script>
        @yield('styles')
        @stack('styles')
    </head>
    <style>
        /* Universal Bulletproof Modal Stacking & Overlay Fix */
        .modal-backdrop, div.modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 1040 !important;
            background-color: #001428 !important;
        }
        .modal-backdrop.show {
            opacity: 0.58 !important;
        }
        .modal, div.modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 1050 !important;
            outline: 0 !important;
        }
        .modal.show, div.modal.show {
            z-index: 1055 !important;
            display: block !important;
        }
        .modal-dialog, div.modal-dialog {
            position: relative !important;
            z-index: 1060 !important;
        }
        .modal-content, div.modal-content, .modal .modal-content {
            position: relative !important;
            z-index: 1065 !important;
            background: #ffffff !important;
        }
        .centered_flex{
            display:flex;
            margin-bottom:10px;
        }
        
        .centered_flex i{
            font-size: 34px;
        }
        
        .centered_flex p{
           margin-bottom: 0px; 
           font-size: 20px;
           margin-left:10px;
           font-weight:600;
        }
        
        .error_message_whatsapp{
            font-weight: 400;
            text-transform: capitalize;
            line-height: 20px;
            margin-bottom: 20px;
        }
        
        .modal_btn{
            border: none;
            padding: 5px 20px;
            color: black;
            font-weight: 600;
        }
        
        .whatsapp_note{
            margin-bottom: 0px;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
    
    <body class="sidebar-mini layout-fixed">
        <div class="sidebar-backdrop"></div>
        <div class="wrapper">
            @include('layout.header')
            @if(Session()->get('role_id') ==3)
                @include('layout.student_sidebar')
            @else
                @include('layout.sidebar')
            @endif
            @include('layout.message')
            @yield('content')
            <script>
                /*$.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });*/
                //var URL  = "{{ url('/') }}";
            </script>
            
            <script src="{{URL::asset('public/assets/school/js/jquery-ui.min.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/jquery.dataTables.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/dataTables.bootstrap4.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/bootstrap.bundle.min.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/Chart.min.js')}}" defer></script>
            <script src="{{URL::asset('public/assets/school/js/moment.min.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/daterangepicker.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/tempusdominus-bootstrap-4.min.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/jquery.validate.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/additional-methods.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/arise-theme.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/toastr.min.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/select2.full.min.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/update.js')}}"></script>

        <script type="text/javascript">
            // Safe polyfill for legacy views calling .buttons()
            $(function () {
                if ($.fn.dataTable && !$.fn.dataTable.Buttons) {
                    $.fn.dataTable.Api.register('buttons()', function () {
                        return {
                            container: function () {
                                return $('<div class="dt-buttons d-none"></div>');
                            }
                        };
                    });
                }

                if ($('#example1').length && $.fn.DataTable) {
                    var example1Options = {
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "pageLength": 25
                    };

                    if ($('#select_all_students').length) {
                        example1Options.columnDefs = [{
                            targets: 0,
                            orderable: false,
                            searchable: false
                        }];
                    }

                    $("#example1").DataTable(example1Options);
                }

                if ($('#example2').length && $.fn.DataTable) {
                    $('#example2').DataTable({
                        "paging": true,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false
                    });
                }
            });

            $(document).ready(function(){
                if ($(window).width() < 400 && $('#example1').length) {
                    $('#example1').addClass('table-responsive nowrap');
                    $("#example1 tr td").css('padding', '6px');
                }
            });

            function isNumber(evt){
                var charCode = (evt.which) ? evt.which : event.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57))
                    return false;
                return true;
            }

            $(function () {
                if ($.fn.select2) {
                    $('.select2').select2();
                    $('.select2bs4').select2({
                        theme: 'bootstrap4'
                    });
                }
            });
            var calendarElement = $('<div>', {});
            calendarElement.load('{{ url("calendarElement") }}', function() {
                $('#calendarElement').append(calendarElement);
            });
        </script>
                
        <script>
        $('#country_id').on('change', function(e){
                var baseurl = "{{ url('/') }}";
            	var country_id = $(this).val();
                $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            	  url: baseurl+'/countryData/'+country_id,
            	  success: function(data){
            			$("#state_id").html(data);
            	  }
            	});
            	
            });
        $('#state_id').on('change', function(e){
                var baseurl = "{{ url('/') }}";
            	var state_id = $(this).val();
                $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            	  url: baseurl+'/stateData/'+state_id,
            	  success: function(data){
            			$("#city_id").html(data);
            	  }
            	});
            	
            });
            
        $('#class_type_id').on('change', function(e){
            var baseurl = "{{ url('/') }}";
        	var class_type_id = $(this).val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
        	    url: baseurl + '/subjectGetData/' + class_type_id,
        	    success: function(data){
        			$("#subject_id").html(data);
        	    }
        	});
        });            
        </script>
    
       
        
        <script>
        
        var timer2 = "5:01";
var interval = setInterval(function() {


  var timer = timer2.split(':');
  //by parsing integer, I avoid all extra string processing
  var minutes = parseInt(timer[0], 10);
  var seconds = parseInt(timer[1], 10);
  --seconds;
  minutes = (seconds < 0) ? --minutes : minutes;
  if (minutes < 0) 
  {
  clearInterval(interval);
   location.reload();
  }
  
  
  seconds = (seconds < 0) ? 59 : seconds;
  seconds = (seconds < 10) ? '0' + seconds : seconds;
  //minutes = (minutes < 10) ?  minutes : minutes;
  

  $('.countdown').html(minutes + ':' + seconds);
  timer2 = minutes + ':' + seconds;
    //console.log(minutes + ':' + seconds);
}, 1000);

    $(document).on( "mousemove keypress", function () {
 timer2 = "5:01";
 
 
});


</script>


<script>
$(document).ready(function() {
    var baseUrl = "{{ url('/') }}";
    
    $(document).on('dblclick', '.editable', function() { // Changed here
        var currentTd = $(this);
        var currentValue = $(this).text().trim();
        var field = $(this).attr('data-field');
        var modal = $(this).attr('data-modal');
        var id = $(this).attr('data-id');
       
        var inputField = $(`<input type="text" name="${field}" data-id="${id}" data-modal="${modal}">`).val(currentValue);
     
        $(this).empty().append(inputField);
        
        inputField.focus();

        inputField.blur(function() {
            var newValue = $(this).val().trim();
            $(this).parent().text(currentValue);
        });

        inputField.focusout(function(event) {
            var input = $(this);
            var inputData = $(this).val();

            if(inputData != '') {
                var inputField = $(this).attr('name');
                var inputModal = $(this).attr('data-modal'); 
                var inputId = $(this).attr('data-id');
                
                $.ajax({ 
                     headers: { 
                         'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                     },
                     url: baseUrl + '/updateSingleField',  
                     type: 'POST',  
                     contentType: 'application/json',
                     data: JSON.stringify({ name: inputField, value: inputData, id: inputId, modal: inputModal }), 
                     success: function(response) {
                         if(response.status) {
                             currentTd.text(inputData);
                             toastr.success(response.message);
                         } else {
                             input.blur();
                             toastr.error(response.message);
                         }
                     },
                     error: function(xhr, status, error) {
                         console.error('Error saving data:', error);
                     }
                 });
            } else {
                input.blur();
                currentTd.text(currentValue);
                toastr.error('Nullable field not be allowed');
            }
        });
    });
});



</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("img").forEach(function (img) {
        img.onerror = function () {
            this.onerror = null;
            this.src = "{{ asset('public/images/default/default.png') }}";
        };
    });
});
</script>






    <script>
document.addEventListener("click", function(e) {
  if (e.target.closest(".tooltip_disable")) {
    e.preventDefault(); // stop link navigation
    e.stopPropagation(); // optional: block bubbling
  }
});
</script>

    @include('layout.csrf_refresh')
    @yield('scripts')
    @stack('scripts')
            <script>
            // Bulletproof Modal Body Relocation & Overlay Fix
            $(document).on('show.bs.modal', function (e) {
                var $modal = $(e.target);
                if (!$modal.parent().is('body')) {
                    $modal.appendTo('body');
                }
                setTimeout(function () {
                    $('.modal-backdrop').css('z-index', 1040);
                    $modal.css('z-index', 1055);
                    $modal.find('.modal-dialog').css('z-index', 1060);
                    $modal.find('.modal-content').css('z-index', 1065);
                }, 0);
            });
        </script>
</body>
</html>



@include('initial.initialView')
