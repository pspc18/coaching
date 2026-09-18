@php
$setting = Helper::getSetting();
//dd($setting);
@endphp
<style>
.address{
     font-size: 14px;
}
p {
    margin-bottom:3px !important;
}

.back_li_student{
    font-size: 11px;
    margin-top: 5px;
    padding-top:1%;
}

.date_text span{
    font-size:10px;
}
.flex_row{
    display:flex;
    align-items:center;
    justify-content: space-around;
}

.flex_details{
    display:flex;
    align-items:center;
}
.dotted{
    border-bottom: 2px dotted;
}

.signature{
    text-align: left;
    border-right: hidden;
    padding-left: 16;
   
}
.student_sign{
    text-align: right;
 
}
.forth_point{
    padding-left: 4%;
padding-right: 6%;
}
.third_point{
    padding-left: 4%;
padding-right: 6%;
}
.second_point{
    padding-left: 4%;
padding-right: 6%;
}
.first_point{
    padding-left: 4%;
padding-right: 6%;
}
.back_side{
    border-width:3px !important;
    border: 2px solid black !important;
}

.logo_size{
width: 80px;
margin-top: 14px;
margin-bottom: 7px;
}
.first_col_nine{
    margin-top:-10px;
    font-size:12px; 
    margin-left:-6%;
}

.heading_name{
    color: #000099;
    font-family: impact;
    font-size: 20px;
     padding-top: 9px;
     margin-bottom: 0px;
}
.student_image{
     width: auto;
     height: 105px;
}
.input_design{
    border: 2px solid black;
    margin-left: -9%;
    margin-bottom: -6%;
    margin-top: -2%;
    color: red;
    font-size: 19;
    width: 271%;
}
.table_width{
    width:100%;
        height: 60px;
}

@media    only screen and (max-width: 600px) {

.logo_size{
    width: 45px;
    margin-top: 6px;
}
.heading_name{
    color:white; 
    font-family: impact;
    font-size: 18px;
        padding-top: 9px;
}
.address{
     font-size: 14px;
}
.all_font{
    font-size: 13px;
}
.data_name{
    font-size: 11px;
}
.student_image{
    width: 83px;
height: 130px;
margin-left: -2px;
}
.input_design{
   font-size: 13;
}
}

.id-card {
  width: 500px;
}
@media    print{@page    {size: landscape}}
</style>


<DOCTYPE html>
    <html>

   <head>
