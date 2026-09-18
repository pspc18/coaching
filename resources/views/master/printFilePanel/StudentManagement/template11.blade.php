@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Student admission Print</title>
    <style>
     
        body {
    font-family: Arial, sans-serif;
    font-size: 15px;
    max-width: 990px;
    margin: 10px auto;
    color: black;
    font-weight: 700;
    background: #ebde37;
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
        .ctr{
            text-align: center;
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
    width: 46%;
    text-align: center;
    height: 124px;
    padding: 3px;
    border: 2px solid #0000007d;
        }
        .heading{
            font-size: 38px;text-align: center;font-weight: 900;
        }
        .name_border{
                border-bottom: 1px solid;
                margin-bottom: 0px;
                width: 84%;
                margin-left: 21px;
        }
        .place_border{
               border-bottom: 1px solid black;
              width: 73%;
        }
        .place_border2{
               border-bottom: 1px solid black;
        }
        
        .dob_listing{
            display: flex;
            align-items: center;
            margin-left: 30px;
        }
        .dob_listingfemale{
            display: flex;
            align-items: center;
            margin-left: 73px;
        }
        
        .flex_items_main{
            display: flex;
            align-items: center;
        }
        
        .dob_listing:first-child{
            margin-left: 0px;
        }
        .dob_listingfemale:first-child{
            margin-left: 0px;
        }
        
        .dob_listing p{
            border: 1px solid black;
            padding: 10px;
            border-radius: 4px;
            width: 10px;
            height: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .dob_listing3 p{
               border: 1px solid black;
                padding: 10px;
                border-radius: 3px;
                width: 52%;
                height: 131px;
                display: flex;
                margin: 0;
            }
     
     
           .dob_listingfemale p{
            border: 1px solid black;
            padding: 10px;
            border-radius: 4px;
            width: 10px;
            height: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        
        .formate_text{
            text-align:center;
        }
        .formate_text3{
            text-align:center;
        }
        
        @page {
    margin: 0;
}
         .text_shadow{
           text-shadow: 1px 3px #ebde37;
            color: white;
            font-family: sans-serif;
         }
   </style>
</head>
<body class='page'>
    
    
<table style=" padding: 30px;margin-bottom: 66px;margin-top: 20px;">
			<tbody >
					<tr>
      <td rowspan='12' width='100%' style="text-align: center;">
          <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 30%;height: 126px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" >
          </td>
     
    
   </tr>


  </tbody>
  </table> 
 
 
 <div style="background-color: white;padding: 22px;margin: 10px;margin-top: -17px;">
<table style="margin-top: -56px;">

<thead>

<tr>
     <td colspan='3' class="heading"><span class="text_shadow">ADMISSION FORM</span></td>
<table style='margin-top: 10px ;'>

<thead>
<tr>
<td width='15%' class='ltr'>Admission for:</td>
<td width='15%' class='ltr'>
  <div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p>
            @if($data->class_type_id == 1)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
  <p>Play Group</p>
    </div>
 </td>
<td width='15%' class='ltr'>
<div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p> 
               @if($data->class_type_id == 2)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
  <p>Nursary</p>
    </div>
</td>
<td width='15%' class='ltr'>
<div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p> 
               @if($data->class_type_id == 3)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
  <p>Junior KG</p>
    </div>
</td>
<td width='15%' class='ltr'>
<div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p> 
               @if($data->class_type_id == 4)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
  <p>Senior KG</p>
    </div>
</td>

</tr>   



<tr>
<td width='15%' class='ltr'></td>
<td width='15%' class='ltr'>
  <div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p> 
               @if($data->class_type_id == 5)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
  <p>1st</p>
    </div>
 </td>
<td width='15%' class='ltr'>
<div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p> 
               @if($data->class_type_id == 6)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
  <p>2nd</p>
    </div>
</td>
<td width='15%' class='ltr'>
<div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p> 
               @if($data->class_type_id == 7)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
  <p>3rd</p>
    </div>
</td>
<td width='15%' class='ltr'>
<div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p> 
               @if($data->class_type_id == 8)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
      
    </div>
    
  <p>4th</p>
    </div>
</td>

</tr> 
  
</thead>

</table>
</tr>
<tr>
<table style='margin-top: 3px ;'>
<thead>
<tr>
<td width='15%' class='ltr'>
     @if(!empty($data['father_img']))
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/father_image/'.$data['father_img'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @else
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @endif 
</td>
<td width='15%' class='ctr'>
     @if(!empty($data['mother_img']))
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/mother_image/'.$data['mother_img'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @else
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @endif 
</td>
<td width='15%' class='ctr'>
     @if(!empty($data['image']))
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @else
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @endif 
</td>
</tr> 
</thead>
</table>
</tr>
<tr>
<table style='margin-top: -5px ;'>
<thead>
<tr>
<td width='15%' class='ltr'>
     Name of the Child : 
</td>
<td width='15%' class='ctr'>
      <p class="name_border">{{ $data['first_name'] ?? '' }}</p>
    <small>First Name</small>
</td>
<td width='15%' class='ctr'>
     <p class="name_border">{{ $data['middle_name'] ?? '' }}</p>
   <small>Middle Name</small>
</td>
<td width='15%' class='ctr'>
     <p class="name_border">{{ $data['last_name'] ?? '' }}</p>
   <small>Last Name</small>
</td>

</tr> 

</thead>
</table>
</tr>
<tr>
<table style='margin-top: 10px ;'>
<thead>
<tr>
<td width='19%' class='ltr'>
    Date of Birth  : 
</td>
@php
    $day = date('d', strtotime($data['dob']));
    $month = date('m', strtotime($data['dob']));
    $year = date('Y', strtotime($data['dob']));
    $dayDigits = str_split($day, 1);
    $monthDigits = str_split($month, 1);
    $yearDigits = str_split($year, 1);
@endphp
<td width='55%' class='ltr'>
    <div class='flex_items_main'>
    <div class="dob_listing">
        <div class="formate_text">
            <p>{{ $dayDigits[0] }}</p>
            <small>D</small>
        </div>
        <div class="formate_text">
            <p>{{ $dayDigits[1] }}</p>
            <small>D</small>
        </div>
    </div>
    
    <div class="dob_listing">
        <div class="formate_text">
            <p>{{ $monthDigits[0] }}</p>
            <small>M</small>
        </div>
        <div class="formate_text">
            <p>{{ $monthDigits[1] }}</p>
            <small>M</small>
        </div>
    </div>
    
    <div class="dob_listing">
        <div class="formate_text">
            <p>{{ $yearDigits[0] }}</p>
            <small>Y</small>
        </div>
        <div class="formate_text">
            <p>{{ $yearDigits[1] }}</p>
            <small>Y</small>
        </div>
        <div class="formate_text">
            <p>{{ $yearDigits[2] }}</p>
            <small>Y</small>
        </div>
        <div class="formate_text">
            <p>{{ $yearDigits[3] }}</p>
            <small>Y</small>
        </div>
    </div>
    </div>
</td>




<td width='11%' class='ltr'>
   Gender  : 
</td>
<td width='25%' class='ltr'>
    <div class='flex_items_main'>
    <div class="dob_listing">
        <div class="formate_text">
            <p>
                @if($data->gender_id == 1)
                <i class="fa fa-check"></i>
                @endif
            </p>
            <small>Male</small>
        </div>
    </div>
    
  
    
    <div class="dob_listingfemale">
        <div class="formate_text">
            <p>
                @if($data->gender_id == 2)
                <i class="fa fa-check"></i>
                @endif
            </p>
            <small>Famale</small>
        </div>
    </div>
    </div>
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: -5px ;'>
<thead>
<tr>
<td width='18%' class='ltr'>
     Place of Birth : 
</td>
<td width='25%' class='ltr'>
     <p class="place_border">{{date('d-m-Y', strtotime($data['place_birth'])) ?? '-' }}</p>
</td>
<td width='9%' class='rtr'>
     
</td>
<td width='15%' class='rtr'>
    <p class=""></p>
</td>
<td width='13%' class='rtr'>
     Nationality : 
</td>
<td width='25%' class='ltr'>
    <p class="place_border">{{$data['nationality'] ?? '-'}}</p>
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: -5px ;'>
<thead>
<tr>
<td width='12%' class='ltr'>
    Height : 
</td>
<td width='25%' class='ltr'>
     <div class='flex_items_main'>
    <div class="dob_listing">
        <div class="formate_text">
            <p></p>
            <small>feet</small>
        </div>
       
    </div>
    
    <div class="dob_listing">
        <div class="formate_text">
            <p></p>
            <small>inches</small>
        </div>
        <div class="formate_text">
            <p></p>
            <small style="color: white;">M</small>
        </div>
    </div>
    
    </div>
</td>
<td width='12%' class='ltr'>
     Weight :
</td>
<td width='18%' class='rtr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
        <div class="formate_text">
            <p></p>
            
        </div>
        <div class="formate_text">
            <p></p>
            
        </div>
    </div>
  <p>Kgs</p>
    </div>
  
</td>
<td width='16%' class='rtr'>
     Blood Group : 
</td>
<td width='25%' class='ltr'>
    <p class="place_border">{{ $data['blood_group'] ?? '-' }}</p>
</td>

</tr> 
</thead>
</table>
</tr>


 <tr>
<table  style='margin-top: 10px ;'>
    
<thead>
<tr>
<td width='33%' class='ltr'>Previous Pre-School Attended:</td>

<td  width='45%'class='' style='border-bottom:1px solid black'>{{$data['previous_school_attended'] ?? '-'}}</td>
   
<td width='9%' class='ltr'>Class :</td>
<td  width='50%' class='ltr' style='border-bottom:1px solid black'>{{ $data['ClassTypes']['name'] ?? '-' }} </td>
    </tr>    
   
</thead>

    </table>


    </tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='23%' class='ltr'>Residential Address :</td>

<td  width='85%'class='' style='border-bottom:1px solid black'>{{$data['residential_address'] ?? '-'}}</td>
    </tr>    

</thead>
    </table>
</tr>
<tr>
<table style='margin-top: 30px ;'>
<thead>
<tr>
<td  width='100%'class='' style='border-bottom:1px solid black'></td>
    </tr>    

</thead>
    </table>
</tr>



<tr>
<table  style='margin-top: 15px ;'>
    
<thead>
<tr>
<td width='7%' class='ltr'>City :</td>

<td  width='25%'class='' style='border-bottom:1px solid black'>{{$data['city_name'] ?? ''}}</td>
   
<td width='11%' class='rtr'>State :</td>
<td  width='25%' class='ltr' style='border-bottom:1px solid black'> {{$data['state_name'] ?? ''}}</td>
  @php
            $digits = str_split($data->mobile);
            $pincodedigits = str_split($data->pincode);
            $mobile2digits = str_split($data->mobile_2);
            
            $emergencymobile = str_split($data->emergency_mobile);
            $emergencyphone = str_split($data->emergency_phone);
        @endphp
<td width='15%' class='' style="text-align: center;">Pin code :</td>
<td  width='50%' class='rtr'>
      <div class="dob_listing">
          
          @if(!empty($pincodedigits))
            @foreach($pincodedigits as $pincode)
                <div class="formate_text">
                    <p>{{ $pincode }}</p>
                </div>
            @endforeach
        @endif
    </div>
</td>

</tr>    
   
</thead>

    </table>


    </tr>
    
    
<tr>
<table  style='margin-top: 15px ;'>
    
<thead>
<tr>
<td width='15%' class='ltr'>Phone No. :</td>
<td  width='50%' class='rtr'>
      <div class="dob_listing">
      
        
        @if(!empty($digits))
            @foreach($digits as $number)
                <div class="formate_text">
                    <p>{{ $number }}</p>
                </div>
            @endforeach
        @endif
    </div>
</td>

<td width='15%' class='ltr'>Mobile No. :</td>
<td  width='50%' class='rtr'>
      <div class="dob_listing">
           @if(!empty($mobile2digits))
            @foreach($mobile2digits as $number2)
                <div class="formate_text">
                    <p>{{ $number2 }}</p>
                </div>
            @endforeach
        @endif
    </div>
</td>

</tr>    
   
</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 20px ;'>
<thead>
<tr>
<td width='100%' class='ltr'>Emergency Contact details :- </td>
</tr> 
</thead>
    </table>
</tr>

<tr>
<table style='margin-top: 20px ;'>
<thead>
<tr>
<td width='8%' class='ltr'>Name :</td>

<td  width='85%'class='' style='border-bottom:1px solid black'>{{$data['emergency_name'] ?? ''}}</td>
    </tr>    

</thead>
    </table>
</tr>

<tr>
<table  style='margin-top: 20px ;'>
    
<thead>
<tr>
<td width='15%' class='ltr'>Phone No. :</td>
<td  width='50%' class='rtr'>
      <div class="dob_listing">
          @if(!empty($emergencymobile))
            @foreach($emergencymobile as $emmobile)
                <div class="formate_text">
                    <p>{{ $emmobile }}</p>
                </div>
            @endforeach
        @endif
    </div>
</td>

<td width='15%' class='ltr'>Mobile No. :</td>
<td  width='50%' class='rtr'>
      <div class="dob_listing">
           @if(!empty($emergencyphone))
            @foreach($emergencyphone as $emphone)
                <div class="formate_text">
                    <p>{{ $emphone }}</p>
                </div>
            @endforeach
        @endif
      </div>
</td>

</tr>    
   
</thead>
    </table>
</tr>


    </thead>
    </table>


</div>









<br><br>

 <div style="background-color: white;padding: 22px;margin: 17px;margin-top: -10px;">
<table style="">

<thead>

<tr>
   
<table style='margin-top: -20px ;'>

<thead>
<tr>
<td width='35%' class='ltr'>Language/s Known by the child :</td>
<td width='16%' class='rtr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
                 @if($data->languag_child == 'English')
                    <i class="fa fa-check"></i>                
                 @endif
            </p>
            
        </div>
    </div>
  <p>English</p>
    </div>
  
</td>
<td width='15%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
                 @if($data->languag_child == 'Hindi')
                    <i class="fa fa-check"></i>                
                 @endif
            </p>
            
        </div>
    </div>
  <p>Hindi</p>
    </div>
  
</td>
<td width='23%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
                 @if($data->languag_child == 'Other')
                    <i class="fa fa-check"></i>                
                 @endif
            </p>
            
        </div>
    </div>
  <p class="ltr">Others, specify </p>
    </div>
  
</td>
<td width='18%' class='ltr' >
    <p style="border-bottom: 1px solid black;"></p>
</td>

</tr> 
</thead>
</table>
</tr>



<tr>
<table style='margin-top: 0px ;'>
<thead>
<tr>
<td  width='100%'class=''>
    Any allergy, disability or other physical/medicak conditions which your child has, or is at risk about which the
</td>
    </tr>    

</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 0px ;'>
<thead>
<tr>
<td width='35%' class='ltr'>Pre-School should be aware of :</td>

<td  width='85%'class='' style='border-bottom:1px solid black'>{{$data['emergency_name'] ?? ''}}</td>
    </tr>    

</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 32px ;'>
<thead>
<tr>
<td  width='100%'class='' style='border-bottom:1px solid black'></td>
    </tr>    

</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='41%' class='ltr'>Name of Family Physician/Pediatrician :</td>

<td  width='85%'class='' style='border-bottom:1px solid black'>{{$data['emergency_name'] ?? ''}}</td>
    </tr>    

</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='8%' class='ltr'>Mobile No. :</td>

<td  width='50%' class='rtr'>
      <div class="dob_listing">
        <div class="formate_text">
            <p>0</p>
        </div>
        <div class="formate_text">
            <p>2</p>
        </div>
        <div class="formate_text">
            <p>0</p>
        </div>
        <div class="formate_text">
            <p>2</p>
        </div>
        <div class="formate_text">
            <p>0</p>
        </div>
        <div class="formate_text">
            <p>2</p>
        </div>
        <div class="formate_text">
            <p>0</p>
        </div>
        <div class="formate_text">
            <p>2</p>
        </div>
        <div class="formate_text">
            <p>0</p>
        </div>
        <div class="formate_text">
            <p>2</p>
        </div>
    </div>
</td>
    </tr>    

</thead>
    </table>
</tr>
<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='8%' class='ltr'><b>Parent's Information :</b></td>

    </tr>    

</thead>
    </table>
</tr>




<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='14%' class='ltr'>
     Father's Name : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_name'] ?? ''}}
