
<head>
<title>School | Fees Receipt</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
@php
$getSetting=Helper::getSetting();

@endphp
<body>
    <style>
        
         body {
            
    border: 1px solid;
    }
    .student_img {
 width: 80px; 
 height:100; margin-top: 10%;margin-left:20%;
    padding-bottom: 10px;
        
    }
    
    .row{
        margin-right: 0px;
    }
    
    .table{
       Width:100%;
       border:1px solid black;
    }
    
    .datae{
        margin-top: -7%;
    }
    
    .row1{
        border: 2px solid black;
    }
    
    .row2{
        border: 1px solid black;
    }
    
    .table2{
        width:100%;
    }
    
    .table3{
        width:100%;
        text-align:center;
        border-top:2px solid black;
    }
    
    .td1{width:59%;}
    
    .td2{
        width:24%;
    }
    </style>
@include('print_file.print_header')
<div class="row">
<table class="table1">
<div class="row">
<div class="col-2 border-left"><b>Receipt No</b></div>
<div class="col-3 border-right"><b>: 963</b></div>
<div class="col-2"><b>Name</b></div>
<div class="col-3 border-right"><b>:</b></div>
<div></div>
</div>
<div class="row">
<div class="col-2 border-left"><b>Date</b></div>
<div class="col-3 border-right"><b>: 25 NOV 2018</b></div>
<div class="col-2"><b>Father's Name</b></div>
<div class="col-3 border-right"><b>: </b></div>
</div>
<div class="row">
<div class="col-2 border-left"><b>Class</b></div>
<div class="col-3 border-right"><b>:  </b></div>
<div class="col-2"><b>Mother's Name</b></div>
<div class="col-3 border-right"><b>: </b></div>
</div>
<div class="row">
<div class="col-2"><b>Mobile</b></div>
<div class="col-3 border-right"><b>: </b></div>
<div class="col-2"><b>Fee Month</b></div>
<div class="col-3 border-right"><b>: July</b></div>
</div>
<div class="row">
<div class="col-2"><b></b></div>
<div class="col-3 border-right"></div>
<div class="col-2"></div>
<div class="col-3 border-right"></div>

<div class="col-2 border-left datae"><span class="style73">@if(!empty($data['Admission']['student_img']))

    <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/student_image/'.$data['Admission']['student_img'] ?? '' }}">
    @else
    <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}">
    @endif </span></div>
</div>
</table></div>


</div>
<div class="row bg-light row1">
<div class="col-3 border-left"><b>S. NO.</b></div>
<div class="col-7 border-right text-left"><b>P A R T I C U L A R S</b></div>
<div class="col-2 border-right border-left text-center"><b>Amount</b></div>
</div>
<div class="row row2">
<div class="col-3 border-left">1</div>
<div class="col-7 border-right text-left">{{ $data['FeesType']['name'] ?? '' }}</div>
<div class="col-2 border-right border-left text-center">{{ $data['total_amount'] ?? '' }}</div>
</div>

<table class="table2">
    <tr>
