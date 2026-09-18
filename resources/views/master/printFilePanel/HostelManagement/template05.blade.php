@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Hostel Print</title>
    <style>
     
        body {
   font-family: Arial, sans-serif;
      font-size:12px;
      max-width:750px;
      margin: 10px auto 
     
   /* border: 0.5px solid; */
   }
   .student_img {
   width: 80px; 
   height:100; 
   margin-top: 5%;
   margin-left:20%;
   padding-bottom: 10px;
       
   }
   
   .row{
       margin-right: 0px;
   }
   .img_background_fixed{
     position: relative;
   }
   
   .img_absolute{
       position: absolute;
         top: 131px;
       width: 100%;
       display: flex;
       align-items: center;
       justify-content: center;
       height: 100%;
       right: 0;
   }
   
   .backhround_img{
       opacity: 0.3;
       width: 47%;
   }

   table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px;
        }
        .inner_table th{
            border: 1px solid #000;
            padding: 5px;
            /* background-color: #f2f2f2; */
        }
      
        .invoice-header {
            margin-bottom: 20px;
            text-align:'center'
        }
        .inner_table td{
            padding:5px
        }
        .ltr{
            text-align: left;
            border-right: none !important;
            padding:5px
        }
        .rtr{
            text-align:right;
        }

       #personal_detail th {
            border: 1px solid #000;
            text-align: left;
            padding: 8px;
            background:#dddddd
        }
       #personal_detail td {
            border: 1px solid #000;
            text-align: left;
            padding: 8px;
        }
        .img_bwm{
            width: 100px;
            text-align: right;
            height: 109px;
            padding: 3px;
            border: 2px dotted black;
            margin-bottom: -13px;
            margin-top: -17px;
        }
        .img_bwm2 {
    width: 100px;
    text-align: right;
    height: 109px;
    padding: 3px;
    border: 2px dotted black;
    margin-bottom: -13px;
        }
   </style>
</head>
<body class='page'>
<table style="background:#6639b5;color:white; padding: 30px;">
			<tbody >
					<tr>
      <td rowspan='2' width='25%'>
          <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 150px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" >
          </td>
      <td   width='50%' style="font-size:20px;text-align:center;"><span><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
      <td width='25%'> 
      </td>
    
   </tr>
	<tr style="text-align:center;">
      <td width='50%'  style="text-align:center;">
      <p ><b >Address </b> {{$getSetting['address'] ?? ''}} </p>
      <p ><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}</p>
    </td>
      <td width='25%'>
           @if(!empty($data['student_image']))
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['student_image'] ?? ''}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @else
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @endif 
      </td>
      </tr>
      <tr>
      <td colspan='3' style="font-size:13px;text-align:center;"><span><strong>HOSTEL FORM </strong></span></td>
    </tr>

  </tbody>
  </table> 
 
<table >

<thead>

<tr>
<table style='margin-top: 10px ;border-top:3px solid #6639b5'>

<thead>
<tr>
<td width='15%' class='ltr'>Student's Name:</td>

<td  width='85%'class='' style='border-bottom:1px dotted black'>{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>
    </tr>    

</thead>

    </table>


    </tr>

<tr>
<table  style=' margin-top:5px' >
<div class="img_background_fixed">
                <div class="img_absolute">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmanisoft_logo.png' }}'" alt="" class="backhround_img">
                </div>
<thead>
<tr>
<td width='10%' class='ltr'>Session:</td>

<td  width='40%'class='ltr' style='border-bottom:1px dotted black'> {{$data->from_year ?? ''}}-{{$data->to_year ?? ''}}</td>
   
<td width='10%' class='ltr'>D.O.B:</td>
<td  width='40%'class='ltr' style='border-bottom:1px dotted black'>{{$data->dob ?? ''}}</td>
   </tr>    
<tr>
<td width='10%' class='ltr'>College:</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->college ?? ''}}</td>
   
<td width='10%' class='ltr'>Course:</td>
<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->Course ?? ''}}</td>
   </tr>    
