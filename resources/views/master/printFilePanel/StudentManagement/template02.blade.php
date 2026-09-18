@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRATION FORM</title>
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
        top: 136px;
       width: 100%;
       display: flex;
       align-items: center;
       justify-content: center;
       height: 100%;
       right: 0;
   }
   
   .backhround_img{
       opacity: 0.3;
       width: 46%;
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
        
        .fontweight{
                font-weight: 600;
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
      <p ><b >Address </b> {{$getSetting['address'] ?? ''}}</p>
      <p ><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}</p>
    </td>
      <td width='25%'>
          <!-- @if(!empty($data['image']))
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/user_image.jpg' }}'">
          @else
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/user_image.jpg' }}'">
          @endif -->
      </td>
      </tr>
      <tr>
      <td colspan='3' style="font-size:13px;text-align:center;"><span><strong>REGISTRATION FORM</strong></span></td>
    </tr>

  </tbody>
 
  </table> 
 
<table >

<thead>

<tr>
<table style='margin-top: 5px ;border-top:3px solid #6639b5'>

<thead>
<tr>
<td width='15%' class='ltr fontweight'>Student's Name:</td>

<td  width='85%'class='' style='border-bottom:1px dotted black'>{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>
    </tr>    
<tr>
<td width='15%' class='ltr fontweight'>Father's Name:</td>

<td  width='85%' class='' style='border-bottom:1px dotted black'>{{ $data['father_name'] ?? '-' }}</td>
    </tr>    
<tr>
<td width='15%' class='ltr fontweight'>Father's Mobile:</td>

<td  width='85%' class='' style='border-bottom:1px dotted black'>{{ $data['mobile'] ?? '-' }}</td>
    </tr>    
<tr>
<td width='15%' class='ltr fontweight'>Mother's Name:</td>

<td  width='85%' class='' style='border-bottom:1px dotted black'>{{ $data['mother_name'] ?? '-' }}</td>
    </tr>    
</thead>

    </table>


    </tr>



    <tr>
<table >
<div class="img_background_fixed">
                <div class="img_absolute">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmanisoft_logo.png' }}'" alt="" class="backhround_img">
                </div>
<thead>
<tr>
<td width='15%' class='ltr fontweight'>Birth Date:</td>

<td  width='20%'class='' style='border-bottom:1px dotted black'>{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>
   
<td width='15%' class='rtr fontweight'>Gender:</td>

<td  width='50%' class='ltr' style='border-bottom:1px dotted black'>  {{ $data['gender'] ['name'] ?? '-' }}</td>
<!--<td  width='50%' class='ltr' style='border-bottom:0px dotted black'>  <input type='radio' name='gender'>Male &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='gender'>Female</td>-->
    </tr>    
   
</thead>
</div>
    </table>


    </tr>


    <tr>


    <fieldset style='border:1px dashed; margin-top:15px'>
   <legend style='font-weight:600'>Present Adddress</legend>
   <table >

<thead>
<tr>
<td width='10%' class='ltr fontweight'>Division:</td>

<td  width='40%'class='rtr' style='border-bottom:1px dotted black'></td>
   
<td width='10%' class='rtr fontweight'>District:</td>

<td  width='40%'class='rtr' style='border-bottom:1px dotted black'></td>
  </tr>    
<tr>
<table >

<thead>
<tr>
<td width='10%' class='ltr fontweight'>Address:</td>

<td  width='90%'class='ltr' style='border-bottom:1px dotted black'>{{ $data['address'] ?? '-' }}</td>
   

  </tr>    

   
</thead>

    </table>
   

  </tr>    
   
</thead>

    </table>

   
</fieldset>
  
    </tr>




    <tr>


<fieldset style='border:1px dashed; margin-top:15px'>
   <legend style='font-weight:600'>Permanent Adddress</legend>
   <table >

<thead>
<tr>
<td width='10%' class='ltr fontweight'>Division:</td>

<td  width='40%'class='rtr' style='border-bottom:1px dotted black'></td>
   
<td width='10%' class='rtr fontweight'>District:</td>

<td  width='40%'class='rtr' style='border-bottom:1px dotted black'></td>
  </tr>    
<tr>
<table >

<thead>
<tr>
<td width='10%' class='ltr fontweight'>Address:</td>

<td  width='90%'class='rtr' style='border-bottom:1px dotted black'></td>
   

  </tr>    

   
</thead>

    </table>
   

  </tr>    
   
</thead>

    </table>

   
</fieldset>
  
    </tr>

    <tr>
<table  style=' margin-top:15px' >

<thead>
<tr>
<td width='10%' class='ltr fontweight'>Religion:</td>

<td  width='40%'class='rtr' style='border-bottom:1px dotted black'></td>
   
<td width='10%' class='ltr fontweight'>Nationality:</td>
<td  width='40%'class='rtr' style='border-bottom:1px dotted black'></td>
   </tr>    
<tr>
<td width='10%' class='ltr fontweight'>Mobile:</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{ $data['mobile'] ?? '-' }}</td>
   
<td width='10%' class='ltr fontweight'>Email:</td>
<td  width='40%'class='' style='border-bottom:1px dotted black'>{{ $data['email'] ?? '-' }}</td>
   </tr>    
<tr>
<td width='10%' class='ltr fontweight'>Class:</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{ $data['ClassTypes']['name'] ?? '-' }}</td>
   
<td width='12%' class='ltr fontweight'>ID Number :</td>
<td  width='40%'class='' style='border-bottom:1px dotted black'>{{ $data['id_number'] ?? '-' }}</td>
   </tr>    
<tr>
<td width='15%' class='ltr fontweight'>Registration No. :</td>

<td  width='40%'class='' style='border-bottom:1px dotted black'>{{ $data['registration_no'] ?? '' }}</td>
   
   </tr>    
   
</thead>

    </table>


    </tr>
    </thead>
    </table>

<p style='text-align:center; line-height:20px;margin-top:35px'>

<b>DECLEARATION</b></br>
I hereby, declearing that i will obey all the rules and regulations of the institution and be fully responsible for violating the rules.

</p>

  <table style='margin-top: 15px; border-bottom:30px solid #6639b5;' >

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