</td>

<td width='15%' class='rtr'>
     Mother's Name : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_name'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='50%' class='ltr' style="border-bottom: 1px solid black;"></td>

<td width='4%'></td>
<td width='50%' class='ltr' style="border-bottom: 1px solid black;"></td>

</tr> 
</thead>
</table>
</tr>

<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='17%' class='ltr'>
     Father's Aadhaar : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_aadhaar'] ?? ''}}
</td>

<td width='16%' class='rtr'>
     Mother's Aadhaar : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_aadhaar'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='3%' class='ltr'>
     Education : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_education'] ?? ''}}
</td>

<td width='9%' class='rtr'>
     Education : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
     {{$data['mother_education'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='22%' class='ltr'>
     Occuoation (specify) : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_occupation'] ?? ''}}
</td>

<td width='22%' class='rtr'>
     Occuoation (specify) :
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_occupation'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>



<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='5%' class='ltr'>
     Designation : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_designation'] ?? ''}}
</td>

<td width='9%' class='rtr'>
     Designation : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_designation'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='5%' class='ltr'>
     Organization : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_organization'] ?? ''}}
</td>

<td width='9%' class='rtr'>
     Organization : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_organization'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='14%' class='ltr'>
     Office Address  : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_address'] ?? ''}}
</td>

<td width='14%' class='rtr'>
      Office Address  : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_address'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='50%' class='ltr' style="border-bottom: 1px solid black;"></td>

<td width='4%'></td>
<td width='50%' class='ltr' style="border-bottom: 1px solid black;"></td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='19%' class='ltr'>
     Phone No. (Office)  : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_phone'] ?? ''}}
</td>

<td width='19%' class='rtr'>
      Phone No. (Office)  : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_phone'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>
 @php
    $fathermobiledigits = str_split($data->father_mobile);
    $mothermobiledigits = str_split($data->mothers_mobile);
@endphp

<tr>
<table style='margin-top: 10px ;'>
<thead>
<tr>
<td width='8%' class='ltr'>
     Mobile: 
</td>
<td  width='25%' class='ltr'>
      <div class="dob_listing">
          @if(!empty($fathermobiledigits))
            @foreach($fathermobiledigits as $fathermob)
                <div class="formate_text">
                    <p>{{ $fathermob }}</p>
                </div>
            @endforeach
            @endif
       
    </div>
</td>

<td width='19%' class='rtr'>
      Mobile: 
</td>


<td  width='25%' class='rtr'>
      <div class="dob_listing">
           @if(!empty($mothermobiledigits))
            @foreach($mothermobiledigits as $mothermob)
                <div class="formate_text">
                    <p>{{ $mothermob }}</p>
                </div>
            @endforeach
            @endif
    </div>
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 10px ;'>
<thead>
<tr>
<td width='9%' class='ltr'>
     E-mail id : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['father_email'] ?? ''}}
</td>

<td width='9%' class='rtr'>
     E-mail id : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['mother_email'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>




<tr>
<table style='margin-top: 16px ;'>
<thead>
<tr>
<td width='100%' class='ltr'>Guardian's Information :- </td>
</tr> 
</thead>
    </table>
</tr>

<tr>
<table style='margin-top: 10px ;'>
<thead>
<tr>
<td width='8%' class='ltr'>Name :</td>

<td  width='85%'class='' style='border-bottom:1px solid black'>{{$data['guardian_name'] ?? ''}}</td>
    </tr>    

</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 20px ;'>
<thead>
<tr>
<td width='11%' class='ltr'>Address :</td>

<td  width='85%'class='' style='border-bottom:1px solid black'>
    {{$data['guardian_address'] ?? ''}}
</td>
    </tr>    

</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 20px ;'>
<thead>
<tr>
<td  width='100%'class='' style='border-bottom:1px solid black'></td>
    </tr>    

</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='12%' class='ltr'>
     Phone (Home) : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
     {{$data['guardian_phone'] ?? ''}}
</td>

<td width='9%' class='rtr'>
     (Office) : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
     {{$data['guardian_phone_office'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>



<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='13%' class='ltr'>
     E-mail id : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['guardian_email'] ?? ''}}
</td>
    @php
        $guardianmobile = str_split($data->guardian_mobile);
    @endphp
<td width='15%' class='rtr'>
     Mobile No. : 
</td>

<td  width='25%' class='rtr'>
      <div class="dob_listing">
          @if(!empty($guardianmobile))
            @foreach($guardianmobile as $guardmob)
                <div class="formate_text">
                    <p>{{ $guardmob }}</p>
                </div>
            @endforeach
          @endif
    </div>
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='25%' class='ltr'>
     Relation with the child  : 
</td>
<td width='85%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['guardian_relation_child'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>








    </thead>
    </table>


</div>








<br><br>
 <div style="background-color: white;padding: 22px;margin: 17px;margin-top: -10px;">
<table style="">

<thead>

<tr>
<table style='margin-top: 20px;'>
<thead>
<tr>
<td width='8%' class='ltr'><b>Siblings Information :</b></td>

    </tr>    

</thead>
    </table>
</tr>




<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='19%' class='ltr'>
     Brother's Name : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['brother_name'] ?? ''}}
</td>
<td width='10%' class='ltr'>
     School : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['brother_school'] ?? ''}}
</td>

<td width='5%' class='rtr'>
     Std : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['brother_std'] ?? ''}}
