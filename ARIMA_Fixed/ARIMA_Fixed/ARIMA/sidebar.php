<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .sidebar {
      background-color: #fff;
      height: 100vh;
      width: 18.5%;
      position: fixed;
      top: 0;
      left: 0;
      overflow-y: auto;
      overflow: hidden;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding-bottom: 150px;
    }

    .logo img {
      width: 100px;
      margin-top: 50px;
      margin-bottom: 20px;
    }

    .sidebar .text {
      text-align: center;
      padding-bottom: 30px;
    }

    .sidebar .text p {
      color: black;
      font-weight: bold;
      font-size: 18px;
    }

    .sidebar-menu ul {
      list-style-type: none;
      padding-left: 60px;
      padding-bottom: 150px;
    }

    .sidebar-menu li {
      padding: 10px 20px;
    }

    .sidebar-menu a {
      text-decoration: none;
      color: black;
      font-weight: bold;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      transition: background-color 0.3s, color 0.3s;
    }

    .sidebar-menu a:hover {
      background-color: #f1f1f1;
    }

    .dropdown-container {
      display: none;
      background-color: #f9f9f9;
      padding-left: 10px;
    }

    .dropdown-container a {
      padding: 10px 20px;
      font-weight: normal;
      display: block;
      color: black;
    }

    .dropdown-container a:hover {
      background-color: #e1e1e1;
    }

    .text-danger a {
      color: #dc3545 !important;
    }

    .text-danger a:hover {
      background-color: #f8d7da;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="text-center logo">
      <img src="img/ARIMAAA.png" alt="">
    </div>
    <div class="fw-bold sidebar-menu">
      <div class="text">
        <p>Forecasting Metode ARIMA</p>
      </div>
      <ul class="sidebar-navigation">
        <li>
          <a href="index.php" class="nav-link">
            <i class="bi bi-house-door-fill me-2"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="data.php" class="nav-link">
            <i class="bi bi-briefcase-fill me-2"></i> Upload Data
          </a>
        </li>
        <li>
          <a class="nav-link dropdown-btn" style="cursor: pointer;">
            <i class="bi bi-database-fill me-2"></i> Forecasting
          </a>
          <div class="dropdown-container">
            <a href="acfpacf.php" class="nav-link">ACF & PACF</a>
            <a href="forecast.php" class="nav-link">ARIMA</a>
          </div>
        </li>
        <li class="text-danger">
          <a href="logout.php" class="nav-link">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>

  <script>
    var dropdown = document.getElementsByClassName("dropdown-btn");
    var i;

    for (i = 0; i < dropdown.length; i++) {
      dropdown[i].addEventListener("click", function() {
        var dropdownContent = this.nextElementSibling;
        if (dropdownContent.style.display === "block") {
          dropdownContent.style.display = "none";
        } else {
          dropdownContent.style.display = "block";
        }
      });
    }
  </script>
</body>
</html>
