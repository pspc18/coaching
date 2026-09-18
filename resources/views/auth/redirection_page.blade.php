<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registration</title>
</head>
<style>
    body {
	display: grid;
	place-content: center;
	height: 100vh;
}

.loading {
	text-align: center;
	width: min-content;
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
	background: linear-gradient(90deg, #fff5, rgba(230, 230, 230, 0.891));
	animation: loading-animation 1.3s infinite;
	border-radius: 1em;
}

@keyframes loading-animation {
	0% {
		left: -50%;
	}

	100% {
		left: 150%;
	}
}

</style>
<body>
    <div class="loading">
    	<p class="loading__text">Loading...</p>
    	<div class="loading__bar"></div>
    </div>
</body>
</html>