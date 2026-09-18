
@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
table{
    width:100%;
}
.text_set{
    text-align:center;
}
</style>
<div class="container-fluid" id="capture">

<center>
          <h2><b style="font-size: 50px;">{{$getSetting->name ?? ''}}</b></h2>

    </center>
    <div class="row">
    
        <div class="col-md-5">
               <h5><b>MARKS <span> - {{ $data[0]['marks'] ?? '' }}</span></b></h5>

        </div>
        <div class="col-md-2">
                <h5><b>DATE <span> - 
                
                @if(!empty($data[0]->date))
                {{ date('d-m-Y', strtotime($data[0]->date ?? '')) }}
                
                @else
                
                @endif
                </span></b></h5>

        </div>
    </div>
  <table>
    <thead >
      <tr>
        <th class="text_set">Sr.No.</th>
        <th class="text_set">NAME</th>
        <th class="text_set">MARKS</th>
        <th class="text_set">PERCENTAGE</th>
        <th class="text_set">RANK</th>
      </tr>
    </thead>
    <tbody>
    @if(!empty($data))
        @php
            $i=1;
           $total = 0;
        @endphp

        @foreach ($data  as $item)        
      <tr>
        <td class="text_set">{{ $i++ }}</td>
        <td>{{ $item['name'] ?? '' }}</td>
        <td class="text_set">{{ $item['total_marks'] ?? '' }}</td>
        <td class="text_set">{{ $item['percentage'] ?? '' }}%</td>
        <td class="text_set">{{ $item['rank'] ?? '' }} </td>
       
      </tr>
                   
       @endforeach
    @endif
    </tbody>
  </table>
    <br><br>
</div>





  <br><br>
  <div class="row p-2">
   
      <div class="col-md-2 col-6">
            <a id="btn"  title="Lead Invoice"  style="color: white;" class="btn btn-primary  btn-sm" /download><i class="fa fa-download"></i> Download </a>


      </div>
  </div>
  
  
 
 <script type="text/javascript">
  
    function capture() {
  const captureElement = document.querySelector('#capture')
  html2canvas(captureElement)
    .then(canvas => {
      canvas.style.display = 'none'
      document.body.appendChild(canvas)
      return canvas
    })
    .then(canvas => {
      const image = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream')
      const a = document.createElement('a')
      a.setAttribute('download', 'Exam Result.png')
      a.setAttribute('href', image)
      a.click()
      canvas.remove()
    })
}

const btn = document.querySelector('#btn')
btn.addEventListener('click', capture)
</script>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>