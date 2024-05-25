<?php
session_start();
include 'config.php';

// Ambil data ruangan dari database
$query_ruangan = "SELECT * FROM ruangan";
$result_ruangan = $koneksi->query($query_ruangan);

// Periksa apakah query berhasil dijalankan
if (!$result_ruangan) {
    die("Query error: " . $koneksi->error);
}

// Tangkap data jika ada pengiriman formulir penambahan ruangan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ruangan_baru = $_POST['nama_ruangan_baru'];

    // Periksa apakah nama ruangan sudah ada di dalam tabel
    $query_check_ruangan = "SELECT COUNT(*) as jumlah FROM ruangan WHERE nama_ruangan = '$nama_ruangan_baru'";
    $result_check_ruangan = $koneksi->query($query_check_ruangan);

    if ($result_check_ruangan) {
        $row = $result_check_ruangan->fetch_assoc();
        $jumlah_ruangan = $row['jumlah'];

        if ($jumlah_ruangan == 0) {
            // Jika nama ruangan belum ada, lakukan penyisipan ke dalam tabel ruangan
            $query_insert_ruangan = "INSERT INTO ruangan (nama_ruangan) VALUES ('$nama_ruangan_baru')";
            $result_insert_ruangan = $koneksi->query($query_insert_ruangan);

            if ($result_insert_ruangan) {
                echo "<p>Ruangan berhasil ditambahkan!</p>";

                // Perbarui hasil query untuk menampilkan ruangan yang baru ditambahkan
                $result_ruangan = $koneksi->query($query_ruangan);
            } else {
                echo "<p>Gagal menambahkan ruangan: " . $koneksi->error . "</p>";
            }
        } else {
            echo "<p>Nama ruangan sudah ada. Silakan pilih nama yang lain.</p>";
        }
    } else {
        echo "<p>Error saat memeriksa nama ruangan: " . $koneksi->error . "</p>";
    }
}

// Hapus ruangan jika ada permintaan penghapusan
if (isset($_GET['hapus_ruangan'])) {
    $id_ruangan_hapus = $_GET['hapus_ruangan'];

    // Hapus data terkait dari tabel perangkat_jaringan
    $query_hapus_perangkat = "DELETE FROM perangkat_jaringan WHERE id_ruangan = '$id_ruangan_hapus'";
    $result_hapus_perangkat = $koneksi->query($query_hapus_perangkat);

    if ($result_hapus_perangkat) {
        // Setelah data perangkat dihapus, hapus ruangan
        $query_hapus_ruangan = "DELETE FROM ruangan WHERE id_ruangan = '$id_ruangan_hapus'";
        $result_hapus_ruangan = $koneksi->query($query_hapus_ruangan);

        if ($result_hapus_ruangan) {
            echo "<p>Ruangan berhasil dihapus!</p>";

            // Perbarui hasil query untuk menampilkan ruangan yang baru ditambahkan
            $result_ruangan = $koneksi->query($query_ruangan);
        } else {
            echo "<p>Gagal menghapus ruangan: " . $koneksi->error . "</p>";
        }
    } else {
        echo "<p>Gagal menghapus perangkat: " . $koneksi->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Utama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tambahan stylesheet atau styling lainnya jika diperlukan -->
</head>
<body>

<h1>Daftar Ruangan</h1>

<ul>
    <?php
    // Tampilkan daftar ruangan
    while ($row_ruangan = $result_ruangan->fetch_assoc()) {
        // Buat link yang mengarah ke halaman ruangan dengan nama sebagai bagian dari URL
        echo "<li>
                <a href='ruangan.php?nama=" . urlencode($row_ruangan['nama_ruangan']) . "'>" . htmlspecialchars($row_ruangan['nama_ruangan']) . "</a>
                <a href='utama.php?hapus_ruangan=" . $row_ruangan['id_ruangan'] . "' class='btn btn-danger btn-sm'>Hapus</a>
              </li>";
    }
    ?>
</ul>

<!-- Formulir Tambah Ruangan -->
<form method="POST" action="">
    <label for="nama_ruangan_baru">Nama Ruangan Baru:</label>
    <input type="text" name="nama_ruangan_baru" required>
    <button type="submit">Tambah Ruangan</button>
</form>

<!-- Tombol untuk Menambah Merk -->
<a href="tambah_merk.php" class="btn btn-primary mt-3">Tambah Merk</a>

<!-- Tombol untuk Menambah Model -->
<a href="tambah_model.php" class="btn btn-primary mt-3">Tambah Model</a>

<!-- Tambahan konten atau elemen lainnya jika diperlukan -->

</body>
</html>

<?php
// Tutup koneksi database setelah selesai digunakan
$koneksi->close();
?>
