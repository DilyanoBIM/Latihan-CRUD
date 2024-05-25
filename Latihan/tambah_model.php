<?php
session_start();
include 'config.php';

// Tangkap data jika ada pengiriman formulir penambahan model
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_model_baru = $_POST['nama_model_baru'];

    // Periksa apakah nama model sudah ada di dalam tabel
    $query_check_model = "SELECT COUNT(*) as jumlah FROM model WHERE nama_model = '$nama_model_baru'";
    $result_check_model = $koneksi->query($query_check_model);

    if ($result_check_model) {
        $row = $result_check_model->fetch_assoc();
        $jumlah_model = $row['jumlah'];

        if ($jumlah_model == 0) {
            // Jika nama model belum ada, lakukan penyisipan ke dalam tabel model
            $query_insert_model = "INSERT INTO model (nama_model) VALUES ('$nama_model_baru')";
            $result_insert_model = $koneksi->query($query_insert_model);

            if ($result_insert_model) {
                echo "<p>Model berhasil ditambahkan!</p>";
            } else {
                echo "<p>Gagal menambahkan model: " . $koneksi->error . "</p>";
            }
        } else {
            echo "<p>Nama model sudah ada. Silakan pilih nama yang lain.</p>";
        }
    } else {
        echo "<p>Error saat memeriksa nama model: " . $koneksi->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Model</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tambahan stylesheet atau styling lainnya jika diperlukan -->
</head>
<body>

<h1>Tambah Model</h1>

<!-- Formulir Tambah Model -->
<form method="POST" action="">
    <label for="nama_model_baru">Nama Model Baru:</label>
    <input type="text" name="nama_model_baru" required>
    <button type="submit">Tambah Model</button>
</form>
<a href="lantai.php" class="btn btn-secondary mt-3">Kembali ke Halaman Lantai</a>
<!-- Tambahan konten atau elemen lainnya jika diperlukan -->

</body>
</html>

<?php
// Tutup koneksi database setelah selesai digunakan
$koneksi->close();
?>
