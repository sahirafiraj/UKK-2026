
<?php
// Mengecek apakah tombol Simpan ditekan
if (isset($_POST['simpan'])) {

    // Mengambil data dari form input
    // trim() digunakan untuk menghapus spasi di awal dan akhir teks
    $nama = trim($_POST['nama']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    // Mengamankan teks nama agar tanda kutip tidak merusak query SQL
    $nama_db = mysqli_real_escape_string($koneksi, $nama);

    // Mencari tahun ajaran yang namanya sama di database
    // Contoh: mencari apakah 2026/2027 sudah pernah ditambahkan
    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM t_tahun_ajaran
         WHERE nama = '$nama_db'"
    );

    // Jika nama tahun ajaran sudah ditemukan, tampilkan pesan error
    if (mysqli_num_rows($cek) > 0) {

        // Pesan ini akan ditampilkan pada form
        $pesan = "Tahun ajaran tersebut sudah terdaftar!";

    // Jika tanggal selesai lebih awal daripada tanggal mulai
    } elseif ($tanggal_selesai < $tanggal_mulai) {

        // Menampilkan pesan jika tanggal tidak sesuai
        $pesan = "Tanggal selesai tidak boleh sebelum tanggal mulai!";

    } else {

        // Jika nama belum ada dan tanggal benar,
        // simpan data tahun ajaran ke database
        $simpan = mysqli_query(
            $koneksi,
            "INSERT INTO t_tahun_ajaran
            (nama, tanggal_mulai, tanggal_selesai, status_aktif)
            VALUES (
                '$nama_db',
                '$tanggal_mulai',
                '$tanggal_selesai',
                '$status_aktif'
            )"
        );

        // Mengecek apakah data berhasil disimpan
        if ($simpan) {

            // Jika berhasil, kembali ke halaman daftar tahun ajaran
            header("Location: data_tahun_ajaran.php");
            exit;

        } else {

            // Jika gagal, tampilkan pesan kesalahan
            $pesan = "Data tahun ajaran gagal disimpan!";
        }
    }
}
?>
