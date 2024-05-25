<?php
session_start();
include 'config.php';

// Ambil nilai parameter 'id' dari URL
$id_perangkat = $_GET['id'] ?? '';

$query_model = "SELECT * FROM model";
$result_model = $koneksi->query($query_model);

// Ambil data merk perangkat dari database
$query_merk = "SELECT * FROM merk";
$result_merk = $koneksi->query($query_merk);

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

// Tangkap data jika ada pengiriman formulir pengeditan perangkat
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
                  }, 2000); // Redirect setelah 2 detik (2000 milidetik)
                </script>";
        } else {
            echo "<div class='alert alert-danger mt-3' role='alert'>
                    Gagal menghapus perangkat: " . $koneksi->error . "
                  </div>";
        }
    } else {
        // Lanjutkan dengan proses pengeditan perangkat
        
        $merk_perangkat = $_POST['merk_perangkat'];
        $model_perangkat = $_POST['model_perangkat'];
        $jumlah_perangkat = $_POST['jumlah_perangkat'];
        $keterangan_perangkat = $_POST['keterangan_perangkat'];

        // Lakukan validasi atau operasi pengeditan ke database sesuai kebutuhan
        // Misalnya, lakukan validasi dan update data di tabel perangkat_jaringan
        $query_update_perangkat = "UPDATE perangkat_jaringan 
                                   SET 
                                       id_model = $model_perangkat, 
                                       id_merk = $merk_perangkat, 
                                       jumlah = $jumlah_perangkat, 
                                       keterangan_perangkat = '$keterangan_perangkat' 
                                   WHERE id_perangkat = '$id_perangkat'";
        $result_update_perangkat = $koneksi->query($query_update_perangkat);

        if ($result_update_perangkat) {
            echo "<div class='alert alert-success mt-3' role='alert'>
                    Perangkat berhasil diperbarui!
                  </div>";

            echo "<script>
                  setTimeout(function() {
                      window.location.href = 'ruangan.php?nama=" . urlencode($nama_ruangan) . "';
                  }, 1); // Redirect setelah 2 detik (2000 milidetik)
                </script>";
        } else {
            echo "<div class='alert alert-danger mt-3' role='alert'>
                    Gagal memperbarui perangkat: " . $koneksi->error . "
                  </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Perangkat</title>

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
        <h1 class="mb-4">Edit Perangkat</h1>

        <?php
        // Tampilkan formulir pengeditan perangkat
        if ($result_perangkat_detail->num_rows > 0) {
            $row_perangkat_detail = $result_perangkat_detail->fetch_assoc();
            ?>

            <form method="POST" action="">
            <div class="mb-3">
                    <label for="model_perangkat" class="form-label">Model Perangkat:</label>
                    <select class="form-select" name="model_perangkat" required>
                        <?php
                        while ($row_model = $result_model->fetch_assoc()) {
                            $selected = ($row_model['id_model'] == $row_perangkat_detail['id_model']) ? 'selected' : '';
                            echo "<option value='" . $row_model['id_model'] . "' $selected>" . htmlspecialchars($row_model['nama_model']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="merk_perangkat" class="form-label">Merk Perangkat:</label>
                    <select class="form-select" name="merk_perangkat" required>
                        <?php
                        while ($row_merk = $result_merk->fetch_assoc()) {
                            $selected = ($row_merk['id_merk'] == $row_perangkat_detail['id_merk']) ? 'selected' : '';
                            echo "<option value='" . $row_merk['id_merk'] . "' $selected>" . htmlspecialchars($row_merk['nama_merk']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="jumlah_perangkat" class="form-label">Jumlah:</label>
                    <input type="number" class="form-control" name="jumlah_perangkat" value="<?php echo htmlspecialchars($row_perangkat_detail['jumlah']); ?>" required>
                </div>
                <div class="mb-3">
            <label for="keterangan_perangkat" class="form-label">Keterangan:</label>
            <select class="form-select" name="keterangan_perangkat">
                <option value="aktif">Aktif</option>
                <option value="tidak aktif">Tidak Aktif</option>
            </select>
        </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>

                <!-- Tombol Hapus -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#hapusModal">Hapus Perangkat</button>

                <!-- Modal Konfirmasi Penghapusan -->
                <div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Penghapusan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Anda yakin ingin menghapus perangkat ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-danger" name="hapus_perangkat">Ya, Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
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
