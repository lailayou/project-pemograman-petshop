<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$pesanan_result = $conn->query("
  SELECT * FROM pesanan 
  WHERE user_id = $user_id 
  AND status NOT IN ('Selesai', 'Dibatalkan') 
  ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Status Pesanan Aktif</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-3 mb-5">
  <div class="mb-3">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
  </div>

  <h2 class="mb-4">Status Pesanan Anda</h2>

  <?php if (!empty($_SESSION['pesan'])): ?>
    <div class="alert alert-success"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['pesan_error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?></div>
  <?php endif; ?>

  <?php if ($pesanan_result->num_rows === 0): ?>
    <div class="alert alert-info">
      Anda tidak memiliki pesanan aktif saat ini.
      <a href="petFoodBoltCat.php" class="alert-link">Lihat produk kami</a>
    </div>
  <?php endif; ?>

  <?php while ($pesanan = $pesanan_result->fetch_assoc()): ?>
    <?php
    $pesanan_id = $pesanan['id'];

    $items = $conn->query("
      SELECT dp.jumlah, dp.harga_satuan, p.nama AS nama_produk, p.gambar
      FROM detail_pesanan dp
      LEFT JOIN produk p ON dp.id_produk = p.id
      WHERE dp.id_pesanan = $pesanan_id
    ");

    $status = strtolower($pesanan['status']);
    $warna_status = match ($status) {
      'menunggu pembayaran' => 'warning text-dark',
      'dibayar' => 'primary',
      'dikemas' => 'info',
      'dikirim' => 'secondary',
      default => 'dark'
    };

    $status_display = ($pesanan['status'] === 'Dikemas') ? 'Proses Pengiriman' : $pesanan['status'];
    $tanggal_pesanan = !empty($pesanan['created_at']) ? date('d M Y H:i', strtotime($pesanan['created_at'])) : '-';
    ?>

    <div class="card mb-5 shadow-sm">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
          <strong>No. Pesanan:</strong> #<?= $pesanan_id ?> |
          <strong>Tanggal:</strong> <?= $tanggal_pesanan ?>
        </div>
        <div class="d-flex gap-2">
          <?php if (empty($pesanan['bukti_pembayaran'])): ?>
            <a href="batal_pesanan.php?id=<?= $pesanan_id ?>" class="btn btn-outline-danger btn-sm"
               onclick="return confirm('Batalkan pesanan ini?')">
              <i class="fa fa-times me-1"></i> Batalkan
            </a>
          <?php endif; ?>

          <?php if (!empty($pesanan['bukti_pembayaran'])): ?>
            <a href="selesaikan_pesanan.php?id=<?= $pesanan_id ?>" class="btn btn-success btn-sm"
               onclick="return confirm('Tandai pesanan ini sebagai selesai?')">
              <i class="fa fa-check me-1"></i> Pesanan Selesai
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="card-body">
        <p><strong>Nama Penerima:</strong> <?= htmlspecialchars($pesanan['nama_penerima'] ?? '-') ?></p>
        <p><strong>Alamat Pengiriman:</strong> <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'] ?? '-')) ?></p>
        <p><strong>Ekspedisi:</strong> <?= htmlspecialchars($pesanan['ekspedisi']) ?></p>
        <p><strong>Pembayaran:</strong> <?= htmlspecialchars($pesanan['pembayaran']) ?></p>
        <p><strong>Status:</strong> <span class="badge bg-<?= $warna_status ?>"><?= $status_display ?></span></p>

        <div class="table-responsive mt-3">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Produk</th>
                <th>Gambar</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              while ($item = $items->fetch_assoc()):
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $total += $subtotal;
              ?>
              <tr>
                <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                <td><img src="<?= !empty($item['gambar']) ? $item['gambar'] : 'assets/img/noimage.png' ?>" width="80"></td>
                <td><?= $item['jumlah'] ?></td>
                <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
              </tr>
              <?php endwhile; ?>
              <tr class="fw-bold">
                <td colspan="4" class="text-end">Total Harga</td>
                <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
              </tr>
            </tbody>
          </table>
        </div>

        <?php if ($status === 'menunggu pembayaran' && empty($pesanan['bukti_pembayaran'])): ?>
          <a href="upload_bukti.php?order_id=<?= $pesanan_id ?>" class="btn btn-success mt-3">
            <i class="fa fa-upload me-1"></i> Upload Bukti Pembayaran
          </a>
        <?php elseif (!empty($pesanan['bukti_pembayaran'])): ?>
          <p class="mt-3"><strong>Bukti Pembayaran:</strong>
            <a href="<?= $pesanan['bukti_pembayaran'] ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
              Lihat Bukti Pembayaran
            </a>
          </p>
        <?php endif; ?>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
