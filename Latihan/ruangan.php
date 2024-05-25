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
    }}
// Ambil data ruangan dari database berdasarkan nama ruangan
$query_ruangan_detail = "SELECT * FROM ruangan WHERE nama_ruangan = '$nama_ruangan'";
$result_ruangan_detail = $koneksi->query($query_ruangan_detail);

// Periksa apakah query berhasil dijalankan
if (!$result_ruangan_detail) {
    die("Query error: " . $koneksi->error);
}

$query_count_entries = "SELECT COUNT(*) as total FROM perangkat_jaringan
                       WHERE id_ruangan = (SELECT id_ruangan FROM ruangan WHERE nama_ruangan = '$nama_ruangan')";
$result_count_entries = $koneksi->query($query_count_entries);

if (!$result_count_entries) {
    die("Query error: " . $koneksi->error);
}

$row_count_entries = $result_count_entries->fetch_assoc();
$total_entries = $row_count_entries['total'];

$sort_order = $_GET['sort_order'] ?? 'asc';

// Jumlah entri per halaman
$entries_per_page = 5;
// Halaman saat ini
$current_page = $_GET['page'] ?? 1;
// Hitung offset berdasarkan halaman saat ini dan jumlah entri per halaman
$offset = ($current_page - 1) * $entries_per_page;

// Ambil data perangkat jaringan dari database berdasarkan ruangan dengan entri per halaman
$query_perangkat_jaringan = "SELECT p.*, m.nama_merk, mdl.nama_model FROM perangkat_jaringan p
                                 LEFT JOIN merk m ON p.id_merk = m.id_merk
                                 LEFT JOIN model mdl ON p.id_model = mdl.id_model
                                 WHERE id_ruangan = (SELECT id_ruangan FROM ruangan WHERE nama_ruangan = '$nama_ruangan')
                                 ORDER BY jumlah $sort_order
                                 LIMIT $entries_per_page OFFSET $offset";

$result_perangkat_jaringan = $koneksi->query($query_perangkat_jaringan);

// Periksa apakah query berhasil dijalankan
if ($result_perangkat_jaringan->num_rows === 0 && $current_page > 1) {
    header("Location: ruangan.php?nama=" . urlencode($nama_ruangan) . "&sort_order=$sort_order&page=" . ($current_page - 1));
    exit();
}

$first_entry_number = min(($current_page - 1) * $entries_per_page + 1, $total_entries);

// Tangkap data jika ada pengiriman formulir penghapusan perangkat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_perangkat'])) {
    $id_perangkat_hapus = $_POST['id_perangkat_hapus'];

    // Lakukan operasi penghapusan ke database sesuai kebutuhan
    // Misalnya, hapus data di tabel perangkat_jaringan
    $query_hapus_perangkat = "DELETE FROM perangkat_jaringan WHERE id_perangkat = '$id_perangkat_hapus'";
    $result_hapus_perangkat = $koneksi->query($query_hapus_perangkat);

    if ($result_hapus_perangkat) {
        echo "<script>
        setTimeout(function() {
            window.location.href = 'ruangan.php?nama=" . urlencode($nama_ruangan) . "&sort_order=$sort_order&page=$current_page';
        }, 1); // Redirect setelah 2 detik (2000 milidetik)
      </script>";
    } else {
        echo "<div class='alert alert-danger mt-3' role='alert'>
                Gagal menghapus perangkat: " . $koneksi->error . "
              </div>";
    }
    $result_perangkat_jaringan = $koneksi->query($query_perangkat_jaringan);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Ruangan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
