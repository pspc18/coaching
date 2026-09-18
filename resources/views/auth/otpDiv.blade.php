@php
$setting= Helper::getSetting();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>OTP Verification</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: #f0f0f0;
    }
    .otp-window {
      width: 390px;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      background-color: #fff;
    }
    .otp-input {
      width: 40px;
      height: 40px;
      font-size: 24px;
      text-align: center;
      margin-right: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .otp-input:last-child {
      margin-right: 0;
    }
    .note {
      font-size: 14px;
      color: #777;
      margin-top: 15px;
    }
    .countdown {
      font-size: 18px;
      margin-top: 10px;
      text-align: center;
    }
    
    .error_message{
        text-transform:capitalize;
        font-size:18px;
        font-weight:600;
    }
    
    #resend_btn{
        color: white;
        padding: 8px;
        border-radius: 4px;
        background: #1f2d3d;
        font-weight: 600;
        letter-spacing: 1.1px;
        cursor:pointer;
    }
    
    .button_back{
        color: white;
        padding: 8px;
        border-radius: 4px;
        font-weight: 600;
        letter-spacing: 1.1px;
        cursor:pointer;
    }

  </style>
</head>
<body>

  <div class="otp-window">
    <h3 class="text-center">OTP Verification</h3>
    <p class="text-center">Enter the OTP sent to your registered email or phone number.</p>
   
      <div class="form-group text-center">
        <input type="text" maxlength="1" class="otp-input" autofocus>
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
      </div>
      
      <button onclick="verifyOtp()" class="btn btn-success btn-block">Verify OTP</button>
    <p class="note text-center">Please enter the 6-digit OTP within <span id="countdown">2:00</span> minutes.</p>
    <p class="text-center mt-3" id="resend_btn" style="display:none;">Resend OTP</p>
    <a href="{{ url('logout') }}"><p class="text-center bg-danger button_back mt-3">Back</p></a>
  </div>
  
<div class="modal fade" id="error_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-header bg-warning">
        <h5 class="modal-title" id="exampleModalLabel">Invalid OTP</h5>
      </div>
      <div class="modal-body">
        <p class="error_message"></p>
      </div>
    </div>
  </div>
</div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
  
  
    document.addEventListener("DOMContentLoaded", function() {
          
      const inputs = document.querySelectorAll(".otp-input");
      const countdownElement = document.getElementById("countdown");
      let timeLeft = 120; // 1 minutes in seconds

      // Function to update the countdown timer
      function updateCountdown() {
        const minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        seconds = seconds < 10 ? `0${seconds}` : seconds;
        countdownElement.textContent = `${minutes}:${seconds}`;
      }

      // Initial call to display the countdown
      updateCountdown();

      // Start countdown timer
      const countdownInterval = setInterval(() => {
        timeLeft--;
        if (timeLeft >= 0) {
          updateCountdown();
        } else {
          clearInterval(countdownInterval);
          // Handle timeout (e.g., display a message or disable inputs)
          countdownElement.textContent = "Time's up!";
          inputs.forEach(input => {
            input.disabled = true; // Disable inputs after timeout
          });
          
          $('#resend_btn').show();
        }
      }, 1000);

      // Event listeners for OTP inputs
      inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
          if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
          }
        });

        input.addEventListener("keydown", (event) => {
          if (event.key === "Backspace" && input.value === "" && index > 0) {
            inputs[index - 1].focus();
          } 
        });
      });
      sendPostRequest();
    });
    
    function sendPostRequest() {
        $('.otp-input').removeAttr('disabled');
           $('#resend_btn').hide();
        var baseUrl = "{{ url('/') }}";
        var clientName = "{{Session::get('first_name')}}";
var serviceName = "Login";
var supportContact = "{{$setting->mobile ?? ''}}";
var companyName = "{{$setting->name ?? ''}}";
var modal = '{{Session::get("role_id") == 3 ? "Admission" : "User"}}'
       var message = `Dear ${clientName},
       
Your One-Time Password (OTP) for ${serviceName} is [#otp]. This code will expire in 2 minutes. If you did not request this OTP, please contact our support team immediately at ${supportContact}.

Best regards,
${companyName}`;
     
        var formData = new FormData();
        formData.append('message_id', '');
        formData.append('id','{{Session::get("id")}}');
        formData.append('message', message);
        formData.append('modal', modal);
        formData.append('service', 'otp');
        formData.append('mobile', '{{Session::get("mobile")}}');
        formData.append('otp', 'request');
               $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: baseUrl + '/sendWhatsapp',
            method: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically transforming the data into a query string
            contentType: false, // Prevent jQuery from overriding the Content-Type header
            success: function(response) {
                if (response.status) {
            
                    
                }
            },
            error: function(error) {
                console.error('Error sending data:', error);
                // Handle error - e.g., display an error message
            }
        });
    }
    
    function verifyOtp() {
        var baseUrl = "{{ url('/') }}";
        
          var otp = '';
                $('.otp-input').each(function() {
                    otp += $(this).val();
                });
              var formData = new FormData();
        formData.append('otp', otp);
               $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: baseUrl + '/validateOtpWhatsapp',
            method: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically transforming the data into a query string
            contentType: false, // Prevent jQuery from overriding the Content-Type header
            success: function(response) {
                if (response.status) {
            
           window.location.href = '/';
                    
                }
                if (!response.status) {
            
              $('.otp-input').each(function() {
                     $(this).val('');
                });
                
                $('.otp-input').eq(0).focus();
                    $('#error_modal').modal('show');
                    $('.error_message').html('❗Otp verification failed please try again later...');
                    
                }
            },
            error: function(error) {
                console.error('Error sending data:', error);
                // Handle error - e.g., display an error message
            }
        });
    }
    
    $(document).ready(function(){
        $('#resend_btn').click(function(){
            sendPostRequest();
            
            const inputs = document.querySelectorAll(".otp-input");
          const countdownElement = document.getElementById("countdown");
          let timeLeft = 120; // 1 minutes in seconds

          // Function to update the countdown timer
          function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            seconds = seconds < 10 ? `0${seconds}` : seconds;
            countdownElement.textContent = `${minutes}:${seconds}`;
          }
    
          // Initial call to display the countdown
          updateCountdown();
    
          // Start countdown timer
          const countdownInterval = setInterval(() => {
            timeLeft--;
            if (timeLeft >= 0) {
              updateCountdown();
            } else {
              clearInterval(countdownInterval);
              // Handle timeout (e.g., display a message or disable inputs)
              countdownElement.textContent = "Time's up!";
              inputs.forEach(input => {
                input.disabled = true; // Disable inputs after timeout
              });
              
              $('#resend_btn').show();
            }
          }, 1000);
        }); 
    });
  </script>
</body>
</html>
