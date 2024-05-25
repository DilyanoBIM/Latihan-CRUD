<?php
session_start();
include 'config.php';

// Ambil nilai parameter 'nama' dari URL
$nama_ruangan = $_GET['nama'] ?? '';

// Ambil data model perangkat dari database
$query_model = "SELECT * FROM model";
$result_model = $koneksi->query($query_model);

// Ambil data merk perangkat dari database
$query_merk = "SELECT * FROM merk";
$result_merk = $koneksi->query($query_merk);

// Periksa apakah query berhasil dijalankan
if (!$result_model || !$result_merk) {
    die("Query error: " . $koneksi->error);
}

// Tangkap data jika ada pengiriman formulir penambahan perangkat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $merk_perangkat = $_POST['merk_perangkat'];
    $model_perangkat = $_POST['model_perangkat'];
    $jumlah_perangkat = $_POST['jumlah_perangkat'];
    $keterangan_perangkat = $_POST['keterangan_perangkat'];

    // Lakukan validasi atau operasi penyimpanan ke database sesuai kebutuhan
    // Misalnya, lakukan validasi dan simpan data ke dalam tabel perangkat_jaringan
    $query_insert_perangkat = "INSERT IGNORE INTO perangkat_jaringan ( id_ruangan, id_model, id_merk, jumlah, keterangan_perangkat)
                           VALUES ((SELECT id_ruangan FROM ruangan WHERE nama_ruangan = '$nama_ruangan'), $model_perangkat, $merk_perangkat, $jumlah_perangkat, '$keterangan_perangkat')";
$result_insert_perangkat = $koneksi->query($query_insert_perangkat);

if ($result_insert_perangkat) {
    echo "<div class='alert alert-success mt-3' role='alert'>
            Perangkat berhasil ditambahkan!
          </div>";

    // Tambahkan skrip JavaScript untuk redirect setelah pesan sukses ditampilkan
    echo "<script>
            setTimeout(function() {
                window.location.href = 'ruangan.php?nama=" . urlencode($nama_ruangan) . "';
            }, 500); // Redirect setelah 2 detik (2000 milidetik)
          </script>";
} else {
    echo "<div class='alert alert-danger mt-3' role='alert'>
            Gagal menambahkan perangkat: " . $koneksi->error . "
          </div>";
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Perangkat</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0; /* Ganti dengan kode warna yang diinginkan */
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Tambah Perangkat ke Ruangan: <?php echo htmlspecialchars($nama_ruangan); ?></h1>

    <!-- Formulir Tambah Perangkat -->
    <form method="POST" action="">
        <div class="mb-3">
                <label for="model_perangkat" class="form-label">Model Perangkat:</label>
                <select class="form-select" name="model_perangkat" required>
                    <?php
                    while ($row_model = $result_model->fetch_assoc()) {
                        echo "<option value='" . $row_model['id_model'] . "'>" . htmlspecialchars($row_model['nama_model']) . "</option>";
                    }
                    ?>
                </select>
            </div>
        <div class="mb-3">
            <label for="merk_perangkat" class="form-label">Merk Perangkat:</label>
            <select class="form-select" name="merk_perangkat" required>
                <?php
                while ($row_merk = $result_merk->fetch_assoc()) {
                    echo "<option value='" . $row_merk['id_merk'] . "'>" . htmlspecialchars($row_merk['nama_merk']) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="jumlah_perangkat" class="form-label">Jumlah:</label>
            <input type="number" class="form-control" name="jumlah_perangkat" required>
        </div>
        <div class="mb-3">
            <label for="keterangan_perangkat" class="form-label">Keterangan:</label>
            <select class="form-select" name="keterangan_perangkat">
                <option value="aktif">Aktif</option>
                <option value="tidak aktif">Tidak Aktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Tambah Perangkat</button>
    </form>
</div>

<!-- Bootstrap JS (diletakkan di akhir body agar mempercepat waktu muat halaman) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Tambahan JavaScript atau script lainnya jika diperlukan -->

</body>
</html>

<?php
// Tutup koneksi database setelah selesai digunakan
$koneksi->close();
?>
