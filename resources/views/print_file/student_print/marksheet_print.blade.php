
<head>
<title>School | Marksheet </title>
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
    
    <body style="border:1px solid black;">
<style>
  #customers {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
  }

  #customers td,
  #customers th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #customers tr:nth-child(even) {
    background-color:
  }

  #customers tr:hover {
    background-color: #ddd;
  }

  #customers th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: center;
    background-color: #04AA6D;
    color: white;
  }

  table {

    border-collapse: collapse;
    width: 100%;
  }

  .a,
  th {
    border: 1px solid black;
  }

  table {
    border-collapse: collapse;
    width: 100%;
  }
</style>


 @include('print_file.print_header')
  </table>
  <br><br>
  <table>
    <tr style="text-align:left;">
      <td colspan="2" style="padding-left:50px;"><b>Student Name</b></td>
      <td colspan="2"><b>: {{ $data['Admission']['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</b></td>
      <td colspan="2"><b>Roll.No.</b></td>
      <td colspan="2"><b>: {{ $data['admissionNo'] ?? '' }}</b></td>

<tr style="text-align:left;">

      <td colspan="2" style="padding-left:50px;"><b>Father's Name</b></td>
      <td colspan="2">:{{ $data['father_name'] ?? '' }}</td>
      <td colspan="2"><b>DOB</b></td>
      <td colspan="2">: {{ $data['dob'] ?? '' }}</td>

    </tr>


    </tr>
    <tr style="text-align:left;">
      <td colspan="2" style="padding-left:50px;"><b>Mother's Name</b></td>
      <td colspan="2">: {{ $data['mother_name'] ?? '' }}</td>
      <td colspan="2"><b>Mobile. No.</b></td>
      <td colspan="2">:{{ $data['mobile'] ?? '' }}</td>

    </tr>
    

  </table>
  <br><br><br>


  <table id="customers">
    <tr>
      <th>subject</th>
      <th>Half Yearily</th>
      <th>Yearily</th>
      <th>Grade</th>
    </tr>
    <tr style="text-align:center;">
      <td>Hindi</td>
      <td>60</td>
      <td>72</td>
      <td>B</td>


    </tr>
    <tr style="text-align:center;">
      <td>English</td>
      <td>70</td>
      <td>90</td>
      <td>B</td>


    </tr>
    <tr style="text-align:center;">
      <td>Maths</td>
      <td>40</td>
      <td>55</td>
      <td>C</td>


    </tr>
    <tr style="text-align:center;">
      <td>Science</td>
      <td>90</td>
      <td>84</td>
      <td>A</td>


    </tr>
    <tr style="text-align:center;">
      <td>Socal Science</td>
      <td>60</td>
      <td>80</td>
      <td>A</td>


    </tr>
  </table>
  <table>
    <tr>
      <th style="background-color:Tomato;height: 30px;">Attendance</th>
      <th>0/160</th>
      <th style="background-color:Tomato;">Total Mark</th>
      <th>303/306</h>
      <th style="background-color:Tomato;">Percentage</th>
      <th>84.17</th>
      <th style="background-color:Tomato;">Grade</th>
      <th>A</th>
    </tr>


  </table>
  <br><br><br>



  <table style="width:100%">
    <tr>
      <th class="a" style="background-color: #04AA6D;width:50%;text-align:left;">CO-SCHOLASTIC : (3 POINT GRADING
        ScALE A,B,C)</th>
      <th class="a" style="background-color: #04AA6D;width:10%;">Term-I</th>
      <th class="a" style="background-color: #04AA6D;width:10%;">Term-II</th>
    </tr>
    <tr>
      <th style="text-align:left;padding-left:30px;">UNIFORM</th>
      <th>A</th>
      <th></th>
    </tr>
    <tr>
      <th style="text-align:left;padding-left:30px;">ACTIVITIES</th>
      <th>c</th>
      <th></th>
    </tr>
    <tr>
      <th style="text-align:left;padding-left:30px;">DEGITAL CLASS</th>
      <th>B</th>
      <th></th>
    </tr>
    <tr>
      <th style="text-align:left;padding-left:30px;">WRITTENSKILLS</th>
      <th>B</th>
      <th></th>
    </tr>
    <tr>
      <th style="text-align:left;padding-left:30px;">SPEAKING SKILLS</th>
      <th>A</th>
      <th></th>
    </tr>


  </table>
  <br><br>
  <br></br>

  @include('print_file.print_footer')
  <br><br>
</body>

</html>