@php
$setting = Helper::getSetting();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Student Admission Print</title>
    <style>
        body {
            font-family: none;
            font-size: 19px;
            max-width: 750px;
            margin: 10px auto;
            color: #8e1616;
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
            text-align:center;
        }
        .font_family{
            font-family: system-ui;
            font-weight: 500;
        }
    .head{
        font-size: 61px;
        font-family: auto;
        font-weight: normal;
        text-align: center;
        color: #8e1616;
        margin: -3px;
    }
    .font_class{
        text-align: center;
        font-size: 21px;
        font-weight: 500;
        letter-spacing: 1px;
        margin-top: 0px;
        margin-bottom: 5px;
        color: #8e1616;
    }
    .reg_head{
        font-size: 22px;
        font-weight: 600;
        font-family: ui-monospace;
        color: #8e1616;
    } 
    .application_form{
        font-family: math;
        font-weight: bold;
        font-size: 23px;
        color: #8e1616;
    } 
    .address_font{
        margin: 6px;
        font-size: 20px;
        font-weight: bold;
    } 
   
    .dob_listing{
            display: flex;
            align-items: center;
            margin-left: 7px;
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
            border: 1px solid #8e1616;
            padding: 10px;
            border-radius: 4px;
            width: 14px;
            height: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
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
   
   </style>
</head>
<body class='page'>
   
    <table style="border: 1px solid;">
        <thead>   
            <tr>
                <td width="12%"></td>
                <td width="55%"></td> 
                <td width="6%"></td>            
                <td width="12%"></td>            
            </tr>      
            <tr>
                <td>
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$setting['left_logo'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 73%;">
                </td>             
                <td colspan="3">
                     <p class="head">{{$setting['name'] ?? ''}}</p>
                     <p class="font_class">"An Institute For Class Not For Mass"</p>
                </td>           
            </tr>   
            <tr>
                <td colspan="2" class="reg_head ctr">Registration Form</td> 
                <td >Date :</td>            
                <td style="border-bottom: 1px solid;">{{date('d-m-Y', strtotime($data['admission_date'])) ?? '' }}</td>            
            </tr> 
            <tr>
                <td colspan="12" class="ctr" style="border-bottom: 1px solid;"></td> 
                      
            </tr> 
               
        </thead>
    </table>
<div style="border: 1px solid;padding: 10px;">    
<table>
<thead>
    
<tr>
    <td width="7" class=''> </td>
    <td width="8%"></td> 
    <td width="71%" class=''></td>
   
    <!-- <td colspan="6" class="application_form"><u class="margin_r">Student Information</u></td> -->
    <td width="30%" rowspan="5" class='' >
        <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" style="width: 100%;height: 166px;border: 2px solid #8e1616;">
    </td>

</tr>    
<tr>
    <td colspan="3"><u><b>Student Information</b></u></td> 
</tr>    
<tr>
    <td>Name</td> 
    <td colspan="2" class=''>
        <p style='border-bottom:1px dotted #8e1616;margin: 0px;'>{{ $data['first_name'] ?? '' }} {{ $data['middle_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</p>
    </td>
</tr>    
<tr>
    <td>D.O.B.</td> 
    <td colspan="2" class=''>
        <p style='border-bottom:1px dotted #8e1616;margin: 0px;'>{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</p>
    </td>
</tr>    
<tr>
    <td class=''> Father's</td>
    <td>Name</td> 
    <td class=''>
        <p style='border-bottom:1px dotted #8e1616;margin: 0px;'>{{ $data['father_name'] ?? '' }}</p>
    </td>
</tr>    
</thead>
</table>


<table style='margin-top: 15px;'>
<thead>
    <tr>
        <td width="9%">Add. </td>    
        <td width="95%" style="border-bottom: 1px dotted;">{{ $data['house'] ?? '' }} {{ $data['village_city'] ?? '' }}</td>
    </tr>   
    <tr>
        <td> </td>    
        <td style="height: 30px; border-bottom: 1px dotted;"></td>
    </tr>   
</thead>
</table>
<table style='margin-top: 25px;'>
<thead>
    <tr>
        <td width="6%">Mobile </td>  
        <td width="50%" style="border-bottom: 1px dotted;">{{ $data['mobile'] ?? '' }}</td>
        <td width="25%"></td>  
    </tr>  
</thead>
</table>
<table style='margin-top: 25px;' style="line-height:10px;">
<thead>
    <tr>
        <td width="6%">Class </td>  
        <td width="10%" style="">
            <div class='flex_items_main'>
                <p>6th</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                           <!--{{$data['class_type_id'] ?? ''}}-->
                        @if($data->class_type_id == 1)
                            <i class="fa fa-check"></i>
                        @endif
                       </p>
                   </div>
               </div>
            </div>
        </td> 
        <td width="10%" style="">
            <div class='flex_items_main'>
                <p>7th</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                        @if($data->class_type_id == 2)
                            <i class="fa fa-check"></i>
                        @endif
                       </p>
                   </div>
               </div>
            </div>
        </td> 
        <td width="10%" style="">
            <div class='flex_items_main'>
                <p>8th</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                      @if($data->class_type_id == 3)
                        <i class="fa fa-check"></i>
                      @endif
                       </p>
                   </div>
               </div>
            </div>
        </td> 
        <td width="10%" style="">
            <div class='flex_items_main'>
                <p>9th</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                        @if($data->class_type_id == 4)
                            <i class="fa fa-check"></i>
                        @endif
                       </p>
                   </div>
               </div>
            </div>
        </td> 
        <td width="10%" style="">
            <div class='flex_items_main'>
                <p>10th</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                        @if($data->class_type_id ==5)
                            <i class="fa fa-check"></i>
                        @endif
                       </p>
                   </div>
               </div>
            </div>
        </td> 
        <td width="10%" style="">
            <div class='flex_items_main'>
                <p>11th</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                        @if($data->class_type_id == 6)
                            <i class="fa fa-check"></i>
                        @endif
                       </p>
                   </div>
               </div>
            </div>
        </td> 
        <td width="10%" style="">
            <div class='flex_items_main'>
                <p>12th</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                        @if($data->class_type_id == 7)
                            <i class="fa fa-check"></i>
                        @endif
                       </p>
                   </div>
               </div>
            </div>
        </td> 
    </tr>  
</thead>
</table>
<!--@php
$subjectName = DB::table('subject')->whereNull('deleted_at')->where('id',$data->subject_id ?? '')->get();
@endphp-->

@php
    $arrSub = explode(',', $data->subject_id);
    $subject = DB::table('subject')->whereIn('id',$arrSub)->get();
@endphp

<table style='margin-top: 25px;'>
<thead>
    <tr>
        <td width="6%">Subject </td>  
        @if(!empty($data->subject_id))
            @foreach($subject as $sub)
        <td width="10%" style="">
           
            <div class='flex_items_main'>
                <p>{{$sub->name ?? ''}}</p>
                <div class="dob_listing">
                   <div class="formate_text">
                       <p>
                       <i class="fa fa-check"></i>
                       </p>
                   </div>
               </div>
            </div>
           
        </td> 
         @endforeach
            @endif
    </tr>  
   
</thead>
</table>




<table style='margin-top: 20px;margin-bottom: 20px;'>
<thead>
    <tr>
       
        <td colspan="12" class="font_family">
            <p><u><b>Terms And Conditions</b></u></p>
            <ol>
                <li>The course will be conducted in Hindi and English Medium.</li>
                <li>Fees ince paid will not be refunded/adjusted under any circumstances.</li>
            </ol>
        </td> 
    </tr>  
   
</thead>
</table>
<table style='margin-top: 10%;text-align: center;'>
<thead>
    <tr>
        <td width="17%" class="font_family" style="">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/principal_sign/'.$setting['principal_sign'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="height:50px;width:100px;">
        </td> 
        <td width="23%" ></td> 
        <td width="15%" class="font_family" style="">{{ date('Y-m-d') }}</td> 
        <td width="23%" ></td> 
        <td width="17%" class="font_family" style=""></td> 
    </tr>  
    <tr>
        <td class="font_family" style="border-top: 1px dotted;">Director Sign.</td> 
        <td></td> 
        <td class="font_family" style="border-top: 1px dotted;">Date</td> 
        <td></td> 
        <td class="font_family" style="border-top: 1px dotted;">Parents Sign.</td> 
    </tr>  
</thead>
</table>
</div>
<table style='margin-top: 0px;text-align: center;border: 1px solid;'>
<thead>
    <tr>
        <td width="7%" class="font_family" style=""></td> 
       
        <td width="86%" >
            <p class="address_font">{{$setting['address'] ?? ''}} </p>
        </td> 
        <td width="7%" class="font_family" style=""></td> 
    </tr>  
</thead>
</table>



</body>
</html>

