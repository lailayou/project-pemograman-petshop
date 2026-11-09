<?php
session_start();
// Cek user login
$user = isset($_SESSION['user']) ? $_SESSION['user'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <title>PetShop-HapppyPaw</title>
</head>
 <style>
        .category-card {
          background: #fff;
          border-radius: 10px;
          box-shadow: 0 5px 8px rgba(14, 13, 13, 0.1);
          overflow: hidden;
          text-align: center;
          width: 250px;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
      
        .category-card:hover {
          transform: translateY(-10px) scale(1.05);
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .footer a {
    color: #333;
    text-decoration: none;
  }

  .footer a:hover {
    text-decoration: underline;
  }

  .footer h6 {
    color: #000;
  }
  </style>
<body>
  <?php include 'navbar.php'; ?>

  
  <section class="p-0">
    <img src="Assets/Header perkenalan pet shop.png" class="w-100 d-block" alt="">
    <img src="Assets/perkenalan petshop.png" class="w-100 d-block" alt="">
    <img src="Assets/perkenalan produk petshop.png" class="w-100 d-block" alt="">
  </section>
    
    <section class="py-5">
    <div class="container">
      <div class="row justify-content-center text-center">
  
        <div class="col-md-3 mx-2 mb-4">
          <div class="category-card" style="width: 200px;">
            <img src="Assets/pet-food.jpg" class="card-img-top rounded-top" alt="Pet Food" style="height: 150px; object-fit: cover;">
            <div class="card-body bg-white rounded-bottom py-2">
              <p class="fw-bold mb-0 small">Pet Food</p>
            </div>
          </div>
        </div>
  
        <div class="col-md-3 mx-2 mb-4">
          <div class="category-card" style="width: 200px;">
            <img src="Assets/pet-grooming.jpg" class="card-img-top rounded-top" alt="Pet Grooming" style="height: 150px; object-fit: cover;">
            <div class="card-body bg-white rounded-bottom py-2">
              <p class="fw-bold mb-0 small">Pet Grooming</p>
            </div>
          </div>
        </div>
  
        <div class="col-md-3 mx-2 mb-4">
          <div class="category-card" style="width: 200px;">
            <img src="Assets/pet-clinic.jpg" class="card-img-top rounded-top" alt="Pet Clinic" style="height: 150px; object-fit: cover;">
            <div class="card-body bg-white rounded-bottom py-2">
              <p class="fw-bold mb-0 small">Pet Clinic</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php include 'footer.php'; ?>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
