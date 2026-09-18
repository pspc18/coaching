@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form Print</title>
    <style>
     
        body {
            font-family: auto;
            font-size: 15px;
            max-width: 750px;
            margin: 10px auto;
            font-weight: 500;
        }
   

        table {
                width: 100%;
                border-collapse: collapse;
                
            }
        
      
        .ltr{
            text-align: left;
            border-right: none !important;
            padding:5px
        }
        .rtr{
            text-align:right;
        }
        .etr{
            text-align:end;
        }
        .ctr{
            text-align:center;
        }
       
        .head{
            text-align: center;
    font-size: 47px;
    font-weight: normal;
    margin-bottom: -14px;
    font-family: fangsong;
        }
        .address_font{
            text-align: center;
            font-size: 19px;
            font-weight: 100;
            margin-bottom: -26px;
        }
        .td_height{
            height: 33px;
        }
        .td_padding{
            padding-top: 12px;
        }

    .td_border{
               border: 1px solid;
        }
        .box_border{
            border: 1px solid;
            height: 28px;
            width: 54%;
        }
        .font_size{
           font-size: 16px;
        }
        
        @page {
          size: A4;
          margin: 0;
        }
   </style>
</head>
<body class='page'>
      <?php
if ($data->branch_id == 2) { ?>



 <div style="border: 1px solid;padding: 10px;">
    <table style='margin-top: 0px ;'>
        <thead>
            <tr>
                <td class='ctr'>
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 74%;height: 115px;">
                </td>
            </tr>
            <tr>
                <td class='ctr'>
                    <h1 style="margin-top: -15px;"><u>Admission Enquiry Form</u></h1>
                </td>
            </tr>
        </thead>
    </table>
   
<table style='margin-top: 20px ;'>
    <thead>
        <tr>
            <td width='25%' class='ltr'>Name of the Applicant :</td>
            <td  width='50%'class='' style='border-bottom:1px dotted black'>{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>
            <td width='5%' class='ltr'>Age</td>
            <td width='20%' class='ltr' style='border-bottom:1px dotted black'>{{ $data['age'] ?? '-' }}</td>
        </tr>  
    </thead>
</table>            
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='17%' class='ltr'>Father's Name :</td>
            <td  width='35%'class='' style='border-bottom:1px dotted black'>{{ $data['father_name'] ?? '-' }}</td>
            <td width='10%' class='ltr'>Occupation</td>
            <td width='30%' class='ltr' style='border-bottom:1px dotted black'>{{ $data['f_occupation'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class='ltr'>Mother's Name :</td>
            <td class='' style='border-bottom:1px dotted black'>{{ $data['mother_name'] ?? '-' }}</td>
            <td class='ltr'>Occupation</td>
            <td class='ltr' style='border-bottom:1px dotted black'>{{ $data['m_occupation'] ?? '-' }}</td>
        </tr>
        
    </thead>
</table>         
<table style='margin-top: 0px ;'>
    <thead>
        <tr>
            <td width='25%' class='td_padding ltr'>Purpose of Visit :</td>
           <td width='30%' class='td_padding ltr'>1. School Admission </td>
           <td width='7%' class='td_padding ltr'><div class="box_border"></div></td>
           <td width='18%' class='td_padding ltr'>Specify Class : </td>
           <td width='23%' class='ltr' style='border-bottom:1px dotted black'></td>
        </tr>  
        <tr>
           <td class='td_padding ltr'></td>
           <td class='td_padding ltr'>2. Evening Classes </td>
           <td class='td_padding ltr'><div class="box_border"></div></td>
           <td class='td_padding ltr'>Specify Course : </td>
           <td class='ltr' style='border-bottom:1px dotted black'></td>
        </tr>  
        <tr>
           <td class='td_padding ltr'></td>
           <td class='td_padding ltr'>3. Day Boarding Facility  </td>
           <td class='td_padding ltr'><div class="box_border"></div></td>
           <td class='td_padding ltr'>Specify Timings : </td>
           <td class='ltr' style='border-bottom:1px dotted black'></td>
        </tr>  
        
    </thead>
</table>         
<table style='margin-top: 10px;'>
    <thead>
        <tr>
            <td colspan="12" class='td_padding ltr'>Sibling Details : </td>
        </tr>
    </thead>
</table>         
<table style='margin-top: 0px;border: 1px solid;'>
    <thead>
          
        <tr>
            <td width='10%' class='td_padding td_border ctr'>S.No.</td>
            <td width='30%' class='td_padding td_border ctr'>Name </td>
            <td width='10%' class='td_padding td_border ctr'>Age</td>
            <td width='15%' class='td_padding td_border ctr'>Class</td>
            <td width='40%' class='td_padding td_border ctr'>School</td>
        </tr>  
        <tr>
            <td class='td_padding td_border ctr' style="height: 20px;"></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
        </tr>  
        <tr>
            <td class='td_padding td_border ctr' style="height: 20px;"></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
        </tr>  
        <tr>
            <td class='td_padding td_border ctr' style="height: 20px;"></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
            <td class='td_padding td_border ctr'></td>
        </tr>  
    </thead>
</table>         
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='5%' class='td_padding ltr'>Date</td>
            <td  width='20%'class='td_padding' style='border-bottom:1px dotted black'>{{date('d-m-Y', strtotime($data['admission_date'])) ?? '' }}</td>
            <td width='30%' class='td_padding ltr'></td>
            <td  width='30%'class='td_padding'></td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td colspan="12" class='td_padding ltr'>Contact Details</td>
        </tr>  
        <tr>
           <td width='10%' class='td_padding ltr'>Address :</td>
            <td  width='85%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['address'] ?? '-' }}</td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='10%' class='td_padding ltr'>Mobile:1.</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['mobile'] ?? '-' }}</td>
            <td width='3%' class='td_padding ltr'>2.</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['mobile2'] ?? '-' }}</td>
            <td width='3%' class='td_padding ltr'>Phone:</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['father_mobile'] ?? '-' }}</td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead> 
        <tr>
            <td width='8%' class='td_padding ltr'>EMail :</td>
            <td  width='85%'class='td_padding' style='border-bottom:1px solid black'>{{ $data['email'] ?? '-' }}</td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead> 
        <tr>
            <td width='12%' class='td_padding ltr'>Reference :</td>
            <td  width='85%'class='td_padding' style='border-bottom:1px dotted black'></td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 50px ;'>
    <thead> 
        <tr>
            <td class='td_padding ltr'>Signature</td>
        </tr>  
        <tr>
            <td class='td_padding ltr'>
              <p style="font-size: 18px;font-family: emoji;font-weight: 600;margin-bottom: -17px;margin-top: 6px;text-align: center;">Corporate Office: 4-D-126,Near Stadium,Chitrakoot, Vaishali Nager,Jaipur (Raj.)</p> 
              <p style="font-size: 18px;font-family: emoji;font-weight: 600;margin-bottom: -5px;text-align: center;">Ph-9983700051 Web-www.thesantakidz.com Email-info@thesantakidz.comm</p>
            </td>
        </tr>  
    </thead>
</table>
</div>

<table style='margin-top: 30px ;'>
    <thead> 
        <tr>
            <td class='td_padding ltr'>
               <p style="text-align: center;font-weight: 600;font-size: 19px;margin-bottom: -14px;">सत्यापन प्रतिवेदन प्रपत्र (भाग-3)</p>
               <p style="text-align: center;font-weight: 600;font-size: 16px;">
                   आरटीई के अन्तर्गत 25 प्रतिशत सीटों पर निःशुल्क शिक्षा हेतु अध्ययनरत बालकों के सत्यापन हेतु<br>निरीक्षण प्रतिवेद (सत्र 2020-21)</p>
            </td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead> 
        <tr>
           <td colspan="12" class='font_size td_padding ltr'>
             <b>1. निरीक्षण दल के सदस्यों के नाम, पद एवं पदस्थापन स्थान :-</b>
           </td>
        </tr>  
        <tr>
           <td colspan="12" class='font_size td_padding ltr'>1.1 निरीक्षणकर्ता अध्यक्ष</td>
        </tr>  
        <tr>
           <td width="62%" class='font_size td_padding ltr'>1.1.1 नाम</td>
           <td width="32%" class='font_size td_padding ltr'>1.1.2 पद</td>
        </tr>  
        <tr>
           <td width="62%" class='font_size td_padding ltr'>1.1.3 पदस्थापन स्थान</td>
           <td width="32%" class='font_size td_padding ltr'>1.1.4 मोबाइल नं</td>
        </tr> 
        
        <tr>
           <td colspan="12" class='font_size td_padding ltr'>
             <b>1.2 निरीक्षणकर्ता सदस्य</b>
           </td>
        </tr>  
        <tr>
           <td width="62%" class='font_size td_padding ltr'>1.2.1 नाम</td>
           <td width="32%" class='font_size td_padding ltr'>1.2.2 पद</td>
        </tr>  
        <tr>
           <td width="62%" class='font_size td_padding ltr'>1.2.3 पदस्थापन स्थान</td>
           <td width="32%" class='font_size td_padding ltr'>1.2.4 मोबाइल नं</td>
        </tr> 
        
        <tr>
           <td colspan="12" class='font_size td_padding ltr'>
             <b>2. निरीक्षण की दिनांक :- ....../....../......../</b>
           </td>
        </tr>    
        
        
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead>
          <tr>
           <td colspan="12" class='font_size td_padding ltr'>
             <b>3. विद्यालय से सम्बन्धित विवरण :-</b>
           </td>
        </tr>  
        <tr>
           <td width="29%" class='font_size td_padding ltr'>3.1 विद्यालय का नाम</td>
           <td width="37%" class='td_padding ltr' style="font-size: 14px;font-family: emoji;">SHREYA INTERNATIONAL SCHOOL (08122603106)</td>
           <td width="17%" class='font_size td_padding ltr'>3.2 डाइस कोड</td>
           <td width="12%" class='font_size td_padding ltr'>08122603106</td>
        </tr>
        <tr>
           <td class='font_size td_padding ltr'>3.3 विद्यालय का पूर्ण पता</td>
           <td class='td_padding ltr' style="font-size: 14px;font-family: emoji;">79 Everest Vihar, King's road, Nirman Nagar,</td>
           <td class='font_size td_padding ltr'></td>
           <td class='font_size td_padding ltr'></td>
        </tr>
        <tr>
           <td class='font_size td_padding ltr'>3.4 जिला</td>
           <td class='td_padding ltr' style="font-size: 14px;font-family: emoji;">JAIPUR</td>
           <td class='font_size td_padding ltr'>3.5 ब्लॉक</td>
           <td class='td_padding ltr' style="font-size: 14px;font-family: emoji;">JAIPUR WEST</td>
        </tr>
        <tr>
           <td class='font_size td_padding ltr'>3.6 ग्राम पंचायत / यू एल बी</td>
           <td class='td_padding ltr' style="font-size: 14px;font-family: emoji;">JAIPUR NN PART2</td>
           <td class='font_size td_padding ltr'>3.7 ग्राम / वार्ड</td>
           <td class='td_padding ltr' style="font-size: 14px;font-family: emoji;">WARD NO.31 JAIPUR CITY</td>
        </tr>
        <tr>
           <td class='font_size td_padding ltr'>3.8 विधानसभा क्षेत्र</td>
           <td class='td_padding ltr' style="font-size: 14px;font-family: emoji;">JHOTWARA(046)</td>
           <td class='font_size td_padding ltr'>3.9 पिन कोड</td>
           <td class='font_size td_padding ltr'>302019</td>
        </tr>
        <tr>
           <td colspan="2" class='font_size td_padding ltr'><b>4. मान्यता के आधार पर विद्यालय में शिक्षण का माध्यम :-</b></td>
           <td colspan="2" class='td_padding ltr' style="font-size: 14px;font-family: emoji;"><b>ENGLISH</b></td>
        </tr>
        <tr>
           <td colspan="12" class='font_size td_padding ltr' style="background-color: #a9a9a96e;padding: 1px"><b>5. विद्यालय के दो माध्यम होने पर माध्यमवार विवरण</b></td>
        </tr>
    </thead>
</table>
<table style='border:1px solid;margin-top: 10px ;'>
    <thead>
        <tr></td>
            <td colspan="12" class="font_size td_border" style="background-color: #a9a9a96e;padding: 1px"><b>5.2 अंग्रेजी माध्यम</b></td>
        </tr>
        <tr>
            <td class="font_size td_border">5.1.1 विद्यालय श्रेणी</td>
            <td colspan="3" class="font_size td_border">Primary with Upper Primary(1-8)</td>
        </tr>
        <tr>
            <td class="font_size td_border">5.1.2 विद्यालय का प्रकार</td>
            <td colspan="3" class="font_size td_border">
               <ul style="margin: 2px;">
                   <li>Boys</li>
                   <li>Girls</li>
                   <li>Co-Edu</li>
               </ul>
            </td>
        </tr>
        <tr>
            <td width="34%" class="font_size td_border" >5.1.3 विद्यालय अंतिम मान्यता संख्या</td>
            <td width="17%" class="font_size td_border">132/2012</td>
            <td width="32%" class="font_size td_border">5.1.4 विद्यालय अंतिम मान्यता दिनांक</td>
            <td width="20%" class="font_size td_border">04/10/2012</td>
        </tr>
        <tr>
            <td class="font_size td_border">5.1.5 विद्यालय संबन्धित बोर्ड</td>
            <td class="font_size td_border">Not Applicable</td>
            <td class="font_size td_border">
                <ul style="margin: 2px;">
                    <li>RBSE</li>
                    <li>IB</li>
                    <li>None</li>
                </ul>
            </td>
            <td class="font_size td_border">
                <ul style="margin: 2px;">
                    <li>CBSE</li>
                    <li>ICSE</li>
                    <li>OTHER</li>
                </ul>
            </td>
        </tr>
         <tr>
            <td class="font_size td_border">5.1.6 मान्यता नंबर</td>
            <td class="font_size td_border"></td>
            <td class="font_size td_border">5.1.7 बोर्ड से मान्यता प्राप्ति दिनांक</td>
            <td class="font_size td_border"></td>
        </tr>
    </thead>
</table>



<?php } else { ?>



    <table style='margin-top: 20px ;'>
        <thead>
            <tr>
                <td width='16%' class='ltr'>
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 100%;height: 124px;margin-top: 41px;">
                </td>
                <td  width='75%'class='ctr' >
                    <h1 class="head">{{$getSetting['name'] ?? ''}}</h1>
                    <p style="margin-bottom: -10px;font-family: auto;">AN ENGLISH MEDIUM CO-ED SCHOOL</p>
                    <P style="font-size: 13px;font-family: auto;">{{$getSetting['address'] ?? ''}} PH : 9829737437,8302171013</P>
                </td>
                <td width='10%' class='ltr'>
                    <!--<img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 74%;height: 115px;">-->
                </td>
            </tr>
        </thead>
    </table>
    <table style='margin-top: 10px ;'>
        <thead>
            <tr>
                <td colspan="12" style="text-align: center;"><u>REGISTRATION FORM</u></td>
            </tr>
            <tr>
                <td width='17%' class='ltr'>ADMISSION No.</td>
                <td  width='23%'class='' style='border-bottom:1px dotted black'>{{ $data['admissionNo'] ?? '' }}</td>
                <td width='27%' class='ltr'></td>
                
                <td width='6%' class='ltr'>DATE</td>
                <td  width='55%'class='' style='border-bottom:1px dotted black'>{{date('d-m-Y', strtotime($data['admission_date'])) ?? '' }}</td>
            </tr>
            
        </thead>
    </table>
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='32%' class='ltr'>ADMISSION SOUGHT TO CLASS :-</td>
            <td  width='45%'class='' style='border-bottom:1px dotted black'>{{ $data['ClassTypes']['name'] ?? '-' }}
            @if (!empty($data['Section']['name']))
                ({{ $data['Section']['name'] }})
            @endif</td>
            <td width='20%' class='ltr'></td>
        </tr> 
        <tr>
            <td colspan="12" class="td_height">Please Register the name of my ward for admission in the school :</td>
        </tr>  
    </thead>
</table>            
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='20%' class='ltr'>NAME OF THE SCHOLAR :-</td>
            <td  width='55%'class='' style='border-bottom:1px dotted black'>{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>
        </tr> 
        <tr>
            <td width='20%' class='td_padding ltr'>DATE OF BIRTH  :-</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>
        </tr> 
        
    </thead>
</table>         
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='22%' class='td_padding ltr'>AGE :-</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['age'] ?? '-' }}</td>
            <td width='10%' class='td_padding ltr'> </td>
            <td width='20%' class='td_padding ltr'>SEX : ( {{ $data['gender'] ['name'] ?? '-' }} )</td>
        </tr>  
        
    </thead>
