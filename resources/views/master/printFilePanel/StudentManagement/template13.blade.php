@php
$getSetting=Helper::getSetting();
$broclass = DB::table('class_types')->where('id',$data->sibling_class_id)->whereNull('deleted_at')->first();
//dd($broclass);
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
      font-size:14px;
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
        .ctr{
            text-align:center;
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
                height: 100px;
                padding: 3px;
                border: 2px dotted black;
                margin-bottom: -13px;
                margin-top: -35px;
        }
        
        .fontweight{
                font-weight: 500;
                height: 21px;
        }
        .table_border{
                   border: 1px solid black;
        }
         
   </style>
</head>
<body class='page'>
<table style="padding: 30px;">
    	
			<tbody >
					<tr>
      <td rowspan='2' width='25%'>
         
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
           @if(!empty($data['image']))
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @else
          <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @endif
      </td>
      </tr>
     
  </tbody>
 
  </table> 
 
<table >

<thead>

<tr>
<table style='margin-top: 5px;'>

<thead>
<tr>
<td width='41%' class='ltr fontweight'>Form of Registration for Admission to class :</td>
<td width='40%' class='ltr fontweight' style='border-bottom:1px dotted black'></td>
<td width='9%' class='ltr fontweight'>Session</td>
<td width='80%' class='ltr fontweight' style='border-bottom:1px dotted black'></td>
    </tr>  
    
</thead>

    </table>
    
</tr>

<tr>
<table style='margin-top: 5px;'>

<thead>
<tr>
<td width='14%' class='ltr fontweight'>Registration No </td>
<td width='80%' class='ltr fontweight'>{{$data['registration_no'] ?? ''}}</td>
    </tr>  
    
</thead>

    </table>
    
</tr>


<tr>
<table style='margin-top: 5px;'>

<thead>
<tr>
    <td width='5%' class='ltr fontweight'>1.</td>
<td width='43%' class='ltr fontweight'>Name of the candidate in full (in Block Letters)</td>

<td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>
    </tr>  
    
</thead>

    </table>
    
</tr>

<tr>
<table style='margin-top: 5px ;'>

<thead>
<tr>
    <td width='6%' class='ltr fontweight'></td>
<td width='40%' class='ltr fontweight' style='border-bottom:1px dotted black'></td>

<td width='10%' class='ltr fontweight'>(Male/Female)</td>

<td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['Gender'] ['name'] ?? '-' }}</td>
    </tr>  
    
</thead>

    </table>
    </tr>
    
    
<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>2.</td>
                    <td width='12%' class='ltr fontweight'>Name of the Father</td>
                    
                    <td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['father_name'] ?? '' }}</td>
                </tr>  
                <tr>
                     <td width='4%' class='ltr fontweight'>3.</td>
                    <td width='14%' class='ltr fontweight'>Name of the Mother</td>
                    
                    <td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['mother_name'] ?? '' }}</td>
                </tr>  
    
        </thead>
    </table>
</tr>
<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>4.</td>
                    <td width='25%' class='ltr fontweight'>Date of Birth in(In Figures)</td>
                    
                    <td  width='45%'class='ltr' style='border-bottom:1px dotted black'>{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>
                    <td  width='55%'class='ltr'>(As per Birth Certificate)</td>
                </tr>  
                
    
        </thead>
    </table>
</tr>
    
    
<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>5.</td>
                    
                    <td width='15%' class='ltr fontweight'>Age on 1 April,20</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'></td>
                    
                    <td width='7%' class='ltr fontweight'>years:</td>
                    <td  width='14%'class='' style='border-bottom:1px dotted black'></td>
                    
                    <td width='7%' class='ltr fontweight'>Months:</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'></td>
                    
                    <td width='7%' class='ltr fontweight'>Days:</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'></td>
                </tr>  
            
    
        </thead>
    </table>
</tr>
    
    

<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'></td>
                    <td width='100%' class='ltr fontweight'>
                        (Attach Photocopy of Date of Birth Certificate from the Municipal Corporation and Aadhar card of child and)
                    </td>
                </tr>  
                <tr>
                    <td width='4%' class='ltr fontweight'></td>
                    <td width='100%' class='ltr fontweight'>father/mother</td>
                </tr>  
            
        </thead>
    </table>
</tr>
    
    
<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>6.</td>
                    <td width='23%' class='ltr fontweight'>Last School Attended (if any)</td>
                    
                    <td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['last_school'] ?? '' }}</td>
                </tr>  
               
    
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'></td>
                    <td width='100%' class='ltr fontweight'>
                        (Attach attested Photocopy of Progress Card from the previous school)
                    </td>
                </tr>  
                
            
        </thead>
    </table>
</tr>
    
    
<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>7.</td>
                    <td width='17%' class='ltr fontweight'>Class in which studying</td>
                    
                    <td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['className'] ?? '-' }}</td>
                </tr>  
               
    
        </thead>
    </table>
