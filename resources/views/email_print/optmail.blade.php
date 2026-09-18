<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body> 
    <h6>Dear {{ $name ?? '' }}<br>
    Your OTP for login to Mohini Lifestyle portal is   {{ $otp ?? '' }}. Valid for 30 minutes. Please do not share this OTP.
    
    </h6>
     <br> 
    Regards,
    Mohini Lifestyle Team  
</body>
</html>