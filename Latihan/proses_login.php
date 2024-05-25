<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_admin = $_POST['kode_admin'];

    // Cek kode admin di tabel users
    $query_check_admin = "SELECT id_users, nama_admin FROM users WHERE kode_admin = ?";
    $stmt_check_admin = $koneksi->prepare($query_check_admin);
    $stmt_check_admin->bind_param('s', $kode_admin);
    $stmt_check_admin->execute();
    $result_check_admin = $stmt_check_admin->get_result();

    if ($result_check_admin->num_rows > 0) {
        // Kode admin valid, lakukan login
        $row_admin = $result_check_admin->fetch_assoc();
        $id_users = $row_admin['id_users'];

        // Masukkan waktu login ke tabel waktu
        $query_insert_waktu = "INSERT INTO waktu (id_users, waktu_login) VALUES (?, CURRENT_TIMESTAMP)";
        $stmt_insert_waktu = $koneksi->prepare($query_insert_waktu);
        $stmt_insert_waktu->bind_param('i', $id_users);
        $stmt_insert_waktu->execute();

        // Lanjutkan dengan langkah-langkah setelah login sukses
        // Misalnya, redirect ke halaman lain atau tampilkan pesan selamat datang.
    } else {
        // Kode admin tidak valid
        echo "Kode admin tidak valid!";
    }
} else {
    // Metode request tidak valid
    echo "Metode request tidak valid!";
}
?>
