<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['id_users'])) {
    // Redirect ke halaman login jika belum login
    header("Location: login.php");
    exit();
}

$id_users = $_SESSION['id_users'];
$nama_admin = $_SESSION['nama_admin'];
include 'config.php';

$nama_ruangan = $_GET['nama'] ?? '';

// Tangkap data jika ada pengiriman formulir logout
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
}

// Ambil data lantai dari database
$query_lantai = "SELECT * FROM lantai";
$result_lantai = $koneksi->query($query_lantai);

// Ambil data lantai dari database, kecuali yang sudah ada
$query_lantai_tanpa_nama = "SELECT * FROM lantai WHERE id_lantai NOT IN (SELECT id_lantai FROM lantai WHERE nama_lantai IS NOT NULL)";
$result_lantai_tanpa_nama = $koneksi->query($query_lantai_tanpa_nama);

// Periksa apakah query berhasil dijalankan
if (!$result_lantai) {
    die("Query error: " . $koneksi->error);
}

$query_model = "SELECT * FROM model";
$result_model = $koneksi->query($query_model);

// Ambil data merk perangkat dari database
$query_merk = "SELECT * FROM merk";
$result_merk = $koneksi->query($query_merk);

// Periksa apakah query berhasil dijalankan
if (!$result_model || !$result_merk) {
    die("Query error: " . $koneksi->error);
}

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

// Tangkap data jika ada pengiriman formulir penambahan lantai atau penghapusan lantai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logika penghapusan lantai
    if (isset($_POST['hapus_lantai_modal'])) {
        $id_lantai_to_delete = $_POST['lantai_to_delete'];

        // Hapus lantai dan ruangan terkait menggunakan foreign key constraint
        $query_hapus_lantai_modal = "DELETE FROM lantai WHERE id_lantai = '$id_lantai_to_delete'";
        $result_hapus_lantai_modal = $koneksi->query($query_hapus_lantai_modal);

        if ($result_hapus_lantai_modal) {
            // Redirect agar halaman diperbarui setelah penghapusan
            header("Location: lantai.php");
            exit();
        } else {
            echo "<p>Gagal menghapus lantai: " . $koneksi->error . "</p>";
        }
    }

    // Logika penambahan lantai (seperti yang sudah Anda miliki)
    if (isset($_POST['nama_lantai_baru'])) {
        $nama_lantai_baru = $_POST['nama_lantai_baru'];

        $query_check_lantai = "SELECT COUNT(*) as jumlah FROM lantai WHERE nama_lantai = '$nama_lantai_baru'";
        $result_check_lantai = $koneksi->query($query_check_lantai);

        if ($result_check_lantai) {
            $row = $result_check_lantai->fetch_assoc();
            $jumlah_lantai = $row['jumlah'];

            if ($jumlah_lantai == 0) {
                $query_insert_lantai = "INSERT INTO lantai (nama_lantai) VALUES ('$nama_lantai_baru')";
                $result_insert_lantai = $koneksi->query($query_insert_lantai);

                if ($result_insert_lantai) {
                    // Perbarui hasil query untuk menampilkan lantai yang baru ditambahkan
                    $result_lantai = $koneksi->query($query_lantai);
                } else {
                    echo "<p>Gagal menambahkan lantai: " . $koneksi->error . "</p>";
                }
            } else {
                echo "<p>Nama lantai sudah ada. Silakan pilih nama yang lain.</p>";
            }
        } else {
            echo "<p>Error saat memeriksa nama lantai: " . $koneksi->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Lantai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- W3.CSS CSS -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        body {
           
            padding-top: px; /* Adjust this value based on your navbar height */
        }

        #content {
            margin-left: 20%; /* Adjust this value based on your sidebar width */
            padding: 20px;
        }

        /* Custom style to make the sidebar smaller */
        .small-sidebar {
            width: 150px;
        }
    </style>
    <script>
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus lantai ini?");
    }