</tr>
    
    
<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>8.</td>
                    <td width='30%' class='ltr fontweight'>Class in which admission is sought</td>
                    
                    <td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['ClassTypes']['name'] ?? '' }}</td>
                </tr>  
               
    
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>9.</td>
                    
                    <td width='10%' class='ltr fontweight'>Nationnality:</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'>{{$data['nationality'] ?? ''}}</td>
                    
                    <td width='7%' class='ltr fontweight'>State:</td>
                    <td  width='14%'class='' style='border-bottom:1px dotted black'></td>
                    
                    <td width='7%' class='ltr fontweight'>Religion:</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'>{{$data['religion'] ?? ''}}</td>
                    
                    <td width='17%' class='ltr fontweight'>Mother Tongue:</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'>{{$data['mother_tongue'] ?? ''}}</td>
                </tr>  
            
    
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'></td>
                    <td width='95%' class='ltr fontweight'>
                    Real Brother / sister studying in Madhav Vidhyapeeth
                    </td>
                    
                </tr>  
            
    
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr>
                    <td width='2%' class='ltr fontweight'>10.</td>
                    
                    <td width='2%' class='ltr fontweight'>(a)Name :</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'>{{$data['sibling_name'] ?? ''}}</td>
                    
                    <td width='2%' class='ltr fontweight'>Class:</td>
                    <td  width='14%'class='' style='border-bottom:1px dotted black'>{{$broclass->name ?? ''}}</td>
                    
                    <td width='2%' class='ltr fontweight'>Section:</td>
                    <td  width='12%'class='' style='border-bottom:1px dotted black'></td>
                </tr>  
            
    
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: -10px ;'>
        <thead>
                <tr>
                    <td width='95%' style='border-bottom:1px dotted black' class='ltr fontweight'></td> 
                </tr> 
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: -10px ;'>
        <thead> 
                <tr>
                      <td width='95%' class='ctr fontweight'>
                         <p style="margin-bottom: -9px;font-size: 15px;font-weight: 100;">(Please cut here)</p>
                        <h2 style="margin-bottom: -6px;">{{$getSetting['name'] ?? ''}}</h2>
                        <p style="font-size: 15px;font-weight: 100;">Kindly bring this slip at the time of interaction.</p>
                    </td>
                </tr> 
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: -25px ;'>
        <thead> 
                <tr>
                      <td width='8%' class='ctr fontweight'>Reg.No. </td>
                      <td width='50%' class='ltr fontweight'>2869</td>
                      <td width='95%' class='rtr fontweight'>Photograph</td>
                </tr> 
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead> 
                <tr>
                      <td width='27%' class='ltr fontweight'>Name of the cendidatein full</td>
                      <td width='53%' class='ltr'  style='border-bottom:1px dotted black'></td>
                      <td width='9%' class='rtr fontweight'>Boy / Girl</td>
                      <td width='54%' class='ltr'  style='border-bottom:1px dotted black'></td>
                </tr> 
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead> 
                <tr>
                      <td width='9%' class='ltr fontweight'>Mother</td>
                      <td width='45%' class='ltr' style='border-bottom:1px dotted black'></td>
                      <td width='8%' class='rtr fontweight'>Father</td>
                      <td width='55%' class='ltr' style='border-bottom:1px dotted black'></td>
                </tr> 
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead> 
                <tr>
                      <td width='30%' class='ltr fontweight'>(Guardian'only if father not living)</td>
                      <td width='53%' class='ltr'  style='border-bottom:1px dotted black'></td>
                      <td width='9%' class='rtr fontweight'></td>
                      <td width='54%' class='ltr'></td>
                </tr> 
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead> 
                <tr>
                      <td width='48%' class='ltr fontweight'>The child will appear for an <b>interaction</b> on the <b>Date</b></td>
                      <td width='15%' class='ltr' style='border-bottom:1px dotted black'></td>
                      <td width='9%' class='ltr fontweight'>& time</td>
                      <td width='14%' class='ltr' style='border-bottom:1px dotted black'></td>
                      <td width='54%' class='ltr'>along with the</td>
                </tr> 
        </thead>
    </table>
</tr>
<tr>
    <table style='margin-top: 5px ;'>
        <thead> 
                <tr>
                      <td width='45%' class='ltr fontweight'>parent with all <b>original</b> certificates as mentioned</td>
                      <td width='25%' class='ltr'></td>
                      <td width='20%' class='ltr fontweight'>Date issuebly office</td>
                      <td width='14%' class='ltr' style='border-bottom:1px dotted black'></td>
                    
                </tr> 
        </thead>
    </table>
</tr>

    </thead>
    </table>
    
    
    
    
    
    
    
<table style='margin-top: 5px;'>
<thead>
<tr>
    <table style='margin-top: 5px;'>
        <thead>
                <tr>
                    <td width='4%' class='ltr fontweight'>11.</td>
                    <td width='85%' class='ltr fontweight'>Parent Details :</td>
                </tr> 
        </thead>
    </table>
</tr>


<tr>
    <table style='margin-top: 5px ;'>
        <thead>
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>Sr.</td>
                    <td width='20%' class='table_border ctr fontweight'>Details</td>
                    <td width='20%' class='table_border ctr fontweight'>Father</td>
                    <td width='20%' class='table_border ctr fontweight'>Mother</td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>A</td>
                    <td width='20%' class='table_border fontweight'>Name</td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>B</td>
                    <td width='20%' class='table_borde fontweight'>Occupation</td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>C</td>
                    <td width='20%' class='table_borde fontweight'>
                        if employed mention whether working with Armed Forces / Govt. Dept. (Gazetted
                        / Non Gazetted ) Non. Govt./C.A./ Engineer / Doctor. Organization / Corporation MNC/Private Service
                    </td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>D</td>
                    <td width='20%' class='table_borde fontweight'>
                        if self employed, give details of the bature of business
                    </td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>E</td>
                    <td width='20%' class='table_borde fontweight'>Office Address</td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>F</td>
                    <td width='20%' class='table_borde fontweight'>Present Posting (place)</td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>G</td>
                    <td width='20%' class='table_borde fontweight'>Transfer Case (Proof to be attached if applying for HKG)</td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                <tr class="table_border ">
                    <td width='4%' class='table_border ctr fontweight'>H</td>
                    <td width='20%' class='table_borde fontweight'>Home Address </td>
                    <td width='20%' class='table_border fontweight'></td>
                    <td width='20%' class='table_border fontweight'></td>
                </tr>  
                
            
        </thead>
    </table>
</tr>


    </thead>
    </table>

</body>
</html>
