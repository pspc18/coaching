<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">    
<style>
    table{
       font-size: 14px; 
    }
</style>
</head>
<body>
    @php
        $getSetting=Helper::getSetting();
    @endphp
        <table style="border-bottom: 2px solid;font-size: 14px;">
			<tbody>
					<tr>
      <td  rowspan="4">
          <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'"></td>
  
      <td   style=" font-size:18px;text-align:center;width:100%;"><span class="style71"><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
   
      <td rowspan="4"> 
      <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'"></td>
     
    
     
   </tr>
	<tr style="text-align:center;">
       
 
   
      <td   style="font-size:14px;text-align:center;"><p style="margin-bottom:-0.5%;"><b >{{$getSetting['address'] ?? ''}}</b></p></td>

      </tr>
      <tr>
     
   
      <td  style="font-size:14px; text-align:center;"><p style="margin-bottom:-0.5%;"><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}  </p></td>
    </tr>
   	<tr style="text-align:center;">
       

   
      <td   style="font-size:14px;text-align:center;"><b>www.rukmanisoftware.com</b></td>

      </tr>

    
  </tbody></table> 
    <table class="table table-bordered" style="font-size: 14px;">
         <tr role="row">
                        <th>{{ __('messages.Sr.No.') }}</th>
                        <th>{{ __('library.catname') }}</th>
                        <th>{{ __('library.Book Name') }}</th>
                        <th>Scan Bar Code</th>
                      </tr>
                    @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['catname'] ?? '' }}</td>
                                <td>{{ $item['name'] ?? '' }}</td>
                                @php
                                $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                                @endphp
                                <td class="text-center"> 
                                    @if(!empty($item->book_code))
                                        <img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($item['book_code'], $generatorPNG::TYPE_CODE_128)) }}"><br><small>{{ $item['book_code'] ?? '' }}</small>
                                    @endif
                                </td>
                        </tr>
                   @endforeach
                @endif
    </table>
  
</body>
</html>