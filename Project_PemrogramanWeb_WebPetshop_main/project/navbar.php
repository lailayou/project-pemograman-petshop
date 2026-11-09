<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hitung jumlah item di keranjang
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['qty'] ?? 1;
    }
}

// Fungsi untuk aktifkan menu
function active($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="Assets/logo.png" alt="HappyPaw Logo" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- Menu Tengah -->
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item fw-bold px-3">
                    <a class="nav-link <?= active('index.php') ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown fw-bold px-5">
                    <a class="nav-link dropdown-toggle <?= active('petFoodBoltCat.php') ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Pet Food
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="petFoodBoltCat.php">Bolt Cat</a></li>
                        <li><a class="dropdown-item" href="petFoodMeo.php">Me-O</a></li>
                        <li><a class="dropdown-item" href="petFoodCoyal.php">Royal Canin</a></li>
                        <li><a class="dropdown-item" href="petFoodWhiskes.php">Whiskas</a></li>
                    </ul>
                </li>
                <li class="nav-item fw-bold px-5">
                    <a class="nav-link <?= active('pet-gromming.php') ?>" href="pet-gromming.php">Pet Grooming</a>
                </li>
                <li class="nav-item fw-bold px-5">
                    <a class="nav-link <?= active('pet-clinic.php') ?>" href="pet-clinic.php">Pet Clinic</a>
                </li>
            </ul>

            <!-- Menu Kanan -->
            <div class="d-flex align-items-center">

                <!-- Keranjang -->
                <div class="dropdown me-4">
                    <a href="#" class="position-relative text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-cart-shopping fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart_count ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-3" style="width: 300px;">
                        <li class="text-center fw-bold">Keranjang</li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <li class="mb-2 small d-flex justify-content-between">
                                    <span><?= htmlspecialchars($item['name']) ?> x<?= $item['qty'] ?? 1 ?></span>
                                    <span>Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-center py-2 text-muted">Keranjang kosong</li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li class="text-center">
                            <a href="cart.php" class="btn btn-primary btn-sm">Lihat Keranjang</a>
                        </li>
                    </ul>
                </div>

                <!-- User Login -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="Assets/default-profile.jpg" alt="Profile" width="32" height="32" class="rounded-circle me-2">
                            <span class="fw-semibold"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="riwayat_pesanan.php"><i class="fas fa-box me-2"></i> Riwayat Pesanan</a></li>
                            <li><a class="dropdown-item" href="status_pesanan.php"><i class="fas fa-truck me-2"></i> Status Pesanan</a></li>
                            <li><a class="dropdown-item" href="daftar_reservasi.php"><i class="fas fa-book me-2"></i> Reservasi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex">
                        <a href="login.php" class="btn btn-outline-primary fw-semibold me-2">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                        </a>
                        <a href="register.php" class="btn btn-primary fw-semibold">
                            <i class="fa-solid fa-user-plus me-1"></i> Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
