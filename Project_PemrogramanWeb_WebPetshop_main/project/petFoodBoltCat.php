<?php
session_start();

// Include koneksi (wajib ada dan benar path-nya)
include 'config/koneksi.php';

// Pastikan koneksi berhasil
if (!isset($conn)) {
    die("Koneksi tidak tersedia.");
}

// Query ambil produk dari DB
$query = "SELECT * FROM produk WHERE nama LIKE '%BOLT%'";
$result = $conn->query($query);

// Cek jika query error
if (!$result) {
    die("Query gagal: " . $conn->error);
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>PetFood BoltCat - HappyPaw</title>
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <!-- ✅ NAVBAR -->
  <?php include 'navbar.php'; ?>

    <!-- ✅ Carousel -->
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="./Assets/Pet shop product/Foto header web bagian petshop.png" class="d-block w-100" alt="Banner Produk">
      </div>
    </div>
  </div>

  <!-- ✅ SECTION PRODUK -->
  <div class="container py-5">
    <h2 class="mb-4 text-center">Featured Product</h2>
    <div class="row justify-content-center g-4">

  <!-- ✅ Search -->
  <div class="d-flex justify-content-end mt-3 px-3">
    <form class="d-flex col-sm-6" role="search">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
  </div>

      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-5">
          <div class="card shadow-sm">
            <img src="<?= htmlspecialchars($row['gambar']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['nama']) ?></h5>
              <p class="card-text text-primary fw-bold">Rp. <?= number_format($row['harga'], 0, ',', '.') ?></p>
              <p class="text-muted">Stok: <?= $row['stok'] ?></p>

              <?php if ($row['stok'] > 0): ?>
                <form method="POST" action="add_to_cart.php">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="name" value="<?= htmlspecialchars($row['nama']) ?>">
                  <input type="hidden" name="price" value="<?= $row['harga'] ?>">
                  <input type="hidden" name="image" value="<?= htmlspecialchars($row['gambar']) ?>">
                  <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fa-solid fa-cart-shopping me-2"></i> Tambah ke Keranjang
                  </button>
                </form>
              <?php else: ?>
                <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>

    </div>
  </div>

    <!-- ✅ Footer -->
  <footer class="footer py-5" style="background-color: #a4cea6;">
    <div class="container">
      <div class="row d-flex justify-content-between align-items-start text-start">

        <!-- Logo dan Deskripsi -->
        <div class="col-md-4 mb-2">
          <img src="Assets/happypaw.png" alt="Happy Paw" width="150" class="mb-4">
          <p style="font-size: 15px; color: #333;">
            Ayo bergabung dengan ribuan pecinta hewan lainnya yang sudah mempercayakan kebutuhan peliharaan mereka kepada HappyPaw!
          </p>
        </div>

        <!-- Hubungi Kami -->
        <div class="col-md-4 mb-4">
          <h4 class="fw-bold mb-3">Hubungi Kami</h4>
          <ul class="list-unstyled" style="font-size: 15px;">
            <li>Email: <a href="mailto:happypawindonesia@gmail.com">happypawindonesia@gmail.com</a></li>
            <li>Telepon: 022-2006880</li>
            <li>WhatsApp: 087800042548</li>
          </ul>
        </div>

        <!-- Toko Kami -->
        <div class="col-md-3 mb-4">
          <h4 class="fw-bold mb-3">Toko Kami</h4>
          <a href="https://shopee.co.id/happypawstore" target="_blank">
            <img src="Assets/shopeelogo.png" width="150px" class="mb-3" alt="Shopee Store">
          </a>
        </div>

  <!-- ✅ JS Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
