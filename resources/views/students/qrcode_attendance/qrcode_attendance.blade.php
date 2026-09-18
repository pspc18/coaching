
@extends('layout.app')
@section('content')
<!-- Index.html file -->
<!DOCTYPE html>
    <section class="content pt-3">

<div class="content-wrapper">

        <div class="container-fluid">
         <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-calendar-check-o"></i> &nbsp;{{ __('Scan Qr Code') }}</h3>
                            <div class="card-tools">
                               
                            </div>
                        </div>
        <div class="row qrcode-scan">
	    <div class="col-md-4 col-sm-12 mb-md">
	    <div class="form-group box mt-md">
	        <div class="justify-content-md-center" id="reader" width="300px" height="300px"></div>
	        <span class="text-center" id='qr_status'>Scanning</span>
					<div class="radio-custom radio-success radio-inline mt-md">
						<input type="radio" value="1" checked name="attendance_mode" id="in_time">
						<label for="in_time">In Time</label>
					</div>
					<div class="radio-custom radio-success radio-inline mt-md">
						<input type="radio" value="2" name="attendance_mode" id="out_time">
						<label for="out_time">Out Time</label>
					</div>
	    </div>
	    </div>
	  </div>
	  </div>
    </div>
	</div>
    </section>
    
    	<link rel="stylesheet" href="{{ asset('public/assets/school/css/qr-code.css') }}">
<script type="text/javascript" src="{{ asset('public/assets/school/js/qrcode.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/assets/school/js/qrcode_attendance.js') }}"></script>
	<!-- ramom css -->
<audio id="in">
  <source src="{{ asset('public/qrcode_voice/in.mp3') }}" type="audio/ogg">
</audio>
<audio id="out">
  <source src="{{ asset('public/qrcode_voice/out.mp3') }}" type="audio/ogg">
</audio>
<audio id="duplicatePunch">
  <source src="{{ asset('public/qrcode_voice/duplicatePunch.mp3') }}" type="audio/ogg">
</audio>
<script type="text/javascript">
	var setting = jQuery.parseJSON('{"confirmation_popup":"1","auto_late_detect":"1","camera":"environment"}');
	var camera = setting.camera;
	var confirmation_popup = setting.confirmation_popup;

	var statusMatched = "Matched";
	var statusScanning = "Scanning";

	

	var i = document.getElementById("in");
	var ou = document.getElementById("out");
	var dpp = document.getElementById("duplicatePunch");
	var lastResult, modalOpen = 0;
	const html5QrCode = new Html5Qrcode("reader");
const qrCodeSuccessCallback = (decodedText, decodedResult) => {
    if (decodedText !== lastResult && modalOpen === 0) {
        modalOpen = 1; // Block further processing until reset.
startCountdown(5);
        const attendance_mode = $('input[name="attendance_mode"]:checked').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '/qrcode_attendance_save', // Backend URL
            data: { 
                admission_id: decodedText,
                attendance_mode: attendance_mode,
            },
            success: function (res) {
                if (res.status === "in") {
                    console.log("Success: Attendance marked.");
                    i.play(); 
                } else if (res.status === "out") {
                    ou.play(); 
                } if (res.status === "duplicatePunch") {
                    console.log("Duplicate Punch: Attendance already marked.");
                    dpp.play(); // Play duplicate sound.
                } else {
                    console.log("Unexpected Response:", res);
                }

                // Reset modalOpen after 10 seconds
                setTimeout(() => {
                    modalOpen = 0;
                    console.log("modalOpen reset to 0.");
                }, 5000);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);

                // Reset modalOpen after 10 seconds even on error
                setTimeout(() => {
                    modalOpen = 0;
                    console.log("modalOpen reset to 0 after error.");
                }, 5000);
            }
        });

        $("#qr_status").html("Processing..."); // Temporary status display.
        html5QrCode.clear(); // Clear the QR code scanner to avoid duplicates.
    }
};

	const formatsToSupport = [
		Html5QrcodeSupportedFormats.QR_CODE,
	];

	var config = { fps: 50, qrbox: 200 };
	if ($(window).width() <= '400') {
		config = { fps: 100, qrbox: 200 };
	}
	if ($(window).width() <= '370') {
		config = { fps: 100, qrbox: 200 };
	}
	// if you want to prefer front camera
	html5QrCode.start({
		facingMode: camera
		
	}, config, qrCodeSuccessCallback).catch((err) => {
		$("#qr_status").css("background", "red");
		$("#reader").addClass("camera-preview").html("Back camera not found.");
		$("#qr_status").html(err);
		alert('Back camera not found.');
		console.log(err);
	});
function startCountdown(seconds) {
        let remainingTime = seconds;
        $("#qr_status").html(`Processing... ${remainingTime}s`);

        const countdownInterval = setInterval(() => {
            remainingTime--;
            if (remainingTime > 0) {
                $("#qr_status").html(`Processing... ${remainingTime}s`);
            } else {
                clearInterval(countdownInterval);
                $("#qr_status").html(statusScanning); // Revert to scanning status
            }
        }, 1000); // Update every second
    }
</script>

<style>




#my-qr-reader {
    padding: 20px !important;
    border: 1.5px solid #b2b2b2 !important;
    border-radius: 8px;
}

#my-qr-reader img[alt="Info icon"] {
    display: none;
}

#my-qr-reader img[alt="Camera based scan"] {
    width: 100px !important;
    height: 100px !important;
}

button {
    padding: 10px 20px;
    border: 1px solid #b2b2b2;
    outline: none;
    border-radius: 0.25em;
    color: white;
    font-size: 15px;
    cursor: pointer;
    margin-top: 15px;
    margin-bottom: 10px;
    background-color: #008000ad;
    transition: 0.3s background-color;
}

button:hover {
    background-color: #008000;
}

#html5-qrcode-anchor-scan-type-change {
    text-decoration: none !important;
    color: #1d9bf0;
}

video {
    width: 100% !important;
    border: 1px solid #b2b2b2 !important;
    border-radius: 0.25em;
}
#my-qr-reader__scan_region{
  width: 25%  !important;
  min-height: 20px;
  text-align: center;
  position: relative;
}
</style>

</html>

@endsection