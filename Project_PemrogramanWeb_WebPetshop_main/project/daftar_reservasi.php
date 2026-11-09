<?php
require_once 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

// Jika belum login, redirect ke login
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Ambil data reservasi milik user yang sedang login
$reservasi = mysqli_query($conn, "
    SELECT * FROM reservasi 
    WHERE user_id = '$user_id' 
    ORDER BY tanggal_reservasi DESC, id ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Reservasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-list-ul me-2"></i>Daftar Reservasi</h2>
        <a href="pet-gromming.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Pemilik</th>
                        <th>No Telepon</th>
                        <th>Nama Hewan</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th>Layanan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($reservasi) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($reservasi)): ?>
                            <tr>
                                <td class="text-center fw-bold">#<?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_pemilik'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['no_telepon'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['nama_hewan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['jenis_hewan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['ukuran_hewan'] ?? '-') ?></td>
                                <td><?= ucwords(str_replace('_', ' ', htmlspecialchars($row['layanan'] ?? '-'))) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_reservasi'] ?? '-') ?></td>
                                <td>
                                    <?= !empty($row['jam_reservasi']) ? date('H:i', strtotime($row['jam_reservasi'])) : '-' ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= ($row['status'] ?? '') === 'Pending' ? 'bg-warning text-dark' : 'bg-success' ?>">
                                        <?= htmlspecialchars($row['status'] ?? '-') ?>
                                    </span>
                                </td>
                                <td><?= !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">Belum ada data reservasi Anda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS (Optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
