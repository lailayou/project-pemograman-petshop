<?php
require_once 'config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .section-title {
      font-size: 2.5rem;
      font-weight: bold;
    }
    .reservasi-btn {
      background-color: #00cc00;
      color: white;
    }
    .section-img {
      width: 100%;
      border-radius: 10px;
    }
    .price-img {
      width: 100%;
      max-width: 300px;
    }
    </style>
<body>
  <?php include 'navbar.php'; ?>


      <div class="container mt-4 text-center">
        <img src="Assets/ANJING/headeranjing.png" alt="Grooming Dog" class="section-img">
        <div class="row mt-4 justify-content-center">
          <div class="col-md-4">
            <img src="Assets/ANJING/smalldog.png" class="price-img" alt="Small Dog">
          </div>
          <div class="col-md-4">
            <img src="Assets/ANJING/mediumdog.png" class="price-img" alt="Medium Dog">
          </div>
          <div class="col-md-4">
            <img src="Assets/ANJING/largedog.png" class="price-img" alt="Large Dog">
          </div>
        </div>
        <div class="d-flex justify-content-center my-5">
            <a class="btn btn-success fw-bold col-sm-3 py-2" class="btn reservasi-btn mt-3" 
            href="reservasi.php" target="_blank"><i class="fas fa-paw me-2"></i>
            Reservasi</a>
        </div>
      </div>
    
      <div class="container mt-5 text-center">
        <img src="Assets/KUCING/headerkucing.png" alt="Grooming Cat" class="section-img">
        <div class="row mt-4 justify-content-center">
          <div class="col-md-4">
            <img src="Assets/KUCING/basic.png" class="price-img" alt="Basic Grooming">
          </div>
          <div class="col-md-4">
            <img src="Assets/KUCING/medicated.png" class="price-img" alt="Medicated Grooming">
          </div>
          <div class="col-md-4">
            <img src="Assets/KUCING/special.png" class="price-img" alt="Special Grooming">
          </div>
        </div>
        <div class="d-flex justify-content-center my-5">
            <a class="btn btn-success fw-bold col-sm-3 py-2" class="btn reservasi-btn mt-3" 
            href="reservasi.php" target="_blank"><i class="fas fa-paw me-2"></i>
            Reservasi</a>
        </div>
      </div>

  <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>