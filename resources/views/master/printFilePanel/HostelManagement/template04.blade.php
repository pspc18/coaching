@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form style="width: 100%; border: 4px solid rgb(0, 0, 0);  background-color: #ffff0059; ">
        <br>
        <table style="border: 4px solid rgb(0, 0, 0); margin-left: 10%; width: 80%; text-align: center; border-radius: 25px;">
            <tr>
                <td style="font-weight: 900; font-size: 500%;">
                    UDAY
                </td>
            </tr>
            <tr>
                <td style="font-weight: 600; font-size: 300%;">
                    Boys Hostel
                </td>
            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td style="font-size: 102%; text-align: center;">{{ $getSetting-> address ?? ''}} Ph. 9636345680</td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td style="background-color: rgb(0, 0, 0);height: 1px;"></td>
            </tr>
        </table>

        <table style="width: 100%; text-align: center;">
            <tr>
                <td>
                    <i>(Plenty tick the relevante)</i>
                </td>
            </tr>
        </table>


        <table style="width: 100%;">
            <tr>
                <td style="background-color: black; color: white; height: 20px;position: absolute; margin-top: -20px; ">ADMISSION FORM</td>
                    <td style=" width: 90%;position: absolute;">
                        <table >
                            <tr>
                                <td>Session 201</td>
                                <td style="border-bottom:groove;width: 12%;"></td>
                                <td>201</td>
                                <td style="border-bottom:groove;width: 12%;"></td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td>Form No.</td>
                                <td style="border: 1px solid rgb(0, 0, 0); width: 50px;"></td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td>Institute <br>
                                    Reit. No</td>
                                <td style="border: 1px solid rgb(0, 0, 0); width: 70px;"></td>
                            </tr>
                        </table>
                    </td>
                <td style="border: 2px solid rgb(0, 0, 0); width: 29%; position: relative; height: 100px; ">
                    <table>
                        <tr>
                            <td>Foundation Batch</td>
                            <td style="border: 1px solid rgb(0, 0, 0); width: 20px;" ></td>
                            <td>Maths</td>
                            
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 64%;"></td>
                            <td style="border: 1px solid rgb(0, 0, 0); width: 20px" ></td>
                            <td>Maths</td>
                            
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 10%;"></td>
                            <td>XI-Foundation</td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 10%;"></td>
                            <td>XII-Foundation</td>
                        </tr>
                    </table>
                </td> 
                <td style="border: 2px solid rgb(0, 0, 0); width: 28%; position: relative; height: 100px;  ">
                    <table>
                        <tr>
                            <td>Leader Batch</td>
                            <td style="border: 1px solid rgb(0, 0, 0); width: 20px;" ></td>
                            <td>Mathssds</td>
                            
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 51%;"></td>
                            <td style="border: 1px solid rgb(0, 0, 0); width: 20px;" ></td>
                            <td>Repeater</td>
                            
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 10%;"></td>
                            <td>ALIMS/NEET</td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 10%;"></td>
                            <td>IIT-JEE Main&Adv</td>
                        </tr>
                    </table>
                </td>
                
               <td style="border: 2px solid rgb(0, 0, 0); width: 19%; position: absolute; height: 155px;  margin-top: -31px;">
                    <img src="{{ env('IMAGE_SHOW_PATH').'guardian_photo/'.$data['father_img'] }}" width="100%" height="150px" style="border: 1px solid #978e8e;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                </td>

                
            </tr>
        </table>

        <table style="width: 100%; ">
            <tr>
                <td style="width: 18%;"></td>
                <td>VI/VII/VIII/IX/X Pre-Foundation</td>
                <td style="border: 1px solid rgb(0, 0, 0); width: 195px; "></td>
                <td style="width: 21%;"></td>
            </tr>
        </table>
          

      



        <table style="width: 100%;">
            <tr>
                <td style="background-color: rgb(0, 0, 0);height: 1px;"></td>
            </tr>
        </table>
        
        <table>
            <tr>
                <th>
                    <i>Please Fill Form in Capital Letters</i>
                </th>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td>Name of Students ;</td>
                <td style="border-bottom:groove;width: 70%; ">{{$data['first_name'] ?? ''}}</td>
            </tr>

            <tr>
                <td>Father's Name ;</td>
                <td style="border-bottom:groove;width: 70%; ">{{$data['father_name'] ?? ''}}</td>
            </tr>

            <tr>
                <td>Mother's Name ;</td>
                <td style="border-bottom:groove; width: 70%;">{{$data['mother_name'] ?? ''}}</td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td style="width: 30%;">Occupation of Father</td>
                <td style=" border-bottom: groove; width: 34%;">{{$data['father_Signature'] ?? ''}}</td>
                <td>Date of Birth</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px; "></td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>

                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px; "></td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>

                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px; "></td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
            </tr>
        </table>

        <table>
            <tr>
                <td>Gender</td>
                <td style="width: 10%;"></td>
                <td>Male:</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td> &nbsp; Female:</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td style="width: 10%;"></td>
                <td>Category: Gen</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td>  &nbsp;SC</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td>  &nbsp;ST</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td>  &nbsp;OBC</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td>  &nbsp;PH</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
            </tr>
        </table>




        <table style="width: 100%;">
            <tr>
                <td>Corresponding Address :</td>
                <td style="border-bottom:groove; width: 70%;">{{$data['guardian_address'] ?? ''}}</td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td>City</td>
                <th style="border-bottom:groove; width: 30%;"></th>
                <td>Distt</td>
                <th style="border-bottom:groove; width: 25%;"></th>
                <td>Pin Code</td>
                <th style="border-bottom:groove; width: 26%;"></th>
            </tr>
        </table>

        <table>
            <tr>
                <td>Phone
                    <th style="border-bottom:groove; width: 193px;">{{ $data ['mobile'] ?? ''}}</th>
                </td>
                <td>Mobile
                    <th style="border-bottom:groove; width: 193px;">{{ $data ['guardian_mobile'] ?? ''}}</th>
                </td>
                <td>Fax
                    <th style="border-bottom:groove; width: 202px;"></th>
                </td>
            </tr>
        </table>


        <table style="width: 100%;">
            <tr>
                <td>Corresponding Address ;</td>
                <td style="border-bottom:groove; width: 70%;">{{$data['guardian_address'] ?? ''}}</td>
            </tr>
        </table>


        <table style="width: 100%;">
            <tr>
                <td>City</td>
                <th style="border-bottom:groove; width: 30%;"></th>
                <td>Distt</td>
                <th style="border-bottom:groove; width: 25%;"></th>
                <td>Pin Code</td>
                <th style="border-bottom:groove; width: 26%;"></th>
            </tr>
        </table>

        <table>
            <tr>
                <td>Phone
                    <th style="border-bottom:groove; width: 193px;"> {{ $data ['mobile'] ?? ''}}</th>
                </td>
                <td>Mobile
                    <th style="border-bottom:groove; width: 193px;"></th>
                </td>
                <td>Fax
                    <th style="border-bottom:groove; width: 202px;"></th>
                </td>
            </tr>
        </table>
         <br>
         <table style="width: 100%;">
            <tr>
                <td>Hobbies (i)</td>
                <td style="border-bottom:groove; width: 42%;"></td>
                <td>(ii)</td>
                <td style="border-bottom:groove; width: 20%;"></td>
                <td>(iii)</td>
                <td style="border-bottom:groove; width: 20%;"></td>
            </tr>
        </table>

        <table>
            <tr>
                <td>Marks Obtained</td>
                <td style="border-bottom:groove; width: 90px;"></td>
                <td>% (X Board)</td>
                <td style="width: 10%;"></td>
                <td>Marks Obtained</td>
                <td style="border-bottom:groove; width: 90px;"></td>
                <td>% (XII Board)</td>
            </tr>
        </table>

        

        <table>
            <tr>
                <td>CBSE:</td>
                <td  style=" border: 1px solid rgb(0, 0, 0); width: 20px;" ></td>
                <td style="width: 1px;"></td>
                <td>RAJ Board:</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td style="width: 1px;"></td>
                <td>Any Other:</td>
                
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td style="width: 10%;"></td>
                <td>CBSE:</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td style="width: 1px;"></td>
                <td>RAJ Board:</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
                <td style="width: 1px;"></td>
                <td>Any Other:</td>
                <td style=" border: 1px solid rgb(0, 0, 0); width: 20px;"></td>
            </tr>
        </table>

        <table>
            <tr>
                <td>
                    <i>(Pinnar Attach a Photocopy of the Mark Sheet)</i>
                </td>
            </tr>
        </table>

        <br>
        <br>

        <table>
            <tr>
                <td>Date</td>
                <th style="border-bottom:groove; width: 193px;">{{ $data ['date'] ?? ''}}</th>
                <td>
                    <table style="text-align: end; position: absolute; text-align: center; margin-left: 40%;">
                        <tr>
                            <td>Sincerely <br>(Signature of Stalenti)</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Place</td>
                <th style="border-bottom:groove; width: 193px;"></th>
            </tr>
        </table>
       <br>

        <table style="font-size: 100%;">
            <tr>
                <td >&nbsp; &nbsp; &nbsp; &nbsp;  I lawfully declare that I will follow the rules and regulations of the Hostel. In case of the violation of <br> these rules and regulations, the Hostel Management is free to take action against me.</td>
            </tr>
        </table>
        <br>
        <table style="width: 100%;">
            <tr>
                @if(!empty($data['Student_Signature_img']))
                        <img src="{{ env('IMAGE_SHOW_PATH').'hostel/Student_Signature_img/'.$data['Student_Signature_img'] }}" width="80px" height="80px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/no_image.png' }}'" >
                         @else
                        <td>Signature of the Student</td>
                        @endif
                <td></td>
                <td> 
                 @if(!empty($data['father_Signature']))
                        <img src="{{ env('IMAGE_SHOW_PATH').'hostel/father_Signature/'.$data['father_Signature'] }}" width="80px" height="80px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/no_image.png' }}'" >
                         @else
                        <td>Signature of the Student's Father</td>
                        @endif
                </td>
                <td></td>
                <td>
                 @if(!empty($data['guardian_Signature']))
                        <img src="{{ env('IMAGE_SHOW_PATH').'hostel/guardian_Signature/'.$data['guardian_Signature'] }}" width="80px" height="80px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/no_image.png' }}'" >
                         @else
                        <td>Signature of the Hostel Superintendent</td>
                        @endif
                </td>
            </tr>
        </table>

    </form>
  
</body>
</html>