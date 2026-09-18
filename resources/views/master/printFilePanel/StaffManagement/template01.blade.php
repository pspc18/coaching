    @php
    $getSetting=Helper::getSetting();
    @endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <title>joining letter</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .font_family {
            font-weight: 700;
            font-family: initial;
        }
        
        .font_family2 {
            font-weight: 700;
            font-family: initial;
            font-size: 35px;
        }
        .border_b {    
            border-bottom: 1px solid black;
            font-weight: 100;
            font-size: 17px;

        }
        
        .join_letter {
            font-family: emoji;
            font-weight: bold;
            font-size: 43px;
        }
        .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            top: -53px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width:38%;
        }
        
        body{
                font-family: Arial, sans-serif;
    font-size: 12px;
    max-width: 740px;
    margin: 25px auto;
        }
    </style>
</head>

<body>
    <div class="container">
        
        <div class="row">
            <div class="card" style="background:black;padding:0.2%;">
                <div class="img_background_fixed">
            <div class="img_absolute">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmanisoft_logo.png' }}'" alt="" class="backhround_img">
            </div>
                <div class="card-body" style="background: white;">
                    <div class="row" style="padding: 15px;">
                     
                            <div class="row">
                                <div class="col-md-3 col-2">
                                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" width="90%">
                                </div>
                                <div class="col-md-8 col-10">
                                    <h4 class="font_family2 text-center">{{$getSetting['name'] ?? ''}}</h4>
                                    <p class="font_family text-center">Phone No : {{$getSetting['mobile'] ?? ''}}<br>Email : {{$getSetting['gmail'] ?? ''}}<br>Address : {{$getSetting['address'] ?? ''}}</p>
                                </div>
                                <div class="col-md-1 text-center">
                                </div>
                                <div class="col-md-12 text-center" style="border-top: 2px solid black;">
                                    <h1 class="join_letter mt-3">Joining Letter</h1>
                                </div>
                                <div class="col-md-12">
                                    <p class="font_family"><b>To,</b></p>
                                    <p class="font_family mb-0">Name :- <span class="border_b">{{$data['first_name'] ?? ''}} {{$data['last_name'] ?? ''}}</span></p>
                                    <p class="font_family mb-0">Mobile :- <span class="border_b">{{$data['mobile'] ?? ''}}</span></p>
                                    <p class="font_family mb-0">Email :- <span class="border_b">{{$data['email'] ?? ''}}</span></p>
                                    <p class="font_family mb-0">DOB :- <span class="border_b">{{date('d-M-Y', strtotime($data['dob'])) ?? '' }} </span></p>
                                    <p class="font_family ">Address :- <span class="border_b">{{$data['address'] ?? ''}}</span></p>


                                    <p class="font_family">Sub: JOINING LETTER</p>
                                    <p class="font_family">Dear Sir/Ma'am,</p>

                                </div>
                                <div class="col-md-12">
                                    <p class="font_family">I am immensely pleased tenform you that I accept the offer and acknowledge the same. I am ready to join as believing in me and offering me this position. I assure to work with sincerity and dedication. <span class="border_b"> <b>Teacher</b> </span> (Job
                                        Position) in your company on <span class="border_b"> <b>  {{date('d-m-Y', strtotime($data['joining_date'])) ?? '' }}</b>  </span>(date of joining). I sincerely thank you for believing in me and offering me this position. I assure to work with sincerity and dedication.</p>
                                    <p class="font_family">I will be submitting all the required documents on my joining date. Should you require any further information.</p>


                                    <p class="font_family">Yours faithfully</p>
                                   
                                </div>
                               
                                 <div class="col-md-12 text-right mt-5">
                                     <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" style="width: 9%;margin: 0px 70px 2px 4px;">
                                    <h3> Director Signature</h3>
                                     </div>

                            <!--<div class="col-md-12"><img src="{{ env('IMAGE_SHOW_PATH').'setting/watermark_image/'.$getSetting->watermark_image }}" style="background-repeat: no-repeat;transform: rotate(7deg);background-size: contain;background-position: center;position: absolute; margin-top: -44%;width: 61%; margin-left: 19%;">
                            </div>-->
                     
                     
                     
                     
                     
                     
                    </div>
                

                </div>


            </div>
        </div>

    </div>
    </div>

</body>

</html>