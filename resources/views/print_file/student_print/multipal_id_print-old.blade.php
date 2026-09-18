@php
$getSetting=Helper::getSetting();
$account=Helper::getQRCode($getSetting->account_id);
@endphp
@php
$getSetting=Helper::getSetting();
$account=Helper::getQRCode($getSetting->account_id);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Cards</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            box-sizing: border-box;
            
            page-break-after: always; /* Ensure page break after each page */
        }
        .id-card {
            width: 95mm;
            height: 59mm;
            border: 1px solid #000;
            box-sizing: border-box;
            /*display: flex;*/
            /*flex-direction: column;*/
            /*justify-content: center;*/
            border-radius: 5px;
        }
        .photo {
            width: 100%;
            height: 30mm;
            background-color: #ccc;
            margin-bottom: 5mm;
        }
        .details {
            font-size: 12px;
        }
        table {
            width: 100%;
            font-size: 11px;
        }
        td {
            text-transform: capitalize;
        }
        .logo_size {
            max-width: 100%;
        }
    
    </style>
</head>
<body>
    @if(!empty($data))
    @php
    $dataArray = $data->toArray();
    $chunkedArray = array_chunk($dataArray, 8);
    $chunkedArray = array_values($chunkedArray);
    @endphp
 
    @foreach($chunkedArray as $key=>$chunk)

        <div class="page">
            <div width='100%' style='display: grid;
          grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(4, 1fr);
            grid-gap: 10mm;'>
            @foreach($chunk as $item)
                <div class="id-card">
                    <table style="line-height:0px;">
                    <thead>
                       	<tr>
                          <td  rowspan="4">
                              <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 60px;height:60px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
                          </td>
                          <td class="school-name" style="font-size:16px;text-align:center;width:100%;"><strong>{{$getSetting->name ?? ''}}</strong></td>
                       </tr>
                       <!--<tr>-->
                       <!--    <td style="font-size: 14px;text-align: center;color: #762423;">-->
                       <!--       ( An English Medium PG to 12th )-->
                       <!--    </td>-->
                       <!--</tr>-->
                    	<tr style="text-align:center;">
                          <td   style="font-size:12px;text-align:center;"><p style="margin: 0px;"><b>Addresse :</b>{{$getSetting->address ?? ''}}</p></td>
                          </tr>
                          <tr>
                          <td  style="font-size:12px; text-align:center;"><p style="margin:0px;"><b>Phone :</b>{{$getSetting->mobile ?? ''}} </p></td>
                        </tr>
                    </thead>
                </table>
                    <table style="line-height: 10px;">
                        <tbody>
                           
                            <tr>
                                <td colspan='3' style='text-align:center;'>
                                    <strong style="margin: 2px;font-size:13px">Identity Card 2024-2025</strong>
                                </td>
                            </tr>
                            <tr>
                                <td width='15%'><strong>Name</strong></td>
                                <td width='60%'>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                <td width='25%' rowspan='5' style='text-align:center'>
                                    <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$item['image'] ?? '' }}"
                                        onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'"
                                        width="70px" height="70px"><br>
                                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}"
                                        onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'"
                                        width="80px" height="25px"><br>
                                    <b>Director</b>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Father</strong></td>
                                <td>{{ $item['father_name'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Class</strong></td>
                                <td>{{ $item['class_name'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>DOB</strong></td>
                                <td>{{ date('d-m-Y', strtotime($item['dob'])) ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone</strong></td>
                                <td>{{ $item['mobile'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Address</strong></td>
                                <td>{{ $item['address'] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
            </div>
        </div>
        <!-- Ensure the page break is applied correctly -->
        <div class="page-break"></div>
    @endforeach
    @endif
</body>
</html>

