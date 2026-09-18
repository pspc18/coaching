
<head>
<title>Mark Sheet</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!--  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>-->
</head>
@php
$getSetting=Helper::getSetting();
@endphp

<button id="btn" onload="myFunction()" class="btn btn-secondary btn1" style="visibility:hidden">Download</button>
<div class="row" id="capture">
<div class="container border border-dark" style="border-width:3px !important;" id="content">
<table style="width:100%">

    <tr>
        <td style="width:20%"><img style="width: 215px; margin-top:30px; margin-left:50px;" 
        src="data:image/png;base64,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"></td>
        <td class="text-center"><h1><b>{{$getSetting['name'] ?? ''}}</b></h1></td>
       <td style="width:20%"><img style="width: 215px; margin-top:30px; margin-left:50px;" src="data:image/png;base64,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"></td>
    </tr>
</table>

<table style="width:100%;font-size:126%;margin-top: -1%;">
    <tr>
       <td class="text-center" ><p><b>{{$getSetting['address'] ?? ''}}</b></p></td>
    </tr>
    
    <tr>
        <td class="text-center"></td>
    </tr>
   
  

 </table>
  <hr width="100%;" style="border:2px solid black;">

<div class="row">
<div class="col-sm-3 text-left"><b>Name of Student :</b></div>
<div class="col-sm-3 text-left"><b>{{$data['first_name'] ?? ''}} {{$data['Admission']['last_name'] ?? ''}}</b></div>
<div class="col-sm-3 text-left"><b>Class :</b></div>
<div class="col-sm-3 text-left"><b>{{$data['class'] ?? ''}} ({{$data['section'] ?? ''}})</b></div>
</div>
<br>
<div class="row">
<div class="col-sm-3 text-left"><b>Father's Name :</b></div>
<div class="col-sm-3 text-left"><b>{{$data['Admission']['father_name'] ?? ''}}</b></div>
<div class="col-sm-3 text-left"><b>Mobile :</b></div>
<div class="col-sm-3 text-left"><b>9023568942</b></div>
</div>
<br>
<div class="row">
<div class="col-sm-3 text-left"><b>Mother's Name :</b></div>
<div class="col-sm-3 text-left"><b>{{$data['Admission']['mother_name'] ?? ''}}</b></div>
<div class="col-sm-3 text-left"><b>DOB :</b></div>
<div class="col-sm-3 text-left"><b>19/03/2002</b></div>
</div>
<br>
<div class="row">
<div class="col-sm-3 text-left"><b>Address :</b></div>
<div class="col-sm-9 text-left"><b>{{$data['Admission']['address'] ?? ''}}</b></div>
</div>
<br>
<div class="row"style="border-top:2px solid black;">
<div class="col-sm-3 text-left"><b>Exam Name</b></div>
<div class="col-sm-3 text-left"><b>Weeckly Test</b></div>
<div class="col-sm-3 text-left"><b>Date:</b></div>
<div class="col-sm-3 text-left"><b>30/07/2022</b></div>
</div>
<div class="row border-top border-dark" style="border-width:3px !important">
<div class="col-sm-2 text-center border-right border-dark" style="border-width:2px !important"></div>
<div class="col-sm-9 text-center"><b>RESULT</b></div>
</div>

<div class="row  border-dark" style="border-width:2px !important;">
<div class="col-sm-2 text-center border-right border-dark" style="border-width:2px !important"><b>Subject Name</b>
</div>
     <table width="83.2%" >
                                        <tr class="text-center "style="border-top:2px solid black;">
                                            <td style="    border-right: 2px solid black;">Total Question</td>
                                            <td style="    border-right: 2px solid black;">Correct Answer</td>
                                            <td style="    border-right: 2px solid black;">Wrong Answer</td>
                                            <td style="    border-right: 2px solid black;">Skipped Question</td>
                                            <td style="    border-right: 2px solid black;">Result (%)</td>
                                            <td style="    width: 20%;">Time</td>
                                        </tr>
                                                                           
                                    </table>
</div>

<div class="row border-top border-dark" style="border-width:2px !important">
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important;border-bottom:2px solid black;"><b>Notes</b></div>

  <table width="83.2%">
                                         <tr class="text-center" style="border-bottom:2px solid black;">
                                            <td ><small class="badge badge-info w-25">{{ $data['total_ques'] ?? ''}}</small></td>
                                            <td><small class="badge badge-success w-25" style="       margin-left: 100%;">{{ $data['correct_ans'] ?? ''}}</small></td>
                                            <td><small class="badge badge-danger w-25" style="    margin-left: 100%;">{{ $data['wrong_ans'] ?? ''}}</small></td>
                                            <td><small class="badge badge-warning w-25" style="    margin-left: 170%;">{{ $data['skip_ques'] ?? ''}}</small></td>
                                            <td><small class="badge badge-{{  $data['percentage'] <= 45 ? 'danger'   : ''  }}{{  $data['percentage'] > 45 && $data['percentage'] < 75 ? 'warning'   : ''  }}{{  $data['percentage'] >= 75 ? 'success'   : ''  }}  "style="    margin-left: 130%;">{{ $data['percentage'] ?? '' }} %</small></td>
                                            <td ><small class="badge badge-secondary w-50" style="    width: 20% !important;margin-left: 48%;">{{ $data['time'] ?? ''}}</small></td>
                                        </tr>        
                                                                           
                                    </table>
</div>
  
                                    

<br><br><br><br>

<div class="row mt-5" style=" font-size:12px;margin-bottom: 2%;">
<div class="col-sm-6  p-1"><b></b></div>
<div class="col-sm-6 text-right p-1"><img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" style="height: 100px; padding-right: 5%;"></div>
<div class="col-sm-6  p-1"><b style="border-top:2px dotted black;padding-top:2%;">CLASS TEACHER'S SIGNATURE</b></div>
<div class="col-sm-6 text-right p-1"><b style="border-top:2px dotted black;padding-top:2%;">PRINCIPAL'S SIGNATURE & SEAL</b></div>

</div>

</div>
</div>
 <script type="text/javascript">
  
    function myFunction() {

  const captureElement = document.querySelector('#capture')
  html2canvas(captureElement)
    .then(canvas => {
      canvas.style.display = 'none'
      document.body.appendChild(canvas)
      return canvas
    })
    
    
    .then(canvas => {
      const image = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream')
            var pdf = new jsPDF('l', 'mm', [297, 210]);
var width = pdf.internal.pageSize.getWidth();
var height = pdf.internal.pageSize.getHeight();
            pdf.addImage(image, 'JPEG', 0, 0, width, height);

            pdf.save('marksheet.pdf');
            
     
     // const a = document.createElement('a')
      
     // a.setAttribute('download', 'my-pdf.png')
    //  a.setAttribute('href', image)
    //  a.click()
      canvas.remove()
    })
}
window.onload = function(){
  document.getElementById('btn').click();
  var scriptTag = document.createElement("script");
scriptTag.src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js";
document.getElementsByTagName("head")[0].appendChild(scriptTag);
}
const btn = document.querySelector('#btn')
btn.addEventListener('click', myFunction)
</script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js'></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<!--<script type="text/javascript">
window.print();
</script>-->