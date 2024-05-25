<?php
session_start();
include 'config.php';

// Tangkap data jika ada pengiriman formulir penambahan merk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_merk_baru = $_POST['nama_merk_baru'];

    // Periksa apakah nama merk sudah ada di dalam tabel
    $query_check_merk = "SELECT COUNT(*) as jumlah FROM merk WHERE nama_merk = '$nama_merk_baru'";
    $result_check_merk = $koneksi->query($query_check_merk);

    if ($result_check_merk) {
        $row = $result_check_merk->fetch_assoc();
        $jumlah_merk = $row['jumlah'];

        if ($jumlah_merk == 0) {
            // Jika nama merk belum ada, lakukan penyisipan ke dalam tabel merk
            $query_insert_merk = "INSERT INTO merk (nama_merk) VALUES ('$nama_merk_baru')";
            $result_insert_merk = $koneksi->query($query_insert_merk);

            if ($result_insert_merk) {
                echo "<p>Merk berhasil ditambahkan!</p>";
            } else {
                echo "<p>Gagal menambahkan merk: " . $koneksi->error . "</p>";
            }
        } else {
            echo "<p>Nama merk sudah ada. Silakan pilih nama yang lain.</p>";
        }
    } else {
        echo "<p>Error saat memeriksa nama merk: " . $koneksi->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Merk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tambahan stylesheet atau styling lainnya jika diperlukan -->
</head>
<body>

<h1>Tambah Merk</h1>

<!-- Formulir Tambah Merk -->
<form method="POST" action="">
    <label for="nama_merk_baru">Nama Merk Baru:</label>
    <input type="text" name="nama_merk_baru" required>
    <button type="submit">Tambah Merk</button>
</form>

<!-- Tombol Kembali -->
<a href="lantai.php" class="btn btn-secondary mt-3">Kembali ke Halaman Lantai</a>

<!-- Tambahan konten atau elemen lainnya jika diperlukan -->

</body>
</html>

<?php
// Tutup koneksi database setelah selesai digunakan
$koneksi->close();
?>