</td>

</tr>
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='18%' class='ltr'>
     Sister's Name : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['sister_name'] ?? ''}}
</td>
<td width='10%' class='ltr'>
     School : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['sister_school'] ?? ''}}
</td>

<td width='5%' class='rtr'>
     Std : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
    {{$data['sister_std'] ?? ''}}
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>

<td width='100%' class='ltr' style="border-bottom: 1px dotted black;">
</td>

</tr> 
</thead>
</table>
</tr>





<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='100%' class='ltr'> How did you get to know of "Shanti Juniors" ? </td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='16%' class='rtr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
                @if($data->how_did_get_shanti_junior == 1)
                <i class="fa fa-check"></i>
                @endif
                </p>
            
        </div>
    </div>
  <p>Newspaper Ad</p>
    </div>
  
</td>
<td width='11%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>@if($data->how_did_get_shanti_junior == 2)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
    </div>
  <p>Banner</p>
    </div>
  
</td>
<td width='11%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>@if($data->how_did_get_shanti_junior == 3)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
    </div>
  <p>Mailer</p>
    </div>
  
</td>
<td width='11%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
            @if($data->how_did_get_shanti_junior == 4)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
    </div>
  <p>Tv</p>
    </div>
  
</td>
<td width='15%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
            @if($data->how_did_get_shanti_junior == 5)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
    </div>
  <p>Friends / Sibling</p>
    </div>
  
