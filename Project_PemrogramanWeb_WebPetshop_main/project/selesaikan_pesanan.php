<?php
include 'config/koneksi.php';
session_start();

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $query = "UPDATE pesanan SET status = 'Selesai' WHERE id = $id";
  if (mysqli_query($conn, $query)) {
    $_SESSION['pesan'] = 'Pesanan telah diselesaikan.';
  } else {
    $_SESSION['pesan_error'] = 'Gagal menyelesaikan pesanan.';
  }
  header("Location: riwayat_pesanan.php");
  exit();
}
?>
