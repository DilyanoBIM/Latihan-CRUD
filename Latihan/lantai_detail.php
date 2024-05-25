<?php
session_start();
include 'config.php';

// Ambil nilai parameter 'id' dari URL
$id_lantai = $_GET['id'] ?? '';

// Ambil data lantai dari database berdasarkan ID
$query_lantai_detail = "SELECT * FROM lantai WHERE id_lantai = '$id_lantai'";
$result_lantai_detail = $koneksi->query($query_lantai_detail);

// Periksa apakah query berhasil dijalankan
if (!$result_lantai_detail) {
    die("Query error: " . $koneksi->error);
}

// Ambil data ruangan dari database berdasarkan lantai
$query_ruangan = "SELECT * FROM ruangan WHERE id_lantai = '$id_lantai'";
$result_ruangan = $koneksi->query($query_ruangan);

// Periksa apakah query berhasil dijalankan
if (!$result_ruangan) {
    die("Query error: " . $koneksi->error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logout'])) {
        // Masukkan waktu logout ke tabel waktu
        $query_update_waktu_logout = "UPDATE waktu SET waktu_logout = CURRENT_TIMESTAMP WHERE id_users = ?";
        $stmt_update_waktu_logout = $koneksi->prepare($query_update_waktu_logout);
        $stmt_update_waktu_logout->bind_param('i', $id_users);
        $stmt_update_waktu_logout->execute();

        // Hapus sesi dan redirect ke halaman login
        session_destroy();
        header("Location: login.php");
        exit();
    }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logout'])) {
        // Masukkan waktu logout ke tabel waktu
        $query_update_waktu_logout = "UPDATE waktu SET waktu_logout = CURRENT_TIMESTAMP WHERE id_users = ?";
        $stmt_update_waktu_logout = $koneksi->prepare($query_update_waktu_logout);
        $stmt_update_waktu_logout->bind_param('i', $id_users);
        $stmt_update_waktu_logout->execute();

        // Hapus sesi dan redirect ke halaman login
        session_destroy();
        header("Location: login.php");
        exit();
    }
// Tangkap data jika ada pengiriman formulir penambahan ruangan atau penghapusan ruangan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hapus_ruangan'])) {
        $id_ruangan_hapus = $_POST['id_ruangan_hapus'];
        
        // Konfirmasi penghapusan ruangan
        echo "<script>
                var confirmation = confirm('Apakah Anda yakin ingin menghapus ruangan ini?');
                if (confirmation) {
                    window.location.href = 'lantai_detail.php?id=$id_lantai&hapus_ruangan=$id_ruangan_hapus';
                }
              </script>";
    }

    // Logika penambahan ruangan (seperti yang sudah Anda miliki)
    if (isset($_POST['nama_ruangan_baru'])) {
        $nama_ruangan_baru = $_POST['nama_ruangan_baru'];

        $query_check_ruangan = "SELECT COUNT(*) as jumlah FROM ruangan WHERE nama_ruangan = ?";
        $stmt_check_ruangan = $koneksi->prepare($query_check_ruangan);
        $stmt_check_ruangan->bind_param('s', $nama_ruangan_baru);
        $stmt_check_ruangan->execute();
        $result_check_ruangan = $stmt_check_ruangan->get_result();

        if ($result_check_ruangan) {
            $row = $result_check_ruangan->fetch_assoc();
            $jumlah_ruangan = $row['jumlah'];

            if ($jumlah_ruangan == 0) {
                $query_insert_ruangan = "INSERT INTO ruangan (nama_ruangan, id_lantai) VALUES (?, ?)";
                $stmt_insert_ruangan = $koneksi->prepare($query_insert_ruangan);

                $stmt_insert_ruangan->bind_param('si', $nama_ruangan_baru, $id_lantai);

                if ($stmt_insert_ruangan->execute()) {
                   
                    // Perbarui hasil query untuk menampilkan ruangan yang baru ditambahkan
                    $result_ruangan = $koneksi->query($query_ruangan);
                } else {
                    echo "<p>Gagal menambahkan ruangan: " . $stmt_insert_ruangan->error . "</p>";
                }
            } else {
                echo "<p>Nama ruangan sudah ada di lantai ini. Silakan pilih nama yang lain.</p>";
            }
        } else {
            echo "<p>Error saat memeriksa nama ruangan: " . $koneksi->error . "</p>";
        }
    }
}
}
}
// Proses penghapusan ruangan setelah konfirmasi
if (isset($_GET['hapus_ruangan'])) {
    $id_ruangan_hapus = $_GET['hapus_ruangan'];

    // Hapus data terkait di tabel lain (contoh: perangkat jaringan)
    $query_hapus_perangkat = "DELETE FROM perangkat_jaringan WHERE id_ruangan = '$id_ruangan_hapus'";
    $result_hapus_perangkat = $koneksi->query($query_hapus_perangkat);

    // Hapus ruangan
    $query_hapus_ruangan = "DELETE FROM ruangan WHERE id_ruangan = '$id_ruangan_hapus'";
    $result_hapus_ruangan = $koneksi->query($query_hapus_ruangan);

    if ($result_hapus_ruangan) {
        echo "<p>Ruangan berhasil dihapus!</p>";
        // Redirect agar halaman diperbarui setelah penghapusan
        header("Location: lantai_detail.php?id=$id_lantai");
        exit();
    } else {
        echo "<p>Gagal menghapus ruangan: " . $koneksi->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Detail Lantai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0; /* Ganti dengan kode warna yang diinginkan */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Damoza Toys</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                </li>
            </ul>
        </div>
        <form class="form-inline" method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin logout?');">
            <button class="btn btn-outline-light" type="submit" name="logout">Logout</button>
        </form>
    </div>
</nav>

<div class="container mt-5">
    <?php
    // Tampilkan informasi lantai
    if ($result_lantai_detail->num_rows > 0) {
        $row_lantai_detail = $result_lantai_detail->fetch_assoc();
        echo "<h1 class='mb-4'>Data Ruangan di " . htmlspecialchars($row_lantai_detail['nama_lantai']) . "</h1>";

        // Tampilkan daftar ruangan
        if ($result_ruangan->num_rows > 0) {
            echo "<ul>";
            while ($row_ruangan = $result_ruangan->fetch_assoc()) {
                echo "<li>
                        <a href='ruangan.php?nama=" . urlencode($row_ruangan['nama_ruangan']) . "'>" . htmlspecialchars($row_ruangan['nama_ruangan']) . "</a>
                        <form method='POST' action=''>
                            <input type='hidden' name='id_ruangan_hapus' value='" . $row_ruangan['id_ruangan'] . "'>
                            <button type='submit' name='hapus_ruangan' class='btn btn-danger btn-sm'>Hapus</button>
                        </form>
                      </li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='text-danger'>Belum ada ruangan di lantai ini.</p>";
        }

        // Formulir Tambah Ruangan
        echo "<form method='POST' action=''>
                <label for='nama_ruangan_baru'>Nama Ruangan Baru:</label>
                <input type='text' name='nama_ruangan_baru' required>
                <button type='submit'>Tambah Ruangan</button>
              </form>";

        // Tombol Kembali ke Halaman Lantai
        echo "<a href='lantai.php' class='btn btn-secondary mt-3'>
                <i class='fas fa-arrow-left'></i> Kembali ke Lantai
              </a>";
    } else {
        echo "<p class='text-danger'>Lantai tidak ditemukan.</p>";
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