</td>


</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>

<td width='15%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
            @if($data->how_did_get_shanti_junior == 6)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
    </div>
  <p>Word of mouth</p>
    </div>
  
</td>

<td width='14%' class='ltr'>
     <div class='flex_items_main'>
     <div class="dob_listing">
      
        <div class="formate_text">
            <p>
                @if($data->how_did_get_shanti_junior == 7)
                <i class="fa fa-check"></i>
                @endif
            </p>
            
        </div>
    </div>
  <p class="ltr">Others, specify </p>
    </div>
  
</td>
<td width='18%' class='ltr' >
    <p style="border-bottom: 1px solid black;"></p>
</td>

<td width='15%' class='ltr'></td>


</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>

<td width='100%' class='ctr'>
       <u>Parents' Undertaking</u>
</td>



</tr> 
</thead>
</table>
</tr>



<tr>
<table style='margin-top: 10px ;'>
<thead>
<tr>
<td width='75%' class='ltr'>
     To, 
</td>


<td width='9%' class='rtr'>
     Date : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
</td>

</tr> 
</thead>
</table>
</tr>



<tr>
<table style='margin-top: 35px ;'>
<thead>
<tr>
<td width='75%' class='ltr'>
     <div class="dob_listing3">
        <div class="formate_text3">
            <p></p>
            
        </div>
    </div> 
   <small>(A Franchisee of Shanti Educational Initiatives Ltd.)</small>
