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



include "config.php";


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Beranda</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- W3.CSS CSS -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 56px; /* Adjust this value based on your navbar height */
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
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">Nama Aplikasi Anda</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Beranda <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Fitur 1</a>
                </li>
                <form class="form-inline" method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin logout?');">
            <button class="btn btn-outline-light" type="submit" name="logout">Logout</button>
        </form>
                <!-- Tambahkan menu sesuai kebutuhan -->
            </ul>
            
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="w3-sidebar w3-bar-block w3-light-grey w3-card small-sidebar">
        <a href="#" class="w3-bar-item w3-button">Link 1</a>
        <a href="#" class="w3-bar-item w3-button">Link 2</a>
        <div class="w3-dropdown-hover">
            <button class="w3-button">Dropdown
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="w3-dropdown-content w3-bar-block">
                <a href="#" class="w3-bar-item w3-button">Link</a>
                <a href="#" class="w3-bar-item w3-button">Link</a>
            </div>
        </div>
        <a href="#" class="w3-bar-item w3-button">Link 3</a>
        
        <!-- Tambahkan tombol Tambah Lantai di sidebar -->
        <a href="#" class="w3-bar-item w3-button" onclick="openAddFloorModal()">Tambah Lantai</a>
    </div>

    <!-- Content -->
    <div id="content">
        <h2>Selamat Datang di Halaman Beranda</h2>
        <!-- Isi konten sesuai kebutuhan -->
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom JS -->
    <script>
        function openAddFloorModal() {
            // Tambahkan logika untuk membuka modal atau lakukan aksi yang sesuai
            alert('Tombol Tambah Lantai ditekan!');
        }
    </script>
</body>

</html>
