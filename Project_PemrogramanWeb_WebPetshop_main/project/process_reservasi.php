<?php
require_once 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan user login
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Anda harus login untuk melakukan reservasi.";
        header("Location: login.php");
        exit();
    }

    $user_id           = $_SESSION['user_id']; // ✅ ambil user ID
    $nama_pemilik      = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $no_telepon        = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $nama_hewan        = mysqli_real_escape_string($conn, trim($_POST['petName'] ?? ''));
    $jenis_hewan       = mysqli_real_escape_string($conn, trim($_POST['petType'] ?? ''));
    $ukuran_hewan      = ($jenis_hewan === 'dog') ? mysqli_real_escape_string($conn, trim($_POST['dogSize'] ?? '-')) : '-';
    $layanan           = mysqli_real_escape_string($conn, trim($_POST['service'] ?? ''));
    $tanggal_reservasi = mysqli_real_escape_string($conn, trim($_POST['reservationDate'] ?? ''));
    $jam_reservasi     = mysqli_real_escape_string($conn, trim($_POST['jam_reservasi'] ?? ''));
    $catatan           = mysqli_real_escape_string($conn, trim($_POST['notes'] ?? ''));
    $status            = 'Pending';

    // Validasi semua field wajib diisi
    if (
        $nama_pemilik && $no_telepon && $nama_hewan && $jenis_hewan &&
        $layanan && $tanggal_reservasi && $jam_reservasi
    ) {
        $sql = "INSERT INTO reservasi 
        (user_id, nama_pemilik, no_telepon, nama_hewan, jenis_hewan, ukuran_hewan, layanan, tanggal_reservasi, jam_reservasi, catatan, status)
        VALUES 
        ('$user_id', '$nama_pemilik', '$no_telepon', '$nama_hewan', '$jenis_hewan', '$ukuran_hewan', '$layanan', '$tanggal_reservasi', '$jam_reservasi', '$catatan', '$status')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "✅ Reservasi berhasil dikirim!";
        } else {
            $_SESSION['error'] = "❌ Gagal menyimpan reservasi: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "❗ Semua field wajib diisi!";
    }

    header("Location: daftar_reservasi.php");
    exit();
} else {
    header("Location: reservasi.php");
    exit();
}
?>
