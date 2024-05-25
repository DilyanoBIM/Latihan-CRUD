echo "<a href='tambah_perangkat.php?nama=" . urlencode($row_ruangan_detail['nama_ruangan']) . "' class='btn btn-primary mt-3'>Tambah Perangkat</a>";

        } else {
            echo "<p class='text-danger'>Error saat mengambil data perangkat jaringan: " . $koneksi->error . "</p>";
        }

    } else {
        echo "<p class='text-danger'>Ruangan tidak ditemukan.</p>";
    }

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
                    echo "<p>Ruangan berhasil ditambahkan!</p>";
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