<title>Library Id</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">    
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/responsive.bootstrap4.css') }}">
</head>
        <body onLoad="window.print()">
        
    <div class="row ">
    
        <div class="id-card mt-3">
        <div class="container-fluid border border-dark" style=" width: 93%;margin-right: 10px; height: 270px;border-radius: 19px;">
        <div class="row" style="background-color: orange;    border-radius: 19px 18px 0px 0px;height: 100px;">
        <div class="col-sm-2 col-2">
            @if(!empty($setting['left_logo']))
            <img class="logo_size" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$setting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
            @else
            <p>NO Logo</p>
            @endif
            </div>
        <div class="col-sm-10 text-center col-10"><h1 class="heading_name">{{$setting['name'] ?? ''}}</h1>
            <div style="font-size: 15px;line-height: 16px;">
                <p><b>Email : </b>{{$setting['gmail'] ?? ''}}</p>
            <p><b>Mobile No : </b>{{$setting['mobile'] ?? ''}}</p>
            <p><b>Address : </b>{{$setting['address'] ?? ''}},{{$setting['City']['name'] ?? ''}} ,{{ $setting['State']['name'] ?? ''}} <br>{{ $setting['Country']['name'] ?? ''}},{{$setting['phonecode'] ?? ''}}</p> 
            </div>
        </div>
        </div>
        
        <div class="row" style="background-color:orange">
        <div class="col-sm-3 col-3"></div>
        <div class="col-sm-9 col-9 text-center first_col_nine"></div>
        </div>
        
        <div class="row pt-2" style="border-top: 5px solid orange;">
         
        <div class="col-sm-12 col-12">
            <p class="mb-0" style="color: red;">Admission No: {{$data->id ?? ''}}</p>
        </div>
        </div>
        <div class="row">
        
        <div class="col-sm-8 col-8">
        <div class="row">
        <div class="col-sm-3 col-3">
        <p class="all_font">Name</p>
        </div>
        <div class="col-sm-9 col-9">
        <p class="dotted data_name">{{$data->first_name ?? '' }} {{$data->last_name ?? '' }}</p>
        </div>
        <div class="col-sm-3 col-3">
        <p class="all_font">F.Name</p>
        </div>
        <div class="col-sm-9 col-9">
        <p class="dotted data_name">{{$data->father_name ?? '' }}</p>
           
        </div>
        <div class="col-sm-3 col-3">
        <p class="all_font">Mobile-</p>
        </div>
        <div class="col-sm-9 col-9">
        <p class="dotted data_name">
                   {{$data->mobile ?? '' }}
                    </p>
        </div>
        <div class="col-sm-3 col-3">
        <p class="all_font">Address</p>
        </div>
        <div class="col-sm-9 col-9">
            <?php
            $address = $data->address; // Replace this with your actual data
            $trimmed_address = strlen($address) > 25 ? substr($address, 0, 25) . '...' : $address;
            ?>
        <p class="dotted data_name">
                        
            {{ $trimmed_address ?? ''}}
                        </p>
        </div>
        </div>
        </div>
        
        <div class="col-sm-4 col-4 text-center">
           <div class="row">
        <div class="col-sm-12 p-2 col-12">
            
                <img class="student_image" src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
     
        </div> 
        </div> 
        </div>
        </div>
        
        </div>
        
        
</div>

     
        <div class="id-card mt-3">
  

           
        <div class="container-fluid border border-dark" style=" border-width:3px;margin: 0;width: 93%; border-radius: 19px;">
        <div class="row flex_row" style="background-color: orange;    border-radius: 19px 18px 0px 0px;">
            
            @if(!empty($setting['left_logo']))
            <img class="logo_size" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$setting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
            @else
            <p>NO Logo</p>
            @endif 
            <h1 class="heading_name">TERMS AND CONDITIONS</h1>
        </div>
        <div class="col-sm-12 col-12">
            <ul class="pl-1 mb-1">
                <li class="back_li_student">
                    Students who are in programs on the campus of Toronto Metropolitan University (Early Childhood Education,
                    Early Childhood Education Consecutive Degree, Early Childhood Assistant) are eligible for the Toronto Metropolitan University/ George
                    Brown College Photo-ID/One Card
                </li>
                <li class="back_li_student">
                    Students who are in programs on the campus of Toronto Metropolitan University (Early Childhood Education,
                    Early Childhood Education Consecutive Degree, Early Childhood Assistant) are eligible for the Toronto Metropolitan University/ George
                    Brown College Photo-ID/One Card
                </li>
            </ul>
        </div>
        <div class="col-sm-12 col-12">
            <div class="row flex_details">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-7 date_text flex_details">
                            <div>
                            <span><b>Phone :</b> {{$setting['mobile'] ?? ''}}</span><br>
                            <span><b>Mail :</b> {{$setting['gmail'] ?? ''}}</span><br>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            @if(!empty($setting['seal_sign']))
                            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$setting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" style="height: 37px;width: 37px;" >
                         @else
                        <p>NO Logo</p>
                        @endif
                            <p>Principal</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                                          <div>
                              @php
                      $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                      @endphp
                        <img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($data->id, $generatorPNG::TYPE_CODE_128)) }}">
                    </div>
                    <div class="date_text">
                        <span><b>Joined Date :</b> {{date('d-m-Y', strtotime($data['date'])) ?? '' }}</span>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    
    </div>
    
    
    
    

</div>



</body>
</html>


