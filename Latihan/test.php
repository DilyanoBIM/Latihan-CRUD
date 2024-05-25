<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Lantai</title>
</head>
<body>

    <?php
    // Fungsi untuk menyimpan nama lantai yang sudah diisi
    function simpanNamaLantai($namaLantai) {
        // Simpan ke file, database, atau tempat penyimpanan lainnya
        // Di sini, kita hanya mencetak nama lantai ke layar sebagai contoh
        echo "Nama Lantai: " . htmlspecialchars($namaLantai) . " telah disimpan.";
    }

    // Pengecekan ketika formulir dikirim
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $namaLantaiBaru = $_POST["nama_lantai_baru"];

        // Pengecekan apakah nama lantai sudah ada sebelumnya
        if (!empty($namaLantaiBaru) && !file_exists("nama_lantai.txt")) {
            // Jika belum ada, simpan nama lantai
            simpanNamaLantai($namaLantaiBaru);
        } else {
            // Jika sudah ada, berikan pesan kesalahan
            echo "Nama lantai sudah diisi sebelumnya!";
        }
    }
    ?>

    <form method="POST" action="">
        <label for="nama_lantai_baru">Masukkan Nama Lantai:</label>
        <input type="text" id="nama_lantai_baru" name="nama_lantai_baru" placeholder="Masukkan Nama Lantai" required>
        <button type="submit">Tambah Lantai</button>
    </form>

</body>
</html>