<div class="container mt-5card-body ">

    <?php
    // Tampilkan informasi ruangan
    if ($result_ruangan_detail->num_rows > 0) {
        $row_ruangan_detail = $result_ruangan_detail->fetch_assoc();
        echo "<a href='lantai_detail.php?id=" . urlencode($row_ruangan_detail['id_lantai']) . "' class='btn btn-secondary mt-3'>
                <i class='fas fa-arrow-left'></i> Kembali
              </a>";
        echo "<h1 class='mb-4 mt-3'>Data Perangkat Jaringan " . htmlspecialchars($row_ruangan_detail['nama_ruangan']) . "</h1>";
        echo "<a href='tambah_perangkat.php?nama=" . urlencode($row_ruangan_detail['nama_ruangan']) . "' class='btn btn-primary mt-3 mb-5'>Tambah Perangkat</a>";
        
        
        // Tampilkan data perangkat jaringan
        if ($result_perangkat_jaringan->num_rows > 0) {
            echo "<table class='table' id='card-body'>";
            echo "<thead><tr><th scope='col'>No</th><th scope='col'>Nama Perangkat</th><th scope='col'>Merk</th><th scope='col'>Jumlah</th><th scope='col'>Keterangan</th><th scope='col'>Aksi</th></tr></thead>";
            echo "<tbody>";
            
            $no = $first_entry_number;
            while ($row_perangkat_jaringan = $result_perangkat_jaringan->fetch_assoc()) {
                echo "<tr>";
                echo "<th scope='row'>" . $no++ . "</th>";
                echo "<td>" . htmlspecialchars($row_perangkat_jaringan['nama_model']) . "</td>";
                echo "<td>" . htmlspecialchars($row_perangkat_jaringan['nama_merk']) . "</td>";
                echo "<td>" . htmlspecialchars($row_perangkat_jaringan['jumlah']) . "</td>";
                echo "<td>" . htmlspecialchars($row_perangkat_jaringan['keterangan_perangkat']) . "</td>";
                echo "<td>
                        <a href='edit_perangkat.php?id=" . $row_perangkat_jaringan['id_perangkat'] . "' class='btn btn-warning btn-sm mb-3'>Edit</a><br>
                        <form method='POST' action='' onsubmit=\"return confirm('Apakah Anda yakin ingin menghapus perangkat ini?');\">
                            <input type='hidden' name='id_perangkat_hapus' value='" . $row_perangkat_jaringan['id_perangkat'] . "'>
                            <button type='submit' name='hapus_perangkat' class='btn btn-danger btn-sm'>Hapus</button>
                        </form>
                      </td>";
                echo "</tr>";
            }

            echo "</tbody></table>";

            // Tampilkan navigasi halaman
            echo "<nav aria-label='Page navigation example'>
                        <ul class='pagination'>
                        <li class='page-item " . ($current_page == 1 ? 'disabled' : '') . "'>
                            <a class='page-link' href='ruangan.php?nama=" . urlencode($nama_ruangan) . "&sort_order=$sort_order&page=" . ($current_page - 1) . "'>
                            Previous
                            </a>
                        </li>";

            // Hitung jumlah total halaman
            $total_pages = max(1, ceil($total_entries / $entries_per_page));

            if ($current_page > $total_pages) {
                // Alihkan ke halaman terakhir
                header("Location: ruangan.php?nama=" . urlencode($nama_ruangan) . "&sort_order=$sort_order&page=$total_pages");
                exit();
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<li class='page-item " . ($i == $current_page ? 'active' : '') . "'>
                        <a class='page-link' href='ruangan.php?nama=" . urlencode($nama_ruangan) . "&sort_order=$sort_order&page=$i'>$i</a>
                      </li>";
            }
            echo "<li class='page-item " . ($current_page == $total_pages ? 'disabled' : '') . "'>
                    <a class='page-link' href='ruangan.php?nama=" . urlencode($nama_ruangan) . "&sort_order=$sort_order&page=" . ($current_page + 2) . "'>
                    Next
                    </a>
                </li>
                </ul>
            </nav>";
        } else {
            echo "<table class='table' id='datatablesSimple'>";
            echo "<thead><tr><th scope='col'>No</th><th scope='col'>Nama Perangkat</th><th scope='col'>Merk</th><th scope='col'>Jumlah</th><th scope='col'>Keterangan</th><th scope='col'>Aksi</th></tr></thead>";
            echo "<tbody>";
            echo "<tr><td colspan='6' class='text-center'>Tidak ada data</td></tr>";
            echo "</tbody></table>";
        }
    } else {
        echo "<p class='text-danger'>Ruangan tidak ditemukan.</p>";
    }
    ?>
    
</div>

<!-- Bootstrap JS (diletakkan di akhir body agar mempercepat waktu muat halaman) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

<!-- Tambahan JavaScript atau script lainnya jika diperlukan -->

</body>
</html>

<?php
// Tutup koneksi database setelah selesai digunakan
$koneksi->close();
?>
