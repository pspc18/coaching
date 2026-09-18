<!DOCTYPE html>
<html>
<head>
  <style>
    /* CSS styling for the email */
    body {
      font-family: Arial, sans-serif;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
    }

    .content {
      margin-bottom: 20px;
    }

    .otp {
      padding: 10px;
      background-color: #f5f5f5;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-weight: bold;
      text-align: center;
    }

    .footer {
      font-size: 12px;
      color: #999;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>New Software Installation</h2>
    </div>
    <div class="content">
      <p>Dear Administrator,</p>
      <p>A new software installation has been initiated. To proceed with the installation, please use the following One-Time Password (OTP):</p>
      <div class="otp">
        <h1>{{ $otp ?? '' }}</h1>
      </div>
      <p>Please note that this OTP is valid for a single use and will expire after a certain period of time.</p>
    </div>
    <div class="footer">
      <p>This is an automated email. Please do not reply.</p>
    </div>
  </div>
</body>
</html>
