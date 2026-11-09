<?php
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bersihkan input
    $name = bersihkan_input($_POST['name']);
    $email = bersihkan_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    $errors = [];
    
    if (empty($name)) $errors[] = 'Nama lengkap wajib diisi';
    if (empty($email)) $errors[] = 'Email wajib diisi';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
    if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter';
    if ($password !== $confirm_password) $errors[] = 'Konfirmasi password tidak cocok';
    
    // Cek email sudah ada
    $check_email = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        $errors[] = 'Email sudah terdaftar';
    }
    $check_email->close();
    
    // Jika tidak ada error
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert ke database
        $stmt = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            alihkan('dashboard.php');
        } else {
            $errors[] = 'Pendaftaran gagal: ' . $stmt->error;
            $_SESSION['errors'] = $errors;
            alihkan('register.php');
        }
        $stmt->close();
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = [
            'name' => $name,
            'email' => $email
        ];
        alihkan('register.php');
    }
} else {
    alihkan('register.php');
}
?>