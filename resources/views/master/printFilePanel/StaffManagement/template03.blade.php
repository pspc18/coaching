@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student admission Print</title>
    <style>
     
        body {
   font-family: Arial, sans-serif;
      font-size:12px;
      max-width:750px;
      margin: 10px auto; 
     
    border: 0.5px solid; 
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
      
        .join_letter {
            font-family: emoji;
            font-weight: bold;
            font-size: 35px;
            text-align: center;
        }
        
        .background_foother{
            margin-top: 15px;        
            background-image: url(/public/images/default/joining_letter_print.png);
            background-size: 100% 100%;
            background-repeat: no-repeat;
            width: 100%;
            color: white;
        }
        .line_height{
            padding: 14px 0px 0px 5px;
        }
   </style>
</head>
<body class='page'>
<table style="background:#d1052a;color:white; padding: 30px;">
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
          
      </td>
      </tr>
      <tr>
      <td colspan='3' style="font-size:13px;text-align:center;"></td>
    </tr>

  </tbody>
  </table> 
 
<table >

<thead>
<tr>
<table style='margin-top: 5px;border-top: 10px solid #d1052a;'>
<thead>
    
<tr>
<td width='100%' class="join_letter">Joining Letter</td>
</tr>    
  
</thead>
</table>
</tr>
    
    
    
<tr>
<table style='margin-top: 5px ;'>

<thead>
<tr>
<td width='15%' class='ltr'><p><b>To,</b></p> </td>
<td  width='25%'class='' ></td>
<td  width='85%'class='' ></td>
</tr>  
<tr>
<td width='15%' class='ltr line_height'>Name :- </td>
<td  width='25%'class='line_height' style='border-bottom:1px dotted black'>{{$data['first_name'] ?? ''}} {{$data['last_name'] ?? ''}}</td>
<td  width='85%'class='line_height' ></td>
</tr>  

<tr>
<td width='15%' class='ltr line_height'>Mobile  :- </td>
<td  width='25%'class='line_height' style='border-bottom:1px dotted black'> {{$data['mobile'] ?? ''}} </td>
<td  width='85%'class='line_height' ></td>
</tr> 

<tr>
<td width='15%' class='ltr line_height'>Email :- </td>
<td  width='25%'class='line_height' style='border-bottom:1px dotted black'> {{$data['email'] ?? ''}}</td>
<td  width='85%'class='line_height' ></td>
</tr>

<tr>
<td width='15%' class='ltr line_height'>DOB :- </td>
<td  width='25%'class='line_height' style='border-bottom:1px dotted black'> {{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>
<td  width='85%'class='line_height' ></td>
</tr> 

</thead>
</table>
</tr>
<tr>
<table style='margin-top: 5px ;'>

<thead>


<tr>
<td width='15%' class='ltr line_height'>Address :- </td>
<td  width='55%'class='line_height' style='border-bottom:1px dotted black'> {{$data['address'] ?? ''}}</td>
<td  width='85%'class='line_height' ></td>
</tr>  

</thead>
</table>
</tr>


<tr>
<table style='margin-top: 5px ;'>
<thead>
    
<tr>
<td width='100%' class='ltr'><p>Sub: JOINING LETTER</p></td>
</tr>  
<tr>
<td width='100%' class='ltr'>Dear Sir/Ma'am,</td>
</tr>  
<tr>
<td width='100%' class='ltr'>
<!--<p>I am immensely pleased to inform you that I accept the offer and acknowledge the same.
I am ready to join as believing in me and offering me this position.
I assure to work with sincerity and dedication. <b>Teacher</b> (Job Position) in your company on <b><u>{{date('d-m-Y', strtotime($data['joining_date'])) ?? '' }}</u></b>  (date of joining). 
I sincerely thank you for believing in me and offering me this position. I assure to work with sincerity and dedication.</p>-->

<p>I am immensely pleased to inform you that I accept the offer for the position of Teacher at your esteemed company. I acknowledge the same and confirm my readiness to join on {{date('d-m-Y', strtotime($data['joining_date'])) ?? '' }}.</p>

<p>I sincerely thank you for believing in me and offering me this opportunity. I assure you that I will work with sincerity and dedication.</p>


</td>
</tr>  
<tr>
<td width='100%' class='ltr'>
<p>I will be submitting all the required documents on my joining date. Should you require any further information, please do not hesitate to contact me.</p>
</td>
</tr>  
<tr>
<td width='100%' class='ltr'>
<p>Yours faithfully</p>
</td>
</tr>  

</thead>
</table>
</tr>

    </thead>
    </table>


  <table class="background_foother" >

    <tfoot style='padding-bottom:10px'>
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