</td>


<td width='9%' class='rtr'>
      
</td>
<td width='25%' class='ltr' style="">
</td>

</tr> 
</thead>
</table>
</tr>




<tr>
<table style='margin-top: 6px ;'>
<thead>
<tr>
<td width='100%' class='ltr'><u>Sub: All or any field trips or excursions</u></td>
</tr> 
</thead>
    </table>
</tr>

<tr>
<table style='margin-top: 20px ;'>
<thead>
<tr>
<td width='3%' class='ltr'>I,</td>
<td  width='25%'class='' style='border-bottom:1px solid black'></td>

<td width='43%' class='ltr'>Father / Mother / Guardian of Miss/Master</td>
<td  width='85%'class='' style='border-bottom:1px solid black'>,</td>
</tr>    

</thead>
    </table>
</tr>
<tr>
<table style='margin-top: 8px ;'>
<thead>
<tr>
<td width='100%' class='ltr'>enrolled at your "Shanti Junior" centre, hereby willingly permit my son/daughter
to go for all trips $ excursions required for his/her studies.</td>

</tr>    

</thead>
    </table>
</tr>
<tr>
<table style='margin-top: 25px ;'>
<thead>
<tr>
<td width='100%' class='ltr'>
I understand that the teachers/escorts accompanying the students implicitly undertake to do everything humanly 
possible for the well being and the safety of the children inside and/or outside the centre, and act in good faith towards this end.
</td>

