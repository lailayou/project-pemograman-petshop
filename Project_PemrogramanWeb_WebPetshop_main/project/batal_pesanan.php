<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $_SESSION['pesan_error'] = 'Akses tidak valid.';
  header('Location: status_pesanan.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$pesanan_id = intval($_GET['id']);

// Ambil pesanan milik user
$query = $conn->query("SELECT * FROM pesanan WHERE id = $pesanan_id AND user_id = $user_id");

if ($query && $query->num_rows > 0) {
  $pesanan = $query->fetch_assoc();

  // Hanya bisa dibatalkan jika belum bayar (belum upload bukti)
  if (empty($pesanan['bukti_pembayaran'])) {
    $update = $conn->query("UPDATE pesanan SET status = 'Dibatalkan' WHERE id = $pesanan_id");

    if ($update) {
      $_SESSION['pesan'] = 'Pesanan berhasil dibatalkan.';
      // ✅ Redirect ke halaman riwayat agar langsung terlihat
      header("Location: riwayat_pesanan.php");
    } else {
      $_SESSION['pesan_error'] = 'Gagal membatalkan pesanan.';
      header("Location: status_pesanan.php");
    }
  } else {
    $_SESSION['pesan_error'] = 'Pesanan sudah dibayar dan tidak bisa dibatalkan.';
    header("Location: status_pesanan.php");
  }
} else {
  $_SESSION['pesan_error'] = 'Pesanan tidak ditemukan atau bukan milik Anda.';
  header("Location: status_pesanan.php");
}
exit();
