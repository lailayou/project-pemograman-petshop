<?php
function bersihkan_input($data) {
    $data = trim($data); // Hilangkan spasi di awal dan akhir
    $data = stripslashes($data); // Hilangkan backslashes
    $data = htmlspecialchars($data); // Konversi karakter khusus ke HTML entities
    return $data;
}

session_start();

// Koneksi database (gantikan dengan kredensial Anda)
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'petshop_db';

// Buat koneksi
$mysqli = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = bersihkan_input($_POST['email']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if (empty($password)) {
        $errors[] = "Password harus diisi";
    }

    // Jika tidak ada error, proses login
    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT id, nama_lengkap, password FROM users WHERE email = ?");
        if (!$stmt) {
            die("Error dalam persiapan statement: " . $mysqli->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama_lengkap'];
                $_SESSION['email'] = $email;
                
                // Redirect ke halaman setelah login
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Email atau password salah";
            }
        } else {
            $errors[] = "Email atau password salah";
        }
        
        $stmt->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Petshop</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    <style>
        .error { color: red; margin-bottom: 15px; }
        .container { max-width: 400px; margin-top: 50px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card bg-light">
        <article class="card-body">
            <h4 class="card-title text-center">Login Petshop</h4>
            <p class="text-center">Masuk dengan akun Anda</p>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                    </div>
                    <input name="email" class="form-control" placeholder="Email" type="email" required
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                    </div>
                    <input name="password" class="form-control" placeholder="Password" type="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block"> Login </button>
                </div>
                
                <p class="text-center">Belum punya akun? <a href="register.php">Daftar disini</a></p>
            </form>
        </article>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>