</tr>    

</thead>
    </table>
</tr>
<tr>
<table style='margin-top: 25px ;'>
<thead>
<tr>
<td width='100%' class='ltr'>
However, I also realize that no one can be held liable for mishaps or accidents occurring due to circumstances beyand their control,
acts of God or Nature. In that event, I hereby absolve the school and their representatives from any liability. and waive all claims arising thereafter.
</td>

</tr>    

</thead>
    </table>
</tr>



<tr>
<table style='margin-top: -3px;margin-bottom: -16px;'>
<thead>
<tr>
<td width='13%' class='ltr'>
     
</td>


<td width='15%' class='rtr'>
      
    @if(!empty($data['father_Signature']))
          <img class="" src="{{ env('IMAGE_SHOW_PATH').'/father_Signature/'.$data['father_Signature'] ?? '' }}" style="width: 34%;margin-bottom: -17px;height: 76px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @else
          <img class="" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" style="width: 34%;margin-bottom: -17px;height: 76px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @endif
</td>


</tr> 
</thead>
</table>
</tr>
<tr>
<table style='margin-top: 15px;'>
<thead>
<tr>
<td width='13%' class='ltr'>
     Yours truly,
</td>


<td width='15%' class='rtr'>
      
     <p>Parent's/Guardian's Signature</p> 
