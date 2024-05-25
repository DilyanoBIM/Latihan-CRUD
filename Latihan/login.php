<?php
session_start();

include 'config.php';

// Tangkap data jika ada pengiriman formulir login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $kode_admin = $_POST['kode_admin'];

    // Ganti query berdasarkan struktur tabel dan kolom pada database Anda
    $query_login = "SELECT * FROM users WHERE kode_admin = '$kode_admin'";
    $result_login = $koneksi->query($query_login);

    if ($result_login && $result_login->num_rows > 0) {
        // Pengguna ditemukan, set session dan redirect ke halaman lantai.php
        $row = $result_login->fetch_assoc();
        $_SESSION['id_users'] = $row['id_users'];
        $_SESSION['nama_admin'] = $row['nama_admin'];

        // Masukkan waktu login ke tabel waktu
        $id_users = $row['id_users'];
        $query_insert_waktu_login = "INSERT INTO waktu (id_users, waktu_login) VALUES (?, CURRENT_TIMESTAMP)";
        $stmt_insert_waktu_login = $koneksi->prepare($query_insert_waktu_login);
        $stmt_insert_waktu_login->bind_param('i', $id_users);
        $stmt_insert_waktu_login->execute();

        // Ganti nama lantai.php dengan nama file yang sesuai
        header("Location: lantai.php");
        exit();
    } else {
        $error_message = "Kode Admin tidak valid. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Login</h1>

    <?php
    if (isset($error_message)) {
        echo "<p class='text-danger'>$error_message</p>";
    }
    ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="kode_admin" class="form-label">Kode Admin:</label>
            <input type="text" name="kode_admin" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary">Login</button>
    </form>
</div>

<!-- Bootstrap JS (diletakkan di akhir body agar mempercepat waktu muat halaman) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Tutup koneksi database setelah selesai digunakan
$koneksi->close();
?>
