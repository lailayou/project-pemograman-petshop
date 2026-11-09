<?php

// File: config/koneksi.php

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'petshop_db';

// Membuat koneksi
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($mysqli->connect_errno) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Set charset
$mysqli->set_charset("utf8mb4");

// ✅ Tambahan agar bisa digunakan sebagai $conn di file lain
$conn = $mysqli;


function alihkan($url) {
    header("Location: " . $url);
    exit();
}
?>
