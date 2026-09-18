@php
$setting = Helper::getSetting();

@endphp
<script type="text/javascript">
    //winow.print();
</script>
<style>
    .student_img {
        border-radius: 10px;
    width: 95%;
    height: 180px;
    }
    
    .id_tites_names{
        font-size: 15px;
        font-weight:600;
        margin-bottom:0px;
    }

    .student_add_no {
        margin-top: 15px;
        font-size: 14px;
        margin-left: 10px;
        color: red;
    }

    .school_logo {
        width: 90px;
        margin-top: -9px;
        margin-left: 1px;
    }

    .principal {
        font-size: 17px;
        color: red;
    }
    .article-container {
  display: flex;
  flex-wrap: wrap;
}

.article {
  flex-grow: 1;
  flex-basis: 50%;
}

.article:after {
  content: "";
  flex: auto;
}
</style>

<DOCTYPE html>
    <html>

    <head>
        <title>School Id</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/school/css/responsive.bootstrap4.css') }}">
    </head>
    

    <body onload="window.print()">
        @if(!empty($data))
           <div class="article-container" >
        

        <div class="article p-2" >


            <div class="container-fluid border border-dark" style="border-radius: 33px 33px 25px 25px; border-width:3px !important;margin-right:200px;margin-right:10%;">
                <div class="row" style=" border-radius: 30px 30px 0px 0px;">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10 text-center">
                        <h1 style="color: #000099; font-family: impact; font-size: 22px;">{{$setting['name'] ?? ''}}</h1>
                    </div>
                </div>

                <div class="row" style="margin-bottom:8px;">
                    <div class="col-sm-3"><img  style="position: absolute;  margin: -29px 10px;" class="school_logo" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$setting['left_logo'] }}"></div>
                    <div class="col-sm-9 text-center" style="font-size:12px; margin-left:-6%; line-height:4px;">
                        <p>{{$setting['gmail'] ?? ''}}</p>
                        <p>{{$setting['mobile'] ?? ''}}</p>
                        <p>{{$setting['address'] ?? ''}},{{$setting['City']['name'] ?? ''}},{{ $setting['State']['name'] ?? ''}},{{ $setting['Country']['name'] ?? ''}}{{$setting['phonecode'] ?? ''}}</p>
                    <br><span class="text-danger">[  Office Copy ]</span></div>
                </div>
 
                 
                
                <div class="row">
                   
                    <div class="col-sm-9">
                    <h5><b class="text-danger">Student Information</b> </h5>

                        <p class="id_tites_names">Name:- {{ $data['student_name'] ?? '-' }} <b class="text-danger">[Addmission No. {{$data->admissionNo ?? ''}}]</b></p>
                        <p class="id_tites_names">Class:- {{ $data['class_name'] ?? '-' }}</p>
                        <p class="id_tites_names">Tel:- {{$data['father_mobile'] ?? '-'}}</p>
                        <p class="id_tites_names">Add:- {{$data['student_address'] ?? '-'}}</p>
                        <h5><b class="text-danger">Reciver Information</b></h5>
                        <p class="id_tites_names">Reciver Name:-{{ $data['reciver_name'] ?? '' }} </p>
                        <p class="id_tites_names">Reciver Mobile:-{{ $data['reciver_mobile'] ?? '' }} </p>
                        <p class="id_tites_names">Reciver Relation:-{{ $data['relation'] ?? '' }} </p>

                    </div>
                    <div class="col-sm-3">
                        @if(!empty($data['student_image']))
                            <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['student_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/user_image.jpg' }}'">
                            @else
                            <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/student_id.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/user_image.jpg' }}'">
                            @endif
                    </div>
                    
                    </div>
                    
             

                    <div class="row">
                    <div class="col-sm-6"><b>Date : {{date('d-m-Y H:i:s', strtotime($data->iessu_date)) ?? ''}}</b></div>
                    <div class="col-sm-3 text-left" style="white-space: nowrap;margin-top: 2%;"><b class="principal">
                      <br>  Receiver Sign</b></div>
                    <div class="col-sm-3 text-left" style="white-space: nowrap;"><b class="principal">
                        
                            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$setting['seal_sign'] }}" style="height: 37px;width: 37px;" alt="signature"/>
                        <br>Principal Sign</b></div>
                </div>

                <div class="row" style="border-radius:0px 0px 50px 50px;">
                    <div class="col-sm-10"></div>
                    <div class="col-sm-2">&nbsp;</div>
                </div>

            </div>

        </div>

  @endif
        @if(!empty($data))
                

        <div class="article p-2">


            <div class="container-fluid border border-dark" style="border-radius: 33px 33px 25px 25px; border-width:3px !important;margin-right:200px;margin-right:10%;">
                <div class="row" style=" border-radius: 30px 30px 0px 0px;">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10 text-center">
                        <h1 style="color: #000099; font-family: impact; font-size: 22px;">{{$setting['name'] ?? ''}}</h1>
                    </div>
                </div>

                <div class="row" style="margin-bottom:8px;">
                    <div class="col-sm-3"><img class="school_logo" style="position: absolute;  margin: -29px 10px;" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$setting['left_logo'] }}"></div>
                    <div class="col-sm-9 text-center" style="line-height:4px; font-size:12px; margin-left:-6%;">
                        <p>{{$setting['gmail'] ?? ''}}</p>
                        <p>{{$setting['mobile'] ?? ''}}</p>
                        <p>{{$setting['address'] ?? ''}},{{$setting['City']['name'] ?? ''}},{{ $setting['State']['name'] ?? ''}},{{ $setting['Country']['name'] ?? ''}}{{$setting['phonecode'] ?? ''}}</p>
                        
                        <br><span class="text-danger">[  Student Copy ]</span></div>
                </div>
               

                <div class="row">
                   
                    <div class="col-sm-9">
                    <h5><b class="text-danger">Student Information</b> </h5>

                        <p class="id_tites_names">Name:- {{ $data['student_name'] ?? '-' }} <b class="text-danger">[Addmission No. {{$data->admissionNo ?? ''}}]</b></p>
                        <p class="id_tites_names">Class:- {{ $data['class_name'] ?? '-' }}</p>
                        <p class="id_tites_names">Tel:- {{$data['father_mobile'] ?? '-'}}</p>
                        <p class="id_tites_names">Add:- {{$data['student_address'] ?? '-'}}</p>
                        <h5><b class="text-danger">Reciver Information</b></h5>
                        <p class="id_tites_names">Reciver Name:-{{ $data['reciver_name'] ?? '' }} </p>
                        <p class="id_tites_names">Reciver Mobile:-{{ $data['reciver_mobile'] ?? '' }} </p>
                        <p class="id_tites_names">Reciver Relation:-{{ $data['relation'] ?? '' }} </p>

                    </div>
                    <div class="col-sm-3">
                        @if(!empty($data['student_image']))
                            <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['student_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/user_image.jpg' }}'">
                            @else
                            <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/student_id.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/user_image.jpg' }}'">
                            @endif
                    </div>
                    
                    </div>

               <div class="row">
                    <div class="col-sm-6"><b>Date : {{date('d-m-Y H:i:s', strtotime($data->iessu_date)) ?? ''}}</b></div>
                    <div class="col-sm-3 text-left" style="white-space: nowrap;    margin-top: 2%;"><b class="principal">
                      <br>  Receiver Sign</b></div>
                    <div class="col-sm-3 text-left" style="white-space: nowrap;"><b class="principal">
                        
                            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$setting['seal_sign'] }}" style="height: 37px;width: 37px;" alt="signature"/>
                        <br>Principal Sign</b></div>
                </div>

                <div class="row" style="border-radius:0px 0px 50px 50px;">
                    <div class="col-sm-10"></div>
                    <div class="col-sm-2">&nbsp;</div>
                </div>

            </div>

        </div>
   @endif

        </div>
        </div>

    </body>

    </html>