<tr>
<td width='10%' class='ltr'>Email:</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->email ?? ''}}</td>
   
<td width='10%' class='ltr'>Mobile:</td>
<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->mobile ?? ''}}</td>

   </tr>    
<tr>
   <td width='10%' class='ltr'>Aadhar:</td>
   
<td  width='40%'class='' style='border-bottom:1px dotted black'>{{ $data['aadhaar'] ?? '-' }}</td>

<td width='10%' class='ltr'>Admission:</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{ $data['admissionNo'] ?? '' }}</td>

   </tr> 
 
</thead>
</div>
    </table>


    </tr>

<tr>
<table  style=' margin-top:7px' >

<thead>
<tr>
<td width='15%' class='ltr'><b>Parents Details:-</b></td>

<td  width='40%'class='rtr' style=''></td>
   
<td width='10%' class='ltr'></td>
<td  width='40%'class='rtr' style=''></td>
   </tr>    
<tr>
<td width='10%' class='ltr'>Father's Name:</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->father_name ?? ''}}</td>
   
<td width='10%' class='ltr'>Contact No:</td>
<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->f_mobile ?? ''}}</td>
   </tr>    
<tr>
<td width='10%' class='ltr'>Mother's Name:</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->mother_name ?? ''}}</td>
   
<td width='10%' class='ltr'>Contact No:</td>
<td  width='40%'class='' style='border-bottom:1px dotted black'>{{$data->mothers_mobile ?? ''}}</td>

   </tr>    

 
</thead>
</div>
    </table>


    </tr>
<tr>
<table style='margin-top: 7px ;'>
<thead>
<tr>
<td width='100%' class='ltr'><b>Local Guardian Details:-</b> </td>
    </tr>    

</thead>
</table>
</tr>    
<tr>
<table  style=' margin-top:7px' >

<thead>
    
<tr>
<td width='10%' class='ltr'>Guardian Name:</td>

<td  width='35%'class='' style='border-bottom:1px dotted black'>{{$data->guardian_name ?? ''}}</td>
   
<td width='10%' class='ltr'>Guardian Mobile:</td>
<td  width='35%'class='' style='border-bottom:1px dotted black'>{{$data->guardian_mobile ?? ''}}</td>
   </tr>    
<tr>
<td width='17%' class='ltr'>Guardian Telephone:</td>

<td  width='35%'class='' style='border-bottom:1px dotted black'>{{$data->guardian_tel ?? ''}}</td>
   
<td width='17%' class='ltr'>Guardian Whatsapp:</td>
<td  width='35%'class='' style='border-bottom:1px dotted black'>{{$data->guardian_whatsapp ?? ''}}</td>

   </tr>    
<tr>
<td width='17%' class='ltr'>Guardian Address:</td>

<td  width='35%'class='' style='border-bottom:1px dotted black'>{{$data->guardian_address ?? ''}}</td>
   
<td width='17%' class='ltr'></td>
<td  width='35%'class='' style='border-bottom:0px dotted black'></td>

   </tr>    

 
</thead>
</div>
    </table>


    </tr>


<tr>
<table style='margin-top: 7px ;'>

<thead>
<tr>
<td width='100%' class='ltr'><b>Hostel room preference</b> </td>
    </tr>    

</thead>

</table>
</tr>
    
    
<tr>
<table style='margin-top: 7px ;'>

<thead>
<tr>
<td width='15%' class='ltr'>Hostel Fees:</td>

<td  width='25%'class='' style='border-bottom:1px dotted black'>{{$data->hostel_fees ?? ''}}</td>
<td  width='75%'class='' ></td>
    </tr>    
<tr>
<td width='15%' class='ltr'>Duration Of stay:</td>

<td  width='25%'class='' style='border-bottom:1px dotted black'>{{$data->duration_Of_stay ?? ''}}</td>
<td  width='75%'class='' ></td>
    </tr>    
<tr>
<td width='15%' class='ltr'>Bed No.:</td>

