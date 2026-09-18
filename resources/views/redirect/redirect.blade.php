<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registration</title>
	
	
    <script type="text/javascript">
        function redirectAfterDelay() {
            setTimeout(function() {
                window.location.href = '{{ url("login") }}';
            }, 3000);
        }
    </script>
@include('layout.message')
</head>
<style>
body {
    display: grid;
    place-content: center;
    height: 100vh;
    overflow:hidden;
}

.loading {
	text-align: center;
	display:flex;
	align-items:center;
	justify-content:center;
}

.loading__text {
	font-weight: 500;
	font-size: 2rem;
}

.loading__bar {
	position: relative;
	height: 5px;
	width: 12rem;
	background-color: rgb(169, 169, 169);
	border-radius: 1em;
	overflow: hidden;
}

.loading__bar::after {
	position: absolute;
	top: 0;
	left: 0;
	content: "";
	width: 50%;
	height: 100%;
	// background-color: rgba(230, 230, 230, 0.891);
	background: linear-gradient(90deg, #ff0909d1, #32c5ff, #6ee809);
	animation: loading-animation 1.3s infinite;
	border-radius: 1em;
}

.redirect_text{
    font-size: 20px;
    font-weight: 500;
    text-transform: capitalize;
}

@keyframes loading-animation {
	0% {
		left: -50%;
	}

	100% {
		left: 150%;
	}
}

.flex_centered{
    display:flex;
    align-items:center;
    justify-content:center;
}

</style>
<body  onload="redirectAfterDelay()">
    <div class="loading">
    	<div>
    	    <p class="redirect_text">Your registration request has been submited successfully</p>
        	<div class="flex_centered"><div class="loading__bar"></div></div>
        	<p class="redirect_text">Please wait while we are redirecting to login.</p>
    	</div>
    </div>
</body>

</html>