<?php
// Konfigurasi Koneksi Database
$host = "localhost"; // Lokasi server database
$username = "root"; // Nama pengguna database (ganti dengan nama pengguna yang sesuai)
$password = ""; // Kata sandi database (ganti dengan kata sandi yang sesuai)
$database = "ridho"; // Nama database

// Membuat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Pastikan koneksi berhasil dibuat sebelumnya
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set karakter set ke UTF-8
$koneksi->set_charset("utf8");
?>
