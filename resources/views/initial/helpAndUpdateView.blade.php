
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>

<script>

if (typeof $ === 'undefined') {
    alert('Error: jQuery ($) is not defined \nSomething went wrong, Contact to your service provider!');
}

var softwareTokenNo = @json($softwareTokenNo);

$(document).ready(function() {
    if (navigator.onLine) {
        //alert('You are online');
    } else {
        $('#updates-container').html(`
            <div>
                <h1>No Internet Connection!</h1>
            </div>
        `);
        return false;
    }

    (function() {

        // Capture uncaught errors
        window.onerror = function(message, source, lineno, colno, error) {
            // Alert the error message
            var windowErrorMessage = 'Uncaught Error: ' + message + '\nAt: ' + source + ':' + lineno + ':' + colno;
            //alert('Uncaught Error: ' + message + '\nAt: ' + source + ':' + lineno + ':' + colno);
            $('#updates-container').html(`
                <div>
                    <h1>Error: <pre>${JSON.stringify(windowErrorMessage, null, 4)}</pre></h1>
                </div>
            `);
            // Optionally, you can log the error to the console as well
            console.log('Uncaught Error:', message, 'At:', source, lineno, colno, error);
        };
        
    })();
    
    function showAuthentication() {
        $.ajax({
            url: 'https://web.rusofterp.in/api/helpAndUpdate/' + softwareTokenNo, // Your API endpoint
            method: 'GET',
            success: function(response) {
                // Handle success
                //alert(JSON.stringify(response));
                if (response.status) {
                    // The request was successful (status code 200)
                    sessionStorage.setItem('showAuthentication', true);
                    sessionStorage.setItem('lastVisitDate', new Date().toISOString().split('T')[0]);
                    //alert('OK Developer');
                    localStorage.setItem('updateLength', response.helpAndUpdateContent.length);
                  
                    $('#updates-container').html(`
                        <div>
                            ${response.helpAndUpdateContent.length > 0 ? response.helpAndUpdateContent : 'Something went wrong, Contact to your service provider!'}
                        </div>
                    `);
                    
                    $('#whatsapp_group_link').attr('href', response.data.whatsapp_group_link);
                } else {
                    // Handle other success responses if needed
                    console.error('Unexpected response:', response);
                }
            },
            error: function(jqXHR) {
                // Handle error
                let data = {
                    error: '',
                    status: jqXHR.status,
                    response: jqXHR.responseJSON
                };
                
                if (jqXHR.status >= 400 && jqXHR.status < 500) {
                    // Client error
                    data.error = 'Client error';
                } else if (jqXHR.status >= 500 && jqXHR.status < 600) {
                    // Server error
                    data.error = 'Server error';
                } else {
                    // Other errors (e.g., network issues, DNS resolution failures)
                    data.error = 'Unexpected error';
                }
                
                // Display error in a specific div or redirect to an error page
                console.error(data);
                $('#updates-container').html(`
                    <div>
                        <h1>Error: <pre>${JSON.stringify(data.error, null, 4)}</pre></h1>
                        <h1>Status: <pre>${JSON.stringify(data.status, null, 4)}</pre></h1>
                        <h1>Response: <pre>${JSON.stringify(data.response, null, 4)}</pre></h1>
                    </div>
                `);
            }
        });
    }

    // Trigger the function when the page loads or based on some event
    showAuthentication();
});
</script>

<!-- Add a div to display errors -->
<div id="updates-container">Loading ...</div>

<style>
#updates-container{
    animation: borderInsideGlow 2s infinite;
    padding:10px;
}
@keyframes borderInsideGlow {
    0% {
        box-shadow: inset 0 0 5px #ff0000, inset 0 0 10px #ff0000;
    }
    25% {
        box-shadow: inset 0 0 5px #ff9900, inset 0 0 10px #ff9900;
    }
    50% {
        box-shadow: inset 0 0 5px #33cc33, inset 0 0 10px #33cc33;
    }
    75% {
        box-shadow: inset 0 0 5px #3399ff, inset 0 0 10px #3399ff;
    }
    100% {
        box-shadow: inset 0 0 5px #ff33cc, inset 0 0 10px #ff33cc;
    }
}
</style>