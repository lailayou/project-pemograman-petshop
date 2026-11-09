<?php
function bersihkan_input($data) {
    $data = trim($data); // Hilangkan spasi di awal dan akhir
    $data = stripslashes($data); // Hilangkan backslashes
    $data = htmlspecialchars($data); // Konversi karakter khusus ke HTML entities
    return $data;
}
// Mulai session
session_start();

// Include koneksi database
require 'config/koneksi.php';

// Inisialisasi variabel
$errors = [];
$success = '';

// Proses form jika ada data dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data
    $nama_lengkap = bersihkan_input($_POST['nama_lengkap']);
    $email = bersihkan_input($_POST['email']);
    $kode_telepon = bersihkan_input($_POST['kode_telepon']);
    $nomor_telepon = bersihkan_input($_POST['nomor_telepon']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $alamat = bersihkan_input($_POST['alamat']);

    // Validasi
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid";
    } else {
        // Cek apakah email sudah terdaftar
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "Email sudah terdaftar";
        }
        $stmt->close();
    }
    
    if (empty($nomor_telepon) || !preg_match('/^[0-9]{8,15}$/', $nomor_telepon)) {
        $errors[] = "Nomor telepon tidak valid (min 8-15 angka)";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    if ($password !== $konfirmasi_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }

    // Jika tidak ada error, proses registrasi
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $telepon_lengkap = $kode_telepon . $nomor_telepon;
        
        $stmt = $mysqli->prepare("INSERT INTO users (nama_lengkap, email, password, no_telepon, alamat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama_lengkap, $email, $password_hash, $telepon_lengkap, $alamat);
        
        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
        } else {
            $errors[] = "Terjadi kesalahan saat menyimpan data: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Petshop</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    <style>
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <br><hr>
    <div class="card bg-light">
        <article class="card-body mx-auto" style="max-width: 400px;">
            <h4 class="card-title mt-3 text-center">Buat Akun Petshop</h4>
            <p class="text-center">Daftar untuk mulai berbelanja</p>
            
            <!-- Tampilkan error/success -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php">
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                    </div>
                    <input name="nama_lengkap" class="form-control" placeholder="Nama lengkap" type="text" required
                           value="<?= isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : '' ?>">
                </div>
                
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                    </div>
                    <input name="email" class="form-control" placeholder="Email" type="email" required
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <!-- form-group// -->
    <div class="form-group input-group">
    	<div class="input-group-prepend">
		    <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
		</div>
		
    	<input type="hidden" name="kode_telepon" value="+62">
    <span class="input-group-text">+62</span>
    <input name="nomor_telepon" class="form-control" placeholder="Nomor telepon" type="text" required
           value="<?= isset($_POST['nomor_telepon']) ? htmlspecialchars($_POST['nomor_telepon']) : '' ?>">
</div> <!-- form-group// -->
                
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-map-marker"></i> </span>
                    </div>
                    <input name="alamat" class="form-control" placeholder="Alamat" type="text"
                           value="<?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '' ?>">
                </div>
                
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                    </div>
                    <input name="password" class="form-control" placeholder="Buat password" type="password" required>
                </div>
                
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                    </div>
                    <input name="konfirmasi_password" class="form-control" placeholder="Ulangi password" type="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block"> Daftar Sekarang </button>
                </div>
                
                <p class="text-center">Sudah punya akun? <a href="login.php">Masuk disini</a></p>
            </form>
        </article>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>