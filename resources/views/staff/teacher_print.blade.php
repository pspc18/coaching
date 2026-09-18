<html>
<head>
<style>
table{
width: 100%;
border-collapse:collapse;
border: 1px solid black;
}
table td{line-height:25px;}

</style>

</head>
@php
$getSetting=Helper::getSetting();
@endphp
<body>
    @include('print_file.print_header')
<table border="1">
<tr style="height=40px" style="background-color:white;color:black;text-align:left;font-size:24px; font-weight:600;">
<td style="border-right:0px solid white;"><img style="width:140px;height:40px;" src="https://www.school.rukmanisoftware.com/public/images/header-logo.png"></td>
</tr>



<tr style="border-top:hidden;">

<td style="text-align:center;" colspan='4'><span style="font-size:24px; font-weight:600;">Rukmanisoftware.com</span><br>INDRA VERMA COLONY SHASTRI NEGAR JAIPUR
JAIPUR. RJ (302016)<br><span style="color:blue; font-weight:600;" ><u>www.rukmanisoftware.com</u></span><br>+91 8079094990, 9166697302 <br>
<span style="font-size:22px; font-weight:600;"><u>Salary Slip</u></span>
</td>
</tr>

<tr >
<td style=" border-right:hidden;font-size:17px;"><span style="font-weight:600">Employee Name :</span> Prashant Sharma<br><span style="font-weight:600">Designation :</span > Website Developer<br><span style="font-weight:600">Month :</span> May <br><span style="font-weight:600">Year :</span> 2022</td>

</tr>
<tr>
<th style="width:25%">Earnings</th>
<td style="width:25%"></td>
<th style="width:25%">Deductions</th>
<td style="width:25%"></td>
</tr>
<!-----2 row--->
<tr>
<th>Basic & DA</th>
<td>4,000.000</td>
<th>Provident Fund</th>
<td>00.00</td>
</tr>
<!------3 row---->
<tr>
<th>HRA</th>
<td>0,000.00</td>
<th>E.S.I.</th>
<td>00.00</td>
</tr>
<!------4 row---->
<tr>
<th>Conveyance</th>
<td>000.00</td>
<th>Loan</th>
<td>-</td>
</tr>
<!------5 row---->
<tr>
<th></th>
<td></td>
<th>Profession Tax</th>
<td>-</td>
</tr>
<!------6 row---->
<tr>
<th></th>
<td>Total Working Days = 30</td>
<th>TSD/IT</th>
<td>-</td>
</tr>

<!------7 row---->
<tr>
<th>Total Addition</th>
<td>0,000.00</td>
<th>Total Absent=0/</th>
<td>-</td>
</tr>
<!------8 row---->
<tr>
<th></th>
<td></td>
<th>Total Deduction</th>
<td>-</td>
</tr>
<!------9 row---->
<tr>
<th></th>
<td></td>
<th><span style="font-size:20px; font-weight:600;">Net Salary</span></th>
<td>4,000/-</td>
</tr>
</table>
<table border="1">
<tr><td  style="border-right:hidden;border-bottom:hidden"><span style=" font-weight:600;">In Words : </span> Four Thousand Only.</td></tr>
<tr>
<td style="border-right:hidden;border-bottom:hidden;height:120px;">Date:____________________________</td><td style="border-top:hidden;border-bottom:hidden;text-align:center"><img style="width:140px;height:40px;" src="https://www.school.rukmanisoftware.com/public/images/header-logo.png"></td>
</tr>
<tr>
<td>Signature of the Employee:___________________________________</td><td style="border-left:hidden;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Director :</td>
</tr>
</table>
<script type="text/javascript">
 window.print();
</script>

</body>
</html>