<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 Not Found</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #333;
    }

    .container {
      text-align: center;
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }

    h1 {
      font-size: 100px;
      margin: 0;
      color: #ff6347;
    }

    p {
      font-size: 20px;
      margin: 20px 0;
      color: #555;
    }

    .back-btn {
      text-decoration: none;
      background-color: #ff6347;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 18px;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: #ff4500;
    }

    .emoji {
      font-size: 80px;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="emoji">😕</div>
    <h1>404</h1>
    <p>Oops! The page you're looking for doesn't exist.</p>
    <a href="{{ url('/') }}" class="back-btn">Go Back to Home</a>
  </div>

</body>
</html>
