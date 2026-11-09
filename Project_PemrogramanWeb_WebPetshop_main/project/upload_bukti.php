<?php
session_start();
require 'config/koneksi.php';

$order_id = $_GET['order_id'] ?? 0;

// Validasi order ID
if (!$order_id) {
    echo "<div class='alert alert-danger'>ID pesanan tidak ditemukan.</div>";
    exit;
}

// Proses upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_tmp = $_FILES["bukti"]["tmp_name"];
    $file_name = basename($_FILES["bukti"]["name"]);
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array(strtolower($file_ext), $allowed_ext)) {
        echo "<div class='alert alert-danger'>Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.</div>";
    } else {
        $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $file_name);
        $target_file = $target_dir . $new_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            // Simpan ke database
            $stmt = $conn->prepare("UPDATE pesanan SET bukti_pembayaran = ?, status = 'Dikemas' WHERE id = ?");
            $stmt->bind_param("si", $target_file, $order_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>✅ Bukti pembayaran berhasil diupload. Pesanan Anda akan segera dikemas.</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Gagal menyimpan ke database: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>❌ Upload gagal. Coba lagi.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Upload Bukti Pembayaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-4">Upload Bukti Pembayaran</h4>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="bukti" class="form-label">Pilih Gambar Bukti Pembayaran</label>
          <input type="file" name="bukti" id="bukti" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">
          <i class="fa fa-upload me-1"></i> Upload Bukti
        </button>
        <a href="status_pesanan.php" class="btn btn-secondary ms-2">Kembali</a>
      </form>
    </div>
  </div>
</div>

</body>
</html>
