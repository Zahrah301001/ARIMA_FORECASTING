<?php
session_start();

if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit(); // Stop execution after redirecting to login
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
  
  <!-- Your Custom Styles -->
  <link rel="stylesheet" href="css/style.css">
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .dashboard {
      background-color: rgba(0, 0, 0, 0.7);
      padding: 20px;
      text-align: justify;
      color: white;
    }
    .dashboard h1, .dashboard h4, .dashboard a {
      color: white;
    }
    .dashboard a {
      font-size: 20px;
      display: block;
      margin-bottom: 10px;
      text-decoration: none; /* Remove underlining */
    }
    .dashboard a.bold {
      font-weight: bold;
    }
    .dashboard .fw-bold {
      color: #FFF200;
      letter-spacing: 0.7px;
    }
    .welcome-text {
      text-align: center;
      margin-bottom: 20px;
      margin-top: 20px;
    }
    .menu {
      text-align: justify;
      margin-bottom: 50px;
      margin-right: 100px;
      margin-left: 100px;
    }
  </style>
</head>
<body>
  <div class="fluid-container">
    <div class="row">
      <div class="col-2">
        <?php include "sidebar.php" ?>
      </div>
      <div class="col-10" style="
        height: 100vh;
        background-color: #4682B4;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;">
        <div class="col-11 dashboard">
          <div class="welcome-text">
            <h1>"SELAMAT DATANG DI APLIKASI</h1>
            <h1>FORECASTING METODE ARIMA"</h1><br>
            <h4 style="color: white;">Panduan Penggunaan Aplikasi :</h4>
          </div>
          <div class="menu">
           <br> <a class="bold">1. Menu Dashboard :</a> <a>Tampilan awal menu aplikasi setelah login.</a>
           <br> <a class="bold">2. Menu Upload Data :</a> <a>Menu yang digunakan untuk menginputkan data, pada menu ini juga dapat mengedit dan mendelete data yang telah diinputkan.</a>
           <br> <a class="bold">3. Menu Forecasting :</a> <a>Terdapat 2 submenu yakni sub menu ACF & PACF yang dapat menampilkan plot ACF dan PACF, kemudian terdapat sub menu ARIMA yang dapat menginputkan model (p,d,q) kemudian akan menampilkan hasil peramalan, nilai MAPE, nilai AIC, dan Grafik perbandingan data aktual dan data peramalan.</a>
            <p class="fw-bold"></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