</td>


</tr> 
</thead>
</table>
</tr>


</thead>
</table>

</div>







<br><br>
 <div style="background-color: white;padding: 22px;margin: 17px;margin-top: -10px;">
<table style="">

<thead>

<tr>
<table style='margin-top: -13px;'>
<thead>
<tr>
<td width='100%' class='ltr'><b>Terms & Conditions</b></td>

    </tr>    

</thead>
    </table>
</tr>

<ul>
  <li>I hereby provide my consent to utilize my child's pictures, artwork and creations done during activity time for marketing purpose.</li><br>
  
  <li>I here by acknowledge to receive all promotional and transactional updates through E-mails/SMS from Shanti Juniors.</li><br>
  
  <li>For every Pre-School, we have an approved fee structure, the same can be availed from the counselor's desk.</li><br>
  
  <li>Any fees paid shall be non-refundable.</li><br>
  
  <li>Shanti Juniors Corporate shall not be responsible for other programs than mentioned in agreement.</li><br>
  
  <li>Transfer Policy is applicable to Shanti Juniors students for all India across network. Fees might differ from center to center-city to city.</li><br>
  
  <li>Fees can be paid in form of Cash/Cheque/Online Payment</li><br>
  
  <li>Un-informed leave of consecutive 20 days or above shall be considered as Drop-out.</li><br>
  
  <li>For any complain or queries, please feel free to write us at info@shantijuniors.com </li><br>
</ul> 


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='25%' class='ltr'>
      
</td>

    
</td>
<td width='10%' class='ltr'>
     Date : 
</td>