<td rowspan="4 border-right border-top;"style="width: 25%;"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAG1BMVEUAAAD///8iIiL5+fkmJib39/cfHx/y8vI2NjaY7mNSAAAFNklEQVR4nO2d67KbMAyESUjO6fs/cafTwdCstaxsp0DO7k9btvVBMpIvwHSbu3QDkSpB2LrPv/k2zVOX0EdSJai0fkJJm+azEd6LZ1DSJhPuyoQmNOHnEZbWJVqMI2xzBEuEKhLrcKh7XEX0OJaQ/AIxE8LfraK1lQlNaEITmvCihCSaM1+HEaKvxGkCT5rHhOQqzMIIlVanJcRfspKpVlqZ0IQmNKEJr074iG1JPDyIUDEWOkZ9CCHJyExoQhOa0IQmvBAhKm5VCta1d2h0vpwmQ6jknCfOS01oQhOa0IT/hVAweeuatyKFmZRkrtj5dmaUEnCaYJjQhCY0oQl/OiGxRV9TDtbOtbWpiVBp9IaziW1qIhyVhSoy4a5MWC0xYb2kTSbclQmrJRjriglZBW/TnMsU2oSoQFjxbNjoozpiY5jQhH2jj+qIjWFCE/aNPqojNsYPJowBx/mVy2kU1wRnseYBJcUGH+VLlRxD2LnllCoxoQlNaEITXpWQTOSJDdnQJ5v+tZxG4VFQBRtMzYiNskVzAULMVE1oQhOa0IQ/nZAMjjY4SyaEmx6JeSdG3B957FCxQZFNf23fYjRh6oCoItLKhCY0oQlNeFVCpb+2eIhVrWcT0VkFNR4qlQMofnW/c6+PUElHJzBOHTTtPl9qQhOa0IQmPJoQu/m/8RD7JmBETa0V5iLlzfTHEI7KSxVjE5rQhCb8VEKlkRItlA43hI/7X61dLyWo6Va+4lJGg5Kw+fQdfxXm19erF4vxeqO+X42JzbP1OzPkhgutSdr1FRtj1vYl2JyOMLX39IbTJiY0oQlNeBnCGLAS6LEKS5Bw7XGT05CPs4GWaL4J9JAwTFHPa6QunmGgL7pDx1iChCW70FYxcFi8worNIozUGOhTojszRxCS/0+bTBj4YcJ/Wyk2JmyWCQM/3keINak9bVRyl7tES4ysq2tlCr3oGecJr5N9nKRLM3q8P+tEftEMxsm3Cq6EguLWJNArOScao85C2JZnm9CEJjShCSca6LGKGBPC1UYijFfsMU9YSyAHgDl+JdBjFck7YIlgLVnG2q7qM8JM1c6d/SMS6FOTfbK8v7mbRxAqf78UITuIYkITmtCEH0qoBHpFOPgTB5uWwExm61JVGSTe/cfYLUz2KwkDLt3jZH9LOEiZa1+kTPZRqQT1LIRtWbUJTWhCE5pwoqlEipA6NsMifkbKysAaqsEEQnYl/8L5e3yEr+LgG94jTC5s3E3n8TyyMnABwlRJpWcT7smEJgx9NWG9pNLz+QmLDXlXPXFwu6ofTqor02zEeEIVTPbLBJ7EdyzB5YR4jl9ZBxj3rSDlego8bE0JbLBDtDGhCU1oQhNelbDYks16MjjZFEAvpJN7bEYPrTJn9dlmfbyuQDYFVk9rT+cpN4ERCq0WdZ7ML5I2BQ4h7DwvWyQt1Zkw8JWUkFYmzMqEJmS+khLSalHnyXzsmOUAbySEB/dIyMaboBzaxw6n16rs6csc4X6How6aEplw12kTmtCEJtzVwYTFBCOa8nE1Re8khGf2cXMgPrCHD9yxR/CgOV+nGUWINqDUgT2U9Ns+ljB3MsaEJjShCU2ouZgBZHkCJQyfqqudzCeEMMcnCzZkfR6O8JG1gspe/7idGXLr4huknMGTjueRns9GqPy1TGhCE5rQhNVuyPydfFMt7rj61vn9p+pqsbaCEacH4at4cP5eyTIIIXHwDacvyQ0HdWahiky4KxOa0IQm3NXBhLWny0O1OTinjuhXVMbHEqyKWytqdPA3WHBz+4cepeoAAAAASUVORK5CYII=
"style="height:90px;"></td>


<td colspan="3"><b>Payment Mode</b></td>
<td colspan="3"></td>
<td colspan="3"></td>
<td colspan="3"><b>: Cash</b></td>




    
</tr>
<tr>
   
<td colspan="3"><b>Total Paid Amount</b></td>
<td colspan="3"style="width:145px;"></td>
<td colspan="3"></td>
<td colspan="3" ><b>:</b> <b id="inNumber">{{ $data->amount ?? '' }}</b></td>



    
</tr>

</table>

<table class="table3">

<tr>
    
  
<td class="td1"><b>Amount In Word</b></td>
<td class="td2"></td>
<td><b>: Five thousand Only</b></td>

</tr>

</table>

</body>

</html>
<script>
var th_val = ['', 'thousand', 'Lakh', 'Crore', 'trillion'];
// System for uncomment this line for Number of English 
// var th_val = ['','thousand','million', 'milliard','billion'];
 
var dg_val = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
var tn_val = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
var tw_val = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
function toWordsconver(s) {
  s = s.toString();
    s = s.replace(/[\, ]/g, '');
    if (s != parseFloat(s))
        return 'not a number ';
    var x_val = s.indexOf('.');
    if (x_val == -1)
        x_val = s.length;
    if (x_val > 15)
        return 'too big';
    var n_val = s.split('');
    var str_val = '';
    var sk_val = 0;
    for (var i = 0; i < x_val; i++) {
        if ((x_val - i) % 3 == 2) {
            if (n_val[i] == '1') {
                str_val += tn_val[Number(n_val[i + 1])] + ' ';
                i++;
                sk_val = 1;
            } else if (n_val[i] != 0) {
                str_val += tw_val[n_val[i] - 2] + ' ';
                sk_val = 1;
            }
        } else if (n_val[i] != 0) {
            str_val += dg_val[n_val[i]] + ' ';
            if ((x_val - i) % 3 == 0)
                str_val += 'Hundred ';
            sk_val = 1;
        }
        if ((x_val - i) % 3 == 1) {
            if (sk_val)
                str_val += th_val[(x_val - i - 1) / 3] + ' ';
            sk_val = 0;
        }
    }
    if (x_val != s.length) {
        var y_val = s.length;
        str_val += 'point ';
        for (var i = x_val + 1; i < y_val; i++)
            str_val += dg_val[n_val[i]] + ' ';
    }
    return str_val.replace(/\s+/g, ' ');
}

var number = document.getElementById( "inNumber" ).innerText;
  var b = parseInt(number);
 var Inwords = toWordsconver(b);
 
 document.getElementById( "inWords" ).innerText=Inwords+ " " + "Only" ;

//alert(Inwords);
</script>
@include('print_file.print_footer')

<script type="text/javascript">
window.print();
</script>