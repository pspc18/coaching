<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Set Permissions</title>
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-theme.css') }}">
  <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
</head>
<body>
    
  
  <section id="sendOTP" class="">
    <div>
        <img class="connectionimg" src="https://img.freepik.com/free-vector/businessman-holding-pencil-big-complete-checklist-with-tick-marks_1150-35019.jpg?w=900&t=st=1688468680~exp=1688469280~hmac=1927bee76b47682e63a58b66b75e8b68fc34d6345586023ae1994a0c98a42500">
        <h1>Send OTP For Authentication of Sidebar</h1>
        <br>
        <p>For Authentication of Sidebar for New Installation Send the OTP to Adminstrator.</p>
        <br>
        <p><b>Email = skwork91@gmail.com</b></p>
        	<div class="row ">
                	 <div class="col-md-12 text-left">
                					<label style="color:red;" for="branch_code">Registration Request From*</label>
                					<input type="text" class="form-control "  id='domain'name="domain" placeholder="Domain or Firm Name" value="">
                			</div>
                        </div>
        
        <br>
        <button id="sendOtpButton" class="refresh-button"><div class="spinner-border d-none"></div> <div>Send</div></button>
        <br><br>
    </div>
  </section>
  <section id="verifyOTP" class="d-none">
    <div>
        <img class="connectionimg" src="https://img.freepik.com/free-vector/enter-otp-concept-illustration_114360-7867.jpg?w=900&t=st=1688470284~exp=1688470884~hmac=13dc2282ed595279252138860e34228632654c13d8a94658e2541e5afd8a532f">
        <h1>Enter OTP</h1>
        <p>For Verification Fill OTP that was sent to Administrator Email Id.</p>
        
        <div class="otp-form">
            <input type="tel" maxlength="1" class="otp-input" id="otp1" onkeypress="javascript:return isNumber(event)">
            <input type="tel" maxlength="1" class="otp-input" id="otp2" onkeypress="javascript:return isNumber(event)">
            <input type="tel" maxlength="1" class="otp-input" id="otp3" onkeypress="javascript:return isNumber(event)">
            <input type="tel" maxlength="1" class="otp-input" id="otp4" onkeypress="javascript:return isNumber(event)">
            <input type="hidden" id="final-otp" class="final-otp-input" readonly>
        </div>
        <p class="error-message"></p>
        <button id="verifyOtpButton" class="refresh-button">Verify</button>
        <p class="pointer resend pl-5" id="resend"><small><u>Resend</u></small></p>
    </div>
  </section>
<style>
/* Reset default browser styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* Set basic styles */
body {
  font-family: Arial, sans-serif;
  background-color: white;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.refresh-button {
  display: inline-block;
  padding: 12px 24px;
  background-color: #6639b5;
  color: #fff;
  border: 1px solid transparent;
  border-color: #6639b5;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-decoration:none;
}

.refresh-button:hover {
    color: #ff5722;
    background-color: #6639b500;
    border-color: #ff5722;
}

.connectionimg{max-width: 400px;}
.d-none{display:none;}
section{
    position: absolute;
    top: 0;
    width: 100%;
    display: flex;
    height: 100%;
    align-items: center;
    text-align: center;
    justify-content: center;
}

.otp-form {
  display: flex;
  justify-content: center;
}

.otp-input {
  width: 40px;
  height: 40px;
  text-align: center;
  font-size: 24px;
  margin: 0 5px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.otp-input:focus {
  outline: none;
  border-color: #00aaff;
}
.error-message {color: red;}
.pointer{cursor:pointer;}
.resend{text-align: initial;}
</style>

<script>
$(document).ready(function() {
    var received_otp = '';
    $('.otp-input,.final-otp-input').val('');
    $('.otp-input').on('keyup', function(e) {
        var charCode = e.which || e.keyCode;
        if (charCode === 8 && this.value.length === 0) {
          $(this).prev('.otp-input').focus();
        } else if (this.value.length === this.maxLength) {
          $(this).next('.otp-input').focus();
        }
    
        var otp = '';
    
        $('.otp-input').each(function() {
          otp += $(this).val();
        });
    
        $('#final-otp').val(otp);
    });
    
    $("#sendOtpButton,#resend").click(function(){
       
        var domain = $('#domain').val();
        
        if(domain != '')
        {
             $('.spinner-border').removeClass('d-none');
        $('#resend').addClass('d-none');
        var baseurl = "{{ url('/') }}";
		$.ajax({
			url: baseurl + '/setSidebar',
			type: 'POST',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data:{domain:domain},
			success: function(response) {
				if(response.status == 1) {
					$('#verifyOTP,#resend').removeClass('d-none');
					$('#sendOTP').addClass('d-none');
					received_otp = response.otp;
				} else {
					alert("Something Went Wrong Plz Try Again After Refreshing the Page");
					window.location.reload(true);
				}
			},
		});   
        }
        else
        {
            	alert("Domain or Firm Name Required");
        }
    })
    
    $("#verifyOtpButton").click(function(){
        var baseurl = "{{ url('/') }}";
        var final_otp = $('#final-otp').val();
        
        	$.ajax({
			url: baseurl + '/validateOtp',
			type: 'POST',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data:{otp:final_otp},
			success: function(response) {
				if(response.status == 1) {
					window.location.href = 'allowSidebar';
				} else {
					$('.error-message').text('Please enter a valid 4-digit OTP.');
			$('.otp-input,.final-otp-input').val('');
				}
			},
		}); 
        

    })
    
});

    function isNumber(evt){
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
    
         return true;
    }
</script>
</body>
</html>