<td width='35%' class='ltr'>
    <div class='flex_items_main'>
    <div class="dob_listing">
        <div class="formate_text">
            <p></p>
            <small>D</small>
        </div>
        <div class="formate_text">
            <p></p>
            <small>D</small>
        </div>
    </div>
    
    <div class="dob_listing">
        <div class="formate_text">
            <p></p>
            <small>M</small>
        </div>
        <div class="formate_text">
            <p></p>
            <small>M</small>
        </div>
    </div>
    
    <div class="dob_listing">
        <div class="formate_text">
            <p></p>
            <small>Y</small>
        </div>
        <div class="formate_text">
            <p></p>
            <small>Y</small>
        </div>
        <div class="formate_text">
            <p></p>
            <small>Y</small>
        </div>
        <div class="formate_text">
            <p></p>
            <small>Y</small>
        </div>
    </div>
    </div>
</td>

<td width='5%' class='rtr'>
     
</td>
<td width='25%' class='ltr' style="">
</td>

</tr>
</thead>
</table>
</tr>




</thead>
    </table>
</tr>


<tr>
<table style='margin-top: 25px;margin-bottom: 30px;'>
<thead>
<tr>


<td width='3%' class='ltr'>Place :</td>
<td  width='25%'class='' >
    <p style='border-bottom:1px solid black'></p>
</td>


<td width='25%' class='ctr'>
    <p style='border-bottom:1px solid black;margin-bottom: 0px;'></p>
     <p style="margin-top: 0px;">Parent's/Guardian's Signature</p> 
</td>


</tr> 
</thead>
</table>
</tr>

<tr>
<table style='margin-top: 25px;margin-bottom: 30px;'>
<thead>
<tr>
<td width='13%' class='ltr'style='border-bottom:1px dotted black;'>
    
</td>

</tr> 
</thead>
</table>
</tr>

<tr>
<table style='margin-top: 25px;margin-bottom: 30px;'>
<thead>
<tr>
<td width='13%' class='ltr'style=''>
    For Office use only
</td>

</tr> 
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='18%' class='ltr'>
     Class enrolled in : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    
</td>
<td width='10%' class='ltr'>
     
</td>
<td width='7%' class='ltr'>
     Term : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    
</td>

</tr>
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='14%' class='ltr'>
     Time Slot : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    
</td>
<td width='10%' class='ltr'>
     
</td>
<td width='28%' class='ltr'>
     E-Juniors Enrollment No. : 
</td>
<td width='27%' class='ltr' style="border-bottom: 1px solid black;">
    
</td>

</tr>
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 15px ;'>
<thead>
<tr>
<td width='22%' class='ltr'>
     Invoice/Receipt No. : 
</td>
<td width='23%' class='ltr' style="border-bottom: 1px solid black;">
    
</td>
<td width='11%' class='ltr'>
     Amount : 
</td>
<td width='23%' class='ltr' style="border-bottom: 1px solid black;">
    
</td>

<td width='8%' class='ltr'>
     Date : 
</td>
<td width='25%' class='ltr' style="border-bottom: 1px solid black;">
</td>

</tr>
</thead>
</table>
</tr>


<tr>
<table style='margin-top: 45px;margin-bottom: 45px;'>
<thead>
<tr>
<td width='74%' class='ltr'></td>
    


<td width='30%' class='ctr'>
    <p style="border-bottom: 1px solid black;margin-bottom: -13px;"></p>
     <p>Authorised Signatory </p>
</td>

</tr>
</thead>
</table>
</tr>




</thead>
</table>

</div>


<table style='margin-top: 5px;'>
<thead>
<tr>
<td width='74%' class='ltr'>
    <b>Corp. Off. :</b> <span>1909-1910, 19 Floor, D-Block, Westgate, SG Highway, Makarba,Ahmedabad - 380051</span>
    
</td>


</tr>
</thead>
</table>
<table style='margin-top: 0px;'>
<thead>
<tr>
<td width='64%' class='ltr'>
   <b>Info.:</b><span>9979666660</span> 
</td>
<td width='24%' class='ltr'>
  <small>www.shantijiniors.com|</small>
</td>
<td width='24%' class='ltr'>
</td>

</tr>
</thead>
</table>

</body>
</html>