</script>
<script>
        function clearInput() {
            document.getElementById("nama_lantai_baru").value = "";
        }
    </script>

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

        <!-- Tombol Logout -->
        
    </div>
</nav>

<div class="w3-sidebar w3-light-grey w3-card small-sidebar  ">
    <a href="#" class="w3-bar-item w3-button mt-3 mb-1"><i class="fas fa-home"></i> Home</a> 
    <div class="w3-dropdown-hover mt-1 mb-5">
        <button class="w3-button">Lantai <i class="fas fa-caret-down"></i></button>
        <div class="w3-dropdown-content w3-bar-block">
    <ul class="mt-3 mb-3">
        <?php while ($row_lantai = $result_lantai->fetch_assoc()) : ?>
            <li class="mb-2"> <!-- Add margin-bottom for spacing -->
                <a href='lantai_detail.php?id=<?= $row_lantai['id_lantai'] ?>'>
                    <?= htmlspecialchars($row_lantai['nama_lantai']) ?>
                </a>
                <?php if ($_SERVER['PHP_SELF'] == '/lantai.php') : ?>
                    <!-- Tampilkan tombol hapus hanya jika halaman saat ini adalah lantai.php -->
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
        <!-- Tombol untuk membuka modal tambah lantai -->
        <button class="btn btn-primary btn-sm mt-2 mb-2" data-toggle="modal" data-target="#tambahLantaiModal"><i class="fas fa-plus"></i> </button>

        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusLantaiModal"><i class="fas fa-trash"></i> </button>
    </ul>
</div>
    </div>

    <form class="form-inline mt-3 text-center" method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin logout?');">
    <button class="btn btn-outline-dark" type="submit" name="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
</form>

</div>


<div class="modal fade" id="tambahLantaiModal" tabindex="-1" role="dialog" aria-labelledby="tambahLantaiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahLantaiModalLabel">Tambah Lantai Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulir Tambah Lantai di dalam modal -->
                <form method="POST" action="">
                    <input type="text" name="nama_lantai_baru" placeholder="Masukkan Nama Lantai" autocomplete="off" required>
                    <button type="submit">Tambah Lantai</button>
                </form>

            </div>
        </div>
    </div>
</div>

<<!-- Modal hapus lantai -->
<div class="modal fade" id="hapusLantaiModal" tabindex="-1" role="dialog" aria-labelledby="hapusLantaiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hapusLantaiModalLabel">Hapus Lantai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form method='POST' action='' onsubmit="return confirmDelete();">
    <div class="form-group">
        <select class="form-control" id="lantaiToDelete" name="lantai_to_delete" required>
            <option value="" disabled selected hidden>Pilih lantai...</option>
            <?php
            // Kembali ke awal hasil query
            $result_lantai->data_seek(0);
            while ($row_lantai_modal = $result_lantai->fetch_assoc()) :
            ?>
                <option value="<?= $row_lantai_modal['id_lantai'] ?>">
                    <?= htmlspecialchars($row_lantai_modal['nama_lantai']) ?>
                </option>
            <?php endwhile; ?>
        </select>
            </div>
            <button type='submit' name='hapus_lantai_modal' class='btn btn-danger mt-2'>Hapus</button>
        </form>

            </div>
        </div>
    </div>
</div>
        
        
        <!-- Tambahkan tombol Tambah Lantai di sidebar -->
        
    </div>
    <div class="container mt-5">
        <h1>Halo <?php echo htmlspecialchars($nama_admin); ?>!</h1>
        <h1>Daftar Lantai Gedung Damoza Toys</h1>


        <!-- Formulir Tambah Lantai -->
    

        <!-- Tombol untuk Menambah Model -->
       
    </div>

<!-- Bootstrap JS (diletakkan di akhir body agar mempercepat waktu muat halaman) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Tutup koneksi database setelah selesai digunakan
$koneksi->close();
?>