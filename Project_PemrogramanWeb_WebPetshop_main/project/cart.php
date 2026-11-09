<?php
session_start();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Proses update jumlah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
  foreach ($_POST['qty'] as $index => $newQty) {
    $_SESSION['cart'][$index]['qty'] = max(1, (int)$newQty); // Minimal 1
  }
  header('Location: cart.php');
  exit();
}

// Proses hapus item
if (isset($_GET['delete'])) {
  $index = $_GET['delete'];
  unset($_SESSION['cart'][$index]);
  $_SESSION['cart'] = array_values($_SESSION['cart']); // Reset index
  header('Location: cart.php');
  exit();
}

$cart = $_SESSION['cart'];
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keranjang Belanja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tambahan agar ikon di navbar muncul -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <h2 class="mb-4">Keranjang Anda</h2>

  <?php if (empty($cart)): ?>
    <p>Keranjang kosong.</p>
  <?php else: ?>
    <form method="POST">
      <table class="table table-bordered align-middle">
        <thead>
          <tr class="table-light text-center">
            <th>Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $grandTotal = 0; ?>
          <?php foreach ($cart as $index => $item): ?>
            <?php
              $qty = $item['qty'] ?? 1;
              $total = $item['price'] * $qty;
              $grandTotal += $total;
            ?>
            <tr>
              <td>
                <img src="<?= $item['image'] ?>" width="50" class="me-2">
                <?= htmlspecialchars($item['name']) ?>
              </td>
              <td class="text-center">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
              <td class="text-center" style="width: 100px;">
                <input type="number" name="qty[<?= $index ?>]" value="<?= $qty ?>" min="1" class="form-control text-center">
              </td>
              <td class="text-center">Rp <?= number_format($total, 0, ',', '.') ?></td>
              <td class="text-center">
                <a href="?delete=<?= $index ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')">
                  <i class="fa fa-trash"></i> Hapus
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <tr class="fw-bold">
            <td colspan="3" class="text-end">Total Belanja</td>
            <td class="text-center">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
            <td></td>
          </tr>
        </tbody>
      </table>
      <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary"><i class="fa fa-arrow-left me-1"></i> Lanjut Belanja</a>
        <div>
          <button type="submit" name="update_qty" class="btn btn-primary me-2"><i class="fa fa-refresh me-1"></i> Update Jumlah</button>
          <a href="checkout.php" class="btn btn-success"><i class="fa fa-credit-card me-1"></i> Checkout</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
