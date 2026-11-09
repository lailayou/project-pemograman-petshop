<?php
// reservasi.php
require_once 'config/koneksi.php';

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil pesan dari session
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;

// Hapus pesan setelah ditampilkan
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reservasi Layanan Petshop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        body {
            background-color: var(--light-color);
            font-family: 'Nunito', sans-serif;
        }
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        .form-section {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            border-left: 4px solid var(--primary-color);
        }
        .service-option {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: var(--light-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .service-option:hover {
            background-color: #e9ecef;
        }
        .size-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            background-color: var(--secondary-color);
            color: white;
        }
        #dogSizeSection {
            display: none;
        }
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold"><i class="fas fa-paw me-2"></i>HappyPaw Petshop</h1>
            <p class="lead">Layanan profesional untuk hewan kesayangan Anda</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">

            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Reservasi Layanan</h2>

                    <div class="card shadow mb-5">
                        <div class="card-body">
                            <form id="reservationForm" method="POST" action="process_reservasi.php">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nama Pemilik</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="petName" class="form-label">Nama Hewan</label>
                                        <input type="text" class="form-control" id="petName" name="petName" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="petType" class="form-label">Jenis Hewan</label>
                                        <select class="form-select" id="petType" name="petType" required>
                                            <option value="" disabled selected>Pilih Jenis Hewan</option>
                                            <option value="dog">Anjing</option>
                                            <option value="cat">Kucing</option>
                                            <option value="other">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3" id="dogSizeSection">
                                    <label class="form-label">Ukuran Anjing</label>
                                    <div class="service-option">
                                        <input class="form-check-input" type="radio" name="dogSize" id="small" value="small" checked>
                                        <label class="form-check-label" for="small">
                                            <span class="size-badge me-2">Small</span> ≤ 10 kg (Chihuahua, Pomeranian)
                                        </label>
                                    </div>
                                    <div class="service-option">
                                        <input class="form-check-input" type="radio" name="dogSize" id="medium" value="medium">
                                        <label class="form-check-label" for="medium">
                                            <span class="size-badge me-2">Medium</span> 11–25 kg (Beagle, Bulldog)
                                        </label>
                                    </div>
                                    <div class="service-option">
                                        <input class="form-check-input" type="radio" name="dogSize" id="large" value="large">
                                        <label class="form-check-label" for="large">
                                            <span class="size-badge me-2">Large</span> ≥ 26 kg (Labrador, German Shepherd)
                                        </label>
                                    </div>
                                </div>
                                

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="service" class="form-label">Layanan</label>
                                        <select class="form-select" id="service" name="service" required>
                                            <option value="" disabled selected>Pilih Layanan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="reservationDate" class="form-label">Tanggal Reservasi</label>
                                        <input type="date" class="form-control" id="reservationDate" name="reservationDate" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="jam_reservasi" class="form-label">Jam Reservasi</label>
                                    <select class="form-select" id="jam_reservasi" name="jam_reservasi" required>
                                        <option value="" disabled selected>Pilih Jam</option>
                                        <option value="09:00">09:00 Pagi</option>
                                        <option value="13:00">13:00 Siang</option>
                                        <option value="16:00">16:00 Sore</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan Tambahan</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Reservasi
                                    </button>
                                    <a href="https://wa.me/6282220271967?text=Halo%20Petshop,%20saya%20ingin%20bertanya%20tentang%20reservasi." 
                                       class="btn btn-success" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>Tanya via WA
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Services -->
                <div class="col-lg-4">
                    <h2 class="mb-4"><i class="fas fa-info-circle me-2"></i>Layanan Kami</h2>
                    <div class="card service-card shadow mb-3">
                        <div class="card-body text-center">
                            <i class="fas fa-bath fa-2x text-primary"></i>
                            <h5 class="mt-2">Grooming</h5>
                            <p class="text-muted">Mandikan dan rapikan hewan kesayangan Anda dengan layanan profesional kami.</p>
                        </div>
                    </div>
                    <div class="card service-card shadow mb-3">
                        <div class="card-body text-center">
                            <i class="fas fa-user-md fa-2x text-success"></i>
                            <h5 class="mt-2">Konsultasi Dokter</h5>
                            <p class="text-muted">Tim dokter hewan kami siap membantu masalah kesehatan hewan peliharaan Anda.</p>
                        </div>
                    </div>
                    <div class="card service-card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-graduation-cap fa-2x text-info"></i>
                            <h5 class="mt-2">Pelatihan</h5>
                            <p class="text-muted">Program pelatihan untuk membuat hewan peliharaan Anda lebih patuh dan terlatih.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript Section -->
    <script>
        const layanan = {
            dog: [
                { value: 'grooming', text: 'Grooming Anjing' },
                { value: 'dog_training', text: 'Pelatihan Anjing' },
                { value: 'dog_vet', text: 'Konsultasi Dokter Anjing' }
            ],
            cat: [
                { value: 'grooming_basic', text: 'Basic Grooming Kucing' },
                { value: 'grooming_medicated', text: 'Medicated Grooming Kucing' },
                { value: 'grooming_special', text: 'Special Grooming Kucing' },
                { value: 'cat_vet', text: 'Konsultasi Dokter Kucing' }
            ],
            other: [
                { value: 'general_checkup', text: 'Pemeriksaan Umum' }
            ]
        };

        const petTypeSelect = document.getElementById('petType');
        const serviceSelect = document.getElementById('service');
        const dogSizeSection = document.getElementById('dogSizeSection');
        const dateInput = document.getElementById('reservationDate');

        petTypeSelect.addEventListener('change', function () {
            const selected = this.value;
            dogSizeSection.style.display = selected === 'dog' ? 'block' : 'none';
            serviceSelect.innerHTML = '<option value="" disabled selected>Pilih Layanan</option>';
            if (layanan[selected]) {
                layanan[selected].forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.value;
                    opt.textContent = item.text;
                    serviceSelect.appendChild(opt);
                });
            }
        });

        dateInput.min = new Date().toISOString().split('T')[0];
    </script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
