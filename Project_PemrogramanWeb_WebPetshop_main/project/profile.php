<?php
session_start();
require 'config/koneksi.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT nama_lengkap, email, no_telepon, alamat, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Proses update
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $mysqli->real_escape_string($_POST['nama_lengkap']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $no_telepon = $mysqli->real_escape_string($_POST['no_telepon']);
    $alamat = $mysqli->real_escape_string($_POST['alamat']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi dasar
    if (empty($nama_lengkap)) $errors[] = "Nama lengkap wajib diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";

    // Validasi password jika diisi
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Password saat ini harus diisi untuk mengubah password";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Password saat ini salah";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Konfirmasi password tidak cocok";
        } elseif (strlen($new_password) < 6) { // Changed from 8 to 6
            $errors[] = "Password baru minimal 6 karakter";
        }
    }

    if (empty($errors)) {
        // Jika password diubah
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, email=?, no_telepon=?, alamat=?, password=? WHERE id=?");
            $stmt->bind_param("sssssi", $nama_lengkap, $email, $no_telepon, $alamat, $hashed_password, $user_id);
        } else {
            // Jika password tidak diubah
            $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, email=?, no_telepon=?, alamat=? WHERE id=?");
            $stmt->bind_param("ssssi", $nama_lengkap, $email, $no_telepon, $alamat, $user_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Profil berhasil diperbarui";
            $_SESSION['nama'] = $nama_lengkap; // Update session
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Gagal memperbarui profil: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<?php include 'navbar.php'; ?>

<!-- Form Profil -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>INFORMASI PRIBADI</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" 
                                   value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. HP</label>
                                <input type="tel" name="no_telepon" class="form-control" 
                                       value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alamat</label>
                                <textarea name="alamat" class="form-control"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <hr>
                        <h5 class="mb-3">Ubah Password</h5>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini">
                            <small class="text-muted">Isi hanya jika ingin mengubah password</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru (minimal 6 karakter)" minlength="6">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password baru" minlength="6">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>