</table>         
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='20%' class='td_padding ltr'>FATHER'S NAME :-</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['father_name'] ?? '-' }}</td>
        </tr>  
        <tr>
            <td width='20%' class='td_padding ltr'>OCCUPATION :-</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['f_occupation'] ?? '-' }}</td>
        </tr>  
        <tr>
            <td width='20%' class='td_padding ltr'>OFFICE ADDRESS :-</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['fathers_address'] ?? ''}}</td>
        </tr>   
        <tr>
            <td width='20%' class='td_padding ltr'>MOTHER'S NAME :-</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['mother_name'] ?? '-' }}</td>
        </tr>  
        <tr>
            <td width='20%' class='td_padding ltr'>OCCUPATION :-</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['m_occupation'] ?? '-' }}</td>
        </tr>
        <tr>
            <td width='20%' class='td_padding ltr'>OFFICE ADDRESS :-</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['mothers_address'] ?? ''}}</td>
        </tr>
        <tr>
            <td width='20%' class='td_padding ltr'>RESIDENTIAL ADDRESS :</td>
            <td  width='55%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['residential_address'] ?? '-' }}</td>
        </tr>
    </thead>
</table>         
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='22%' class='td_padding ltr'>MOBILE NO. :-</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['mobile'] ?? '-' }}</td>
            <td width='3%' class='td_padding ltr'>2</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['mobile2'] ?? '-' }}</td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='24%' class='td_padding ltr'>WHETHER S.C/S.T/O.B.C :</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['caste_category'] ?? '-' }}</td>
            <td width='10%' class='td_padding ltr'>Religion :</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{$data['religion'] ?? ''}}</td>
        </tr>  
    </thead>
</table>
<table style='margin-top: 10px ;'>
    <thead>
        <tr>
            <td width='22%' class='td_padding ltr'>NAME OF PREVIOUS SCHOOL ATTENDED :</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['previous_school_attended'] ?? '-' }}</td>
        </tr>  
        <tr>
            <td width='22%' class='td_padding ltr'>ANY OTHER INFORMATION :</td>
            <td  width='30%'class='td_padding' style='border-bottom:1px dotted black'>{{ $data['other_information'] ?? '-' }}</td>
        </tr>  
        <tr>
            <td colspan="12" class='etr' style="padding-top: 85px;">PARENT SIGNATURE </td>
        </tr>  
        <tr>
            <td colspan="12" class='td_padding'>
              I Understand that the School, on accepting this fee & registering the name is not bound to refund the money . I agree to 
              abide by the rules & regulation of the School , pay the fee in advance and settle all accounts promptly . I understand that
              I  will not hold the school responsible for any untoward incident , mishap which might occur during the school hours.   
            </td>
        </tr>  
    </thead>
</table>





<?php } ?>




</body>
</html>

