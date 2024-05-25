<?php
session_start();
include 'config.php';

// Ambil nilai parameter 'id' dari URL
$id_perangkat = $_GET['id'] ?? '';

// Ambil data perangkat jaringan dari database berdasarkan id_perangkat
$query_perangkat_detail = "SELECT * FROM perangkat_jaringan WHERE id_perangkat = '$id_perangkat'";
$result_perangkat_detail = $koneksi->query($query_perangkat_detail);

// Ambil data ruangan dari database berdasarkan id_ruangan perangkat jaringan
$query_ruangan_detail = "SELECT r.nama_ruangan FROM ruangan r
                         INNER JOIN perangkat_jaringan p ON r.id_ruangan = p.id_ruangan
                         WHERE p.id_perangkat = '$id_perangkat'";
$result_ruangan_detail = $koneksi->query($query_ruangan_detail);

// Periksa apakah query berhasil dijalankan
if (!$result_perangkat_detail || !$result_ruangan_detail) {
    die("Query error: " . $koneksi->error);
}

$nama_ruangan = $result_ruangan_detail->fetch_assoc()['nama_ruangan'] ?? '';


// Tangkap data jika ada pengiriman formulir penghapusan perangkat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hapus_perangkat'])) {
        // Hapus perangkat jika tombol hapus ditekan
        $query_hapus_perangkat = "DELETE FROM perangkat_jaringan WHERE id_perangkat = '$id_perangkat'";
        $result_hapus_perangkat = $koneksi->query($query_hapus_perangkat);

        if ($result_hapus_perangkat) {
            echo "<div class='alert alert-success mt-3' role='alert'>
                    Perangkat berhasil dihapus!
                  </div>";

            echo "<script>
                  setTimeout(function() {
                      window.location.href = 'ruangan.php?nama=" . urlencode($nama_ruangan) . "';
                  }, 500); // Redirect setelah 2 detik (2000 milidetik)
                </script>";
        } else {
            echo "<div class='alert alert-danger mt-3' role='alert'>
                    Gagal menghapus perangkat: " . $koneksi->error . "
                  </div>";
        }}}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Perangkat</title>

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
        <h1 class="mb-4">Hapus Perangkat</h1>

        <?php
        // Tampilkan formulir penghapusan perangkat
        if ($result_perangkat_detail->num_rows > 0) {
            $row_perangkat_detail = $result_perangkat_detail->fetch_assoc();
            ?>

            <form method="POST" action="">
                <p>Apakah Anda yakin ingin menghapus perangkat ini?</p>
                <p>Nama Perangkat: <?php echo htmlspecialchars($row_perangkat_detail['nama_perangkat']); ?></p>
                <p>Merk: <?php echo htmlspecialchars($row_perangkat_detail['id_merk']); ?></p>
                <p>Model: <?php echo htmlspecialchars($row_perangkat_detail['id_model']); ?></p>
                <p>Jumlah: <?php echo htmlspecialchars($row_perangkat_detail['jumlah']); ?></p>
                <p>Keterangan: <?php echo htmlspecialchars($row_perangkat_detail['keterangan_perangkat']); ?></p>

                <input type="hidden" name="id_perangkat" value="<?php echo $id_perangkat; ?>">
                <button type="submit" class="btn btn-danger">Hapus</button>
                <a href="ruangan.php?nama=<?php echo urlencode($nama_ruangan); ?>" class="btn btn-secondary">Batal</a>
            </form>

        <?php
        } else {
            echo "<p class='text-danger'>Perangkat tidak ditemukan.</p>";
        }
        ?>
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