<td  width='25%'class='' style='border-bottom:1px dotted black'>{{$data->bed_name ?? ''}}</td>
<td  width='75%'class='' ></td>
    </tr>    

</thead>

    </table>


    </tr>

<tr>
<table style='margin-top: 10px ;'>
<thead>
<tr>
<td width='100%' class='ltr'><b>Details Of Persons who will contact the student</b> </td>
    </tr>    

</thead>
</table>
</tr>
<tr>
<table style='margin-top: 10px;text-align: center;'>
<thead>
<tr>
        <td width='' class=''>
            <label><b>Father's Photo </b></label>
        </td>
        <td width='' class=''>
             <label><b>Father's Photo </b></label>
        </td>
        <td width='' class=''>
            <b>Mother's Photo </b>
        </td>
</tr>    
<tr>
        <td width='' class=''>
                 @if(!empty($data['student_image']))
                  <img class="img_bwm2" src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['student_image'] ?? ''}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                  @else
                  <img class="img_bwm2" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                  @endif    
        </td>
        <td width='' class=''>
             @if(!empty($data['student_image']))
                  <img class="img_bwm2" src="{{ env('IMAGE_SHOW_PATH').'father_photo/'.$data['father_img'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                  @else
                  <img class="img_bwm2" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
             @endif
        </td>
        <td width='' class=''>
             @if(!empty($data['student_image']))
                  <img class="img_bwm2" src="{{ env('IMAGE_SHOW_PATH').'mother_photo/'.$data['mother_img'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                  @else
                  <img class="img_bwm2" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
             @endif
        </td>
</tr>    
<tr>
        <td width='' class=''>
            @if(!empty($data['Student_Signature_img']))
            <img src="{{ env('IMAGE_SHOW_PATH').'hostel/Student_Signature_img/'.$data['Student_Signature_img'] }}" width="70px" height="80px"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/no_image.png' }}'" style="margin-bottom: -21px;">
             @else
            <p style="padding-top:25px;">No Signature</p>
            @endif
            <p style="font-weight: 600;border-top: 1px solid;width: 103px;margin-left: 28%;">Signature</p>
        </td>
        <td width='' class=''>
            @if(!empty($data['father_Signature']))
            <img src="{{ env('IMAGE_SHOW_PATH').'hostel/father_Signature/'.$data['father_Signature'] }}" width="80px" height="70px"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/no_image.png' }}'" style="margin-bottom: -21px;">
             @else
            <p style="padding-top:25px;">No Signature</p>
            @endif
            <p style="font-weight: 600;border-top: 1px solid;width: 103px;margin-left: 28%;">Signature</p>
                 
        </td>
        <td width='' class=''>
           @if(!empty($data['mother_Signature']))
            <img src="{{ env('IMAGE_SHOW_PATH').'hostel/mother_Signature/'.$data['mother_Signature'] }}" width="80px" height="70px"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/no_image.png' }}'" style="margin-bottom: -21px;">
           @else
            <p style="padding-top:25px;">No Signature</p>
            @endif

            <p style="font-weight: 600;border-top: 1px solid;width: 103px;margin-left: 28%;">Signature</p>
         </td>
</tr>    

</thead>
</table>
</tr>

</thead>
</table>


  <table style='margin-top: 0px; border-bottom:30px solid #6639b5;' >

    <tfoot style='border:1px solid black;padding-bottom:10px'>
            <tr>
            <td style="text-align: center;"></td>
                <td style="text-align: right">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" style="height:90px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'">

                </td>
                
            </tr>
            <tr>
            <td style="text-align: left;">&nbsp;&nbsp;&nbsp;Signature</td>
                <td style="text-align: right;padding:10px">
            Seal & Sign    
            </td>
                
            </tr>
           
           
           
        </tfoot>
    </table>


<!-- <p style='text-align:center; line-height:20px;margin-top:35px'>

Generated By Rusoft</br>
for more details, log on to <a href='https://school.rusoft.in'>www.school.rusoft.in</a>

</p> -->

</body>
</html>

