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
            margin: 0 ;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .page {
            width: 54mm;
            height: 86mm;
            padding: 5mm 0mm 0mm 1mm;
            box-sizing: border-box;
        }
        .id-card {
            /* border: 1px solid #000; */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-image: url('{{ asset('/schoolimage/default/ariseinstitute_id_card.jpeg') }}') !important;
        
            width: 56mm;
            height: 86mm; 
            position: relative;
            background-size: contain;
            background-repeat: no-repeat;
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
        .student_img {
            width: 88px;
            height: 90px;
            border-radius: 50%;
            position: absolute;
            top: -89.7px;
            left: 57.4px;
        }
    
    </style>
</head>
<body>

        <div class="page">
            @php
                //dd($data);
            @endphp
            <div class="id-card">
                <p style="position: absolute;top: 173px;left: 4px;width: 22%;text-align: center;
                    font-weight: 500;font-size: 18px;color: black;font-family: auto;letter-spacing: 2px;">
                    <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" />
                </p>
             
                <p style="position: absolute;
                        top: 202px;
                        width: 100%;
                        text-align: center;
                        font-weight: 900;
                        font-size: 7px;
                        color: #cb312e;
                        font-family: sans-serif;
                        letter-spacing: 1px;
                        transform: scale(1.0, 1.1);">
                    {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}
                </p>

                <p style="position: absolute;
                        top: 216px;
                        left: 98px;
                        width: 53%;
                        font-weight: 600;
                        font-size: 7px;
                        color: black;
                        font-family: sans-serif;
                        letter-spacing: 1px;
                        transform: scale(1.0, 1.1);">
                    {{ $data['admissionNo'] ?? '' }} 
                </p>
                
                <p style="position: absolute;
                        top: 229px;
                        left: 98px;
                        width: 59%;
                        font-weight: 600;
                        font-size: 6px;
                        color: black;
                        font-family: sans-serif;
                        ">
                    {{ $data['class_name'] ?? '-' }}
                </p>
                <p style="position: absolute;
                        top: 237px;
                        left: 98px;
                        width: 54%;
                        font-weight: 600;
                        font-size: 8px;
                        color: black;
                        font-family: sans-serif;
                        letter-spacing: 1px;
                        transform: scale(1.0, 1.0);">
                    {{ $data['mobile'] ?? '-' }}
                </p>
                <p style="position: absolute;
                        top: 252px;
                        left: 98px;
                        width: 54%;
                        font-weight: 600;
                        font-size: 6px;
                        color: black;
                        font-family: sans-serif;
                        letter-spacing: 0px;
                        transform: scale(1.0, 1.0);">
                    {{ $data['address'] ?? '-' }}
                </p>
            </div>
        </div>
  
</body>
</html>