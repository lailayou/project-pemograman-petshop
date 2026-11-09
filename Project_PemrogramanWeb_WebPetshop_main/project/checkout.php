<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: login.php");
    exit();
}

$provinces = [
    'DI Yogyakarta' => [
        'Kabupaten Sleman' => [
            'Depok' => ['Condong Catur (55283)', 'Caturtunggal (55281)'],
            'Godean' => ['Sidomulyo', 'Sidorejo']
        ],
        'Kota Yogyakarta' => [
            'Gondokusuman' => ['Demangan', 'Sagan'],
            'Jetis' => ['Gowongan', 'Bumijo']
        ]
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $nama_penerima = $_SESSION['nama']; // ambil nama dari session login
    $alamat = $_POST['alamat'];
    $provinsi = $_POST['provinsi'];
    $kota = $_POST['kota'];
    $kecamatan = $_POST['kecamatan'];
    $kelurahan = $_POST['kelurahan'];
    $no_telepon = $_POST['no_telepon'];
    $ekspedisi = $_POST['ekspedisi'];
    $pembayaran = $_POST['pembayaran'];

    $alamat_lengkap = "$alamat, $kelurahan, $kecamatan, $kota, $provinsi";

    // ✅ Tambahkan nama_penerima ke dalam INSERT tanpa mengubah codingan lainnya
    $stmt = $conn->prepare("INSERT INTO pesanan (user_id, nama_penerima, alamat_pengiriman, ekspedisi, pembayaran) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $nama_penerima, $alamat_lengkap, $ekspedisi, $pembayaran);
    $stmt->execute();
    $pesanan_id = $stmt->insert_id;
    $stmt->close();

    foreach ($_SESSION['cart'] as $item) {
        $produk_id = $item['id'];
        $qty = $item['qty'] ?? 1;
        $harga = $item['price'];

        $conn->query("UPDATE produk SET stok = stok - $qty WHERE id = $produk_id");

        $stmt2 = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_satuan, nama_produk) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiiis", $pesanan_id, $produk_id, $qty, $harga, $item['nama']);
        $stmt2->execute();
        $stmt2->close();
    }

    unset($_SESSION['cart']);
    header("Location: status_pesanan.php?pesanan_id=" . $pesanan_id);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .address-container {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
    }
    body {
      padding-bottom: 50px;
    }
    .container.mt-5 {
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

<div id="navbar-placeholder"></div>

<div class="container mt-5">
  <h2>Checkout</h2>

  <form method="POST">
    <div class="address-container">
      <h5>Alamat Pengiriman</h5>

      <div class="mb-3">
        <label for="nama" class="form-label">Nama Penerima</label>
        <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($_SESSION['nama']) ?>" readonly>
      </div>

      <div class="mb-3">
        <label for="alamat" class="form-label">Alamat Lengkap (Nama jalan, Gedung, No Rumah)</label>
        <textarea id="alamat" name="alamat" class="form-control" required rows="3"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="provinsi" class="form-label">Provinsi</label>
          <select id="provinsi" name="provinsi" class="form-select" required>
            <option value="">Pilih Provinsi</option>
            <?php foreach(array_keys($provinces) as $prov): ?>
              <option value="<?= $prov ?>" <?= ($prov == 'DI Yogyakarta') ? 'selected' : '' ?>><?= $prov ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="kota" class="form-label">Kota/Kabupaten</label>
          <select id="kota" name="kota" class="form-select" required disabled>
            <option value="">Pilih Kota</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="kecamatan" class="form-label">Kecamatan</label>
          <select id="kecamatan" name="kecamatan" class="form-select" required disabled>
            <option value="">Pilih Kecamatan</option>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="kelurahan" class="form-label">Kelurahan</label>
          <select id="kelurahan" name="kelurahan" class="form-select" required disabled>
            <option value="">Pilih Kelurahan</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label for="no_telepon" class="form-label">No. HP</label>
        <input type="tel" id="no_telepon" name="no_telepon" class="form-control" value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>" required>
      </div>
    </div>

    <div class="mb-3">
      <label for="ekspedisi" class="form-label">Pilih Ekspedisi</label>
      <select id="ekspedisi" name="ekspedisi" class="form-select" required>
        <option value="">-- Pilih Ekspedisi --</option>
        <option value="JNE">JNE</option>
        <option value="J&T">J&T</option>
        <option value="SiCepat">SiCepat</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="pembayaran" class="form-label">Metode Pembayaran</label>
      <select id="pembayaran" name="pembayaran" class="form-select" required>
        <option value="">-- Transfer Bank --</option>
        <option value="Bank BCA">Bank BCA - 8321311717 (an. budi)</option>
        <option value="Bank BRI">Bank BRI - 319475820492710 (an. budi)</option>
        <option value="Bank Mandiri">Bank Mandiri - 3749265017583 (an. budi)</option>
      </select>
    </div>

    <div class="d-flex justify-content-between">
      <a href="cart.php" class="btn btn-secondary">← Kembali ke Keranjang</a>
      <button type="submit" class="btn btn-success">Lanjutkan Pembayaran</button>
    </div>
  </form>
</div>

<script src="navbarScript.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const provinces = <?= json_encode($provinces) ?>;
  const provinsiSelect = document.getElementById('provinsi');
  const kotaSelect = document.getElementById('kota');
  const kecamatanSelect = document.getElementById('kecamatan');
  const kelurahanSelect = document.getElementById('kelurahan');

  function fillDropdown(select, data, placeholder) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    if (!data) return;
    select.disabled = false;
    data.forEach(item => {
      const option = document.createElement('option');
      option.value = item;
      option.textContent = item;
      select.appendChild(option);
    });
  }

  provinsiSelect.addEventListener('change', function () {
    const selectedProvince = this.value;
    const cities = selectedProvince ? Object.keys(provinces[selectedProvince]) : null;
    fillDropdown(kotaSelect, cities, 'Pilih Kota');
    kecamatanSelect.disabled = true;
    kelurahanSelect.disabled = true;
    kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
    kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
  });

  kotaSelect.addEventListener('change', function () {
    const selectedProvince = provinsiSelect.value;
    const selectedCity = this.value;
    const districts = selectedProvince && selectedCity ?
        Object.keys(provinces[selectedProvince][selectedCity]) : null;
    fillDropdown(kecamatanSelect, districts, 'Pilih Kecamatan');
    kelurahanSelect.disabled = true;
    kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
  });

  kecamatanSelect.addEventListener('change', function () {
    const selectedProvince = provinsiSelect.value;
    const selectedCity = kotaSelect.value;
    const selectedDistrict = this.value;
    const villages = selectedProvince && selectedCity && selectedDistrict ?
        provinces[selectedProvince][selectedCity][selectedDistrict] : null;
    fillDropdown(kelurahanSelect, villages, 'Pilih Kelurahan');
  });

  provinsiSelect.dispatchEvent(new Event('change'));
});
</script>
</body>
</html>