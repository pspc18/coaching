
<head>
<title>Bulk Id Card Generate</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <style>
        .page-break {
            page-break-before: always;
        }

        #loader {
    display: none; /* Initially hide loader */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.7); /* Semi-transparent white background */
    z-index: 9999; /* Ensure loader is on top of other content */
}

.loader-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 4px solid #f3f3f3; /* Light gray border */
    border-top: 4px solid #3498db; /* Blue border on top */
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite; /* Rotate animation */
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    </style>
</head>

<body>
    
    <div id="container"></div>
    <div id="loader">
    <div class="loader-inner"></div>
</div>
</body>
<script>
$(document).ready(function() {
    var array = @json($admission_ids);
    var totalRequests = array.length;
    var requestsCompleted = 0;

    // Show loader initially
    $('#loader').show();

    function processArray(index) {
        if (index < array.length) {
            var data = {
                'student_id': array[index],
            };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "GET",
                url: "admissionStudentIdPrint/" + array[index],
                success: function(response) {
                    $('#container').append(response);
                    const pageBreak = $('<div>').addClass('page-break');
                    $('#container').append(pageBreak);
                   
                    requestsCompleted++;
                    setTimeout(function() {
                        processArray(index + 1);
                    }, 1000); // 1000 milliseconds = 1 second
                    if (requestsCompleted === totalRequests) {
                        $('#loader').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", error);
                    requestsCompleted++;
                    setTimeout(function() {
                        processArray(index + 1);
                    }, 1000); // 1000 milliseconds = 1 second

                    if (requestsCompleted === totalRequests) {
                        $('#loader').hide();
                    }
                }
            });
        }
    }
    processArray(0);
});
            </script>
            