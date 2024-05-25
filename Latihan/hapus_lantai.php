<?php
// Sesuaikan dengan konfigurasi koneksi dan fungsi-fungsi lain yang dibutuhkan
include 'config.php';

// Pastikan metode yang digunakan adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil ID lantai yang akan dihapus dari data yang dikirimkan
    $id_lantai_hapus = $_POST['id_lantai_hapus'];

    // Lakukan operasi penghapusan lantai di database
    $query_hapus_lantai = "DELETE FROM lantai WHERE id_lantai = ?";
    $stmt_hapus_lantai = $koneksi->prepare($query_hapus_lantai);
    $stmt_hapus_lantai->bind_param('i', $id_lantai_hapus);

    if ($stmt_hapus_lantai->execute()) {
        // Jika penghapusan berhasil, kirimkan respons JSON
        echo json_encode(['status' => 'success', 'message' => 'Lantai berhasil dihapus']);
    } else {
        // Jika terjadi kesalahan, kirimkan respons JSON dengan pesan kesalahan
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus lantai: ' . $stmt_hapus_lantai->error]);
    }

    // Tutup statement dan koneksi database setelah selesai digunakan
    $stmt_hapus_lantai->close();
    $koneksi->close();
} else {
    // Jika bukan metode POST, kirimkan respons JSON dengan pesan kesalahan
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid']);
}
?>
