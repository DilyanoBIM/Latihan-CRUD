<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai id_lantai dari permintaan
    $id_lantai = $_POST['id_lantai'];

    // Query untuk mengambil ruangan berdasarkan lantai
    $query_ruangan = "SELECT * FROM ruangan WHERE id_lantai = '$id_lantai'";
    $result_ruangan = $koneksi->query($query_ruangan);

    if ($result_ruangan) {
        // Bangun daftar ruangan berdasarkan data yang diterima
        echo "<label for='id_ruangan'>Pilih Ruangan:</label>";
        echo "<select name='id_ruangan' required>";
        while ($row_ruangan = $result_ruangan->fetch_assoc()) {
            echo "<option value='" . $row_ruangan['id_ruangan'] . "'>" . htmlspecialchars($row_ruangan['nama_ruangan']) . "</option>";
        }
        echo "</select>";
    } else {
        echo "Error: " . $koneksi->error;
    }
}
?>
