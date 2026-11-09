<?php
session_start();
include 'config/koneksi.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
  $pesanan_id = intval($_GET['id']);
  $user_id = $_SESSION['user_id'];

  $conn->query("DELETE FROM pesanan WHERE id = $pesanan_id AND user_id = $user_id AND status IN ('Selesai', 'Dibatalkan')");
}

header("Location: riwayat_pesanan.php");
exit();
?>
