@php
$setting = Helper::getSetting();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
         .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .header img {
            max-height: 50px;
        }
        .header button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .header button:hover {
            background-color: #0056b3;
        }
        .access-denied img {
            max-width: 100%;
            height: auto;
        }
        .full-height {
            height: 100vh;
        }
      
    </style>
</head>
<body>
      <div class="header">
          <img src="{{env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$setting->left_logo}}" width='40px' alt="Company Logo">
        <button onclick="logout()">Logout</button>
    </div>
    <div class="container d-flex flex-column justify-content-center align-items-center full-height">
        <div class="access-denied text-center">
           <img width='550px' src="{{env('IMAGE_SHOW_PATH').'/default/access deny.png'}}" alt="Access Denied Image" class='mb-3'>
        <p>You do not have permission to view this page.</p>
        
        </div>
    </div>


    <script>
        function logout() {
            window.location.href = 'logout';
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
