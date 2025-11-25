<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title') | Car Rental</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('/images/car-bg.jpg') no-repeat center center/cover;
      backdrop-filter: blur(5px);
      height: 100vh;
    }
    .auth-card {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      padding: 2rem;
      width: 380px;
    }
    .auth-logo {
      font-weight: 700;
      color: #007bff;
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 1.8rem;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0056b3;
    }
    a {
      color: #007bff;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center">
  <div class="auth-card">
    <div class="auth-logo">Car Rental</div>
    @yield('content')
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
