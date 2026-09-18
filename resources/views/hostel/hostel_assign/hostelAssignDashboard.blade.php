@php
    $getHostel = Helper::getHostel();
    $classType = Helper::classType();
    $getgenders = Helper::getgender();
    $getSetting=Helper::getSetting();
    $getPaymentMode = Helper::getPaymentMode();

   
@endphp
@extends('layout.app') 
@section('content')
<style>
.select2-container .select2-selection--single {height:38px !important;}
.select2-container--default .select2-selection--single .select2-selection__arrow {height:38px !important;}
.c_height {height: 160px;overflow-y:scroll;}
.c_height1 {height: 260px;overflow-y:scroll;}
.bed {
    display: none;
}
@media (max-width: 600px) {
  .modal div {
      font-size:10px;
  }
}
@media (min-width: 605px) {
  .level4 .btn-xs {
      font-size:1.7rem;
  }
}
</style>

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row text-center">
          <div class="col-12">
            <div class="card card-outline card-orange">
                
                    <div class="row text-center m-2">
                        <div class="col-md-12">
                        @if(!empty($getHostel)) 
                            @foreach($getHostel as $hostel)
                                <button class="btn btn-primary hostels" data-id="{{ $hostel->id ?? ''  }}">{{ $hostel->name ?? ''  }}</button>
        	                @endforeach
                        @endif
                        </div>

                            <div class="col-md-12 text-center"><h1 id="fetchingDetails" class="mt-2 d-none"><i class="fa fa-spinner fa-spin"></i></h1></div>
                            <div id="hostelElement"></div>                 
                    
                    

                    </div>
                   
                   
                    </div>
                
            </div>
</div>
</div>
</div>
</section>
</div>

                
<style>
    .hostel_bed{
        width:11px;
        height:15px;
    }
    .hostel_bed_2{
    width: 8px;
  height: 10px;
    }
    .hostel_bathroom{
        width:35px;
        height:25px;
    }
    .hostel_door{
        width:35px;
        height:25px;
        position: relative;
        bottom: -4px;
    }
    .hostel_door_2{
        width:35px;
        height:25px;
        position: relative;
          transform: rotate(180deg);
          top:-4px;
       
    }
    .hostel_stairs{
      width: 79px;
  height: 104px;
     
    }
</style>

<style>
    .fixed-container-big_2 {
            /*position: absolute;*/
            padding:10px;
            top: 20px; /* Adjust the position as needed */
            left: 20px; /* Adjust the position as needed */
            width: 360px;
            height: 215px;
            
            /*border: 1px solid #ccc;*/
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform-origin: 0 0; /* Prevents scaling from the center */
            transform: scale(1); /* Maintains original scale */
            overflow: auto;
            display: flex;
            flex-direction: column; /* Stacks upper and lower parts vertically */
        }
    .fixed-container-big {
            /*position: absolute;*/
            padding:10px;
            top: 20px; /* Adjust the position as needed */
            left: 20px; /* Adjust the position as needed */
            width: 240px;
            height: 215px;
            
            /*border: 1px solid #ccc;*/
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform-origin: 0 0; /* Prevents scaling from the center */
            transform: scale(1); /* Maintains original scale */
            overflow: auto;
            display: flex;
            flex-direction: column; /* Stacks upper and lower parts vertically */
        }
    .fixed-container {
            /*position: absolute;*/
            padding:10px;
            top: 20px; /* Adjust the position as needed */
            left: 20px; /* Adjust the position as needed */
            width: 120px;
            height: 215px;
            
            /*border: 1px solid #ccc;*/
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform-origin: 0 0; /* Prevents scaling from the center */
            transform: scale(1); /* Maintains original scale */
            overflow: auto;
            display: flex;
            flex-direction: column; /* Stacks upper and lower parts vertically */
        }
        .upper-part {
           flex: 1;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: repeat(5, 1fr);
  border: 2px solid black;
  padding: 1px;
        }
        .upper-part_2 {
           flex: 1;
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  grid-template-rows: repeat(12, 0.1fr);
  border: 2px solid black;
  padding: 1px;
        }
        .upper-part_3 {
           flex: 1;
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  grid-template-rows: repeat(1, 1fr);
  border: 2px solid black;
  padding: 1px;
        }
        .bathroom {
           flex: 2;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: repeat(1, 1fr);
  border: 2px solid black;
  padding: 1px;
        }
        .box {
            border: 1px solid #000; 
            display: flex;
            align-items: center;
            justify-content: center;
            margin:1px;
            /*font-size: 12px;*/
          
            
            background-color: #adffad;
        }
        .box1 {
            border: 1px solid #000; 
            display: flex;
            align-items: center;
            justify-content: center;
            margin:1px;
            /*font-size: 12px;*/
          
            
            background-color: #adffad;
        }
        .box_none {
            border: 0px solid #000; 
            display: flex;
            align-items: center;
            justify-content: center;
            /*font-size: 12px;*/
            background-color: #fff;
        }
       .lower-part {
           flex: 1;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: repeat(6, 1fr);
  border: 2px solid black;
  padding: 1px;
        }
       .lower-part_2 {
           flex: 1;
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  grid-template-rows: repeat(3, 1fr);
  border: 2px solid black;
  padding: 1px;
        }

             .rotate-90 {
        transform: rotate(90deg);
    }
             .rotate-120 {
        transform: rotate(-32deg);
    }
             .rotate-270 {
        transform: rotate(270deg);
    }
             .rotate-180 {
        transform: rotate(180deg);
    }
    
    .bed_green{
        background-color:#adffad;
    }
    .bed_red{
        background-color:#ffadad;
    }

</style>

<script>
$(document).ready(function() {
        

    $('.hostels').on('click', function() {
        
        $('#fetchingDetails').removeClass('d-none');
        var id = $(this).attr('data-id');
        
        var hostelElement = $('<div></div>', {});
        hostelElement.load('{{ url("getHostelAssignDashboard") }}/' + id, function() {
            $('#hostelElement').html('');
            $('#hostelElement').append(hostelElement);
            $('#fetchingDetails').addClass('d-none');
        });

        
    });
 
       
    
});
</script>
@endsection      