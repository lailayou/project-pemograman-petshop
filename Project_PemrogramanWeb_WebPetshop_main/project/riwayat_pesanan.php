<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$selesai_result = $conn->query("SELECT * FROM pesanan WHERE user_id = $user_id AND status = 'Selesai' ORDER BY id DESC");
$dibatalkan_result = $conn->query("SELECT * FROM pesanan WHERE user_id = $user_id AND status = 'Dibatalkan' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Pesanan</title>
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

  <h2 class="mb-4">Riwayat Pesanan</h2>

  <ul class="nav nav-tabs mb-3" id="riwayatTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#tab-selesai" type="button" role="tab">Selesai</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="batal-tab" data-bs-toggle="tab" data-bs-target="#tab-batal" type="button" role="tab">Dibatalkan</button>
    </li>
  </ul>

  <div class="tab-content" id="riwayatTabContent">

    <!-- Tab Pesanan Selesai -->
    <div class="tab-pane fade show active" id="tab-selesai" role="tabpanel">
      <?php if ($selesai_result->num_rows === 0): ?>
        <div class="alert alert-info">Belum ada pesanan yang selesai.</div>
      <?php else: ?>
        <?php while ($pesanan = $selesai_result->fetch_assoc()): ?>
          <?php
            $id_pesanan = $pesanan['id'];
            $tanggal = !empty($pesanan['tanggal']) ? date('d M Y H:i', strtotime($pesanan['tanggal'])) : '-';
            $warna_status = 'success';

            $items = $conn->query("
              SELECT dp.jumlah, dp.harga_satuan,
                     COALESCE(dp.nama_produk, p.nama) AS nama_produk,
                     COALESCE(p.gambar, 'assets/img/noimage.png') AS gambar
              FROM detail_pesanan dp
              LEFT JOIN produk p ON dp.id_produk = p.id
              WHERE dp.id_pesanan = $id_pesanan
            ");
          ?>
          <div class="card mb-5 shadow-sm border-<?= $warna_status ?>">
            <div class="card-header bg-<?= $warna_status ?> text-white d-flex justify-content-between align-items-center">
              <div>
                <strong>No. Pesanan:</strong> #<?= $id_pesanan ?> |
                <strong>Tanggal:</strong> <?= $tanggal ?>
              </div>
              <a href="hapus_pesanan.php?id=<?= $id_pesanan ?>" class="btn btn-outline-light btn-sm" onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                <i class="fa fa-trash me-1"></i> Hapus
              </a>
            </div>
            <div class="card-body">
              <p><strong>Nama Penerima:</strong> <?= htmlspecialchars($pesanan['nama_penerima'] ?? '-') ?></p>
              <p><strong>Alamat:</strong> <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'] ?? '-')) ?></p>
              <p><strong>Ekspedisi:</strong> <?= htmlspecialchars($pesanan['ekspedisi']) ?></p>
              <p><strong>Pembayaran:</strong> <?= htmlspecialchars($pesanan['pembayaran']) ?></p>
              <p><strong>Status:</strong> <span class="badge bg-<?= $warna_status ?>"><?= $pesanan['status'] ?></span></p>

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
                    <?php $total = 0; while ($item = $items->fetch_assoc()):
                      $subtotal = $item['jumlah'] * $item['harga_satuan'];
                      $total += $subtotal;
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                      <td><img src="<?= htmlspecialchars($item['gambar']) ?>" width="80"></td>
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

              <?php if (!empty($pesanan['bukti_pembayaran'])): ?>
                <p class="mt-3"><strong>Bukti Pembayaran:</strong>
                  <a href="<?= htmlspecialchars($pesanan['bukti_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                    Lihat Bukti Pembayaran
                  </a>
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

    <!-- Tab Pesanan Dibatalkan -->
    <div class="tab-pane fade" id="tab-batal" role="tabpanel">
      <?php if ($dibatalkan_result->num_rows === 0): ?>
        <div class="alert alert-warning">Belum ada pesanan yang dibatalkan.</div>
      <?php else: ?>
        <?php while ($pesanan = $dibatalkan_result->fetch_assoc()): ?>
          <?php
            $id_pesanan = $pesanan['id'];
            $tanggal = !empty($pesanan['tanggal']) ? date('d M Y H:i', strtotime($pesanan['tanggal'])) : '-';
            $warna_status = 'danger';

            $items = $conn->query("
              SELECT dp.jumlah, dp.harga_satuan,
                     COALESCE(dp.nama_produk, p.nama) AS nama_produk,
                     COALESCE(p.gambar, 'assets/img/noimage.png') AS gambar
              FROM detail_pesanan dp
              LEFT JOIN produk p ON dp.id_produk = p.id
              WHERE dp.id_pesanan = $id_pesanan
            ");
          ?>
          <div class="card mb-5 shadow-sm border-<?= $warna_status ?>">
            <div class="card-header bg-<?= $warna_status ?> text-white d-flex justify-content-between align-items-center">
              <div>
                <strong>No. Pesanan:</strong> #<?= $id_pesanan ?> |
                <strong>Tanggal:</strong> <?= $tanggal ?>
              </div>
              <a href="hapus_pesanan.php?id=<?= $id_pesanan ?>" class="btn btn-outline-light btn-sm" onclick="return confirm('Hapus pesanan ini secara permanen?')">
                <i class="fa fa-trash me-1"></i> Hapus
              </a>
            </div>
            <div class="card-body">
              <p><strong>Nama Penerima:</strong> <?= htmlspecialchars($pesanan['nama_penerima'] ?? '-') ?></p>
              <p><strong>Alamat:</strong> <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'] ?? '-')) ?></p>
              <p><strong>Ekspedisi:</strong> <?= htmlspecialchars($pesanan['ekspedisi']) ?></p>
              <p><strong>Pembayaran:</strong> <?= htmlspecialchars($pesanan['pembayaran']) ?></p>
              <p><strong>Status:</strong> <span class="badge bg-<?= $warna_status ?>"><?= $pesanan['status'] ?></span></p>

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
                    <?php $total = 0; while ($item = $items->fetch_assoc()):
                      $subtotal = $item['jumlah'] * $item['harga_satuan'];
                      $total += $subtotal;
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                      <td><img src="<?= htmlspecialchars($item['gambar']) ?>" width="80"></td>
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

              <?php if (!empty($pesanan['bukti_pembayaran'])): ?>
                <p class="mt-3"><strong>Bukti Pembayaran:</strong>
                  <a href="<?= htmlspecialchars($pesanan['bukti_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                    Lihat Bukti Pembayaran
                  </a>
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
