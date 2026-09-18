<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<style>
@page{
  width:100%;
  margin:0;
  
}
body{

padding-right: 10px;
font-family:Cambria;
}
.style23 {

  font-weight: bold;
  font-size: 17px;
  width: 5%;
}
.style69 { font-weight: bold;}
.style73 {font-size: 14px}

.footer {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  text-align: center;
}
</style>
<title>Email lab report</title>
@php
$getSetting=Helper::getSetting();
@endphp
<div id="showcont" >
  <div align="center">
    
    
    
    <table  style="padding-top:30px;">

	   <tr>
	      <td colspan="3"><img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"></td>
	      <td colspan="6"><center><h1>{{$getSetting['name'] ?? ''}}</h1></center>
	      <center><p><b>{{$getSetting['address'] ?? ''}}</b></p></center>
	      <center>Email : {{$getSetting['gmail'] ?? ''}} Web : www.rukmanisoftware.com</center></td>
	      <td colspan="3"><img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"></td>
      </tr>
	    <tr>
        <td colspan="12"><hr></td>
      </tr>
 
	    <tr>
        <td colspan="12"><center><h3><b><u>ADMISSION FORM</u></b></h3></center></td>
      </tr>.
      
      <tr>
        <td colspan="12">
            <table width="100%">
                <tr>
                <td><span class="style73"><strong>School Code:</strong></span><span class="style73"><strong>
                    
                </strong></span></td>
                <td align="right"><span class="style73 style33 style73"><strong>School Dise Code :</strong></span><span class="style73"><strong>
                    
                </strong></span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>Admission No.:</strong></span><span class="style73"><strong>
                   {{$emailData['name'] ?? ''}}
                </strong></span></td>
                <td align="right"><span class="style73"><strong>Date of Admission:</strong></span><span class="style73"><strong>
                    {{$emailData['name'] ?? ''}}
                </strong></span></td>
                </tr>

            </table>
            <hr>

            <table width="100%">
                <tr>
                <td><span class="style73"><strong>1. Name of Student :- </strong></span><span class="style73"><strong>
                    {{$emailData['name'] ?? ''}}
                </strong></span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>2. Father's Name :- </strong></span> <span class="style73">
                   {{$emailData['father_name'] ?? ''}}
               </span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>3. Date Of Birth :- </strong></span><span class="style73">
                    {{$emailData['dob'] ?? ''}}
                </span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>4. Gender :- </strong></span><span class="style73">
                    {{$emailData['Gender']['name'] ?? ''}}
                </span></td>
                </tr>                
                <tr>
                <td><span class="style73"><strong>5. Class Of Admission :</strong></span><span class="style73"><strong>
                    {{$emailData['ClassTypes']['name'] ?? ''}}
                </strong></span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>6. Father's Contact No. :- </strong></span><span class="style73">
                    {{$emailData['father_mobile'] ?? ''}}
                </span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>7. Admission Type :- </strong></span><span class="style73">
                   Regular
                 </span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>8. Admission Fees :- </strong></span><span class="style73">
                    {{$emailData['rigistration_fees'] ?? ''}}
                </span></td>
                </tr>
                <tr>
                <td><span class="style73"><strong>9. Remark :- </strong></span><span class="style73">
                    {{$emailData['remark_1'] ?? ''}}
                </span></td>
                </tr>                
            </table>            
        </td>
      </tr>

    </table>

  

  <div  class="footer" id="myfooter" style="margin-bottom:90px;">
    <table height="119"  cellpadding="0" cellspacing="0" style="padding-top:40px; width:100%">

     <tr>
        <td width="46%" height="79" class="line style33 style73" >             </td>
       <td width="44%" class="css style14 style73" style="line-height:0.2em;"><p align="right" class="style69"> 
        
         </p>
         <p align="right" class="style69" ><img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}"></p>
      <p align="right" class="style69">Principal Seal & Sign</p>	  </td>
	   <td width="10%" class="css style14 style73" style="line-height:0.1em;">&nbsp;</td>
     </tr>
	 <tr>
       <td colspan="3" ><div align="center"><hr></div></td>
      </tr>     
    </table>
    
    
    
    
    
    </div>
  </div>
</div>



