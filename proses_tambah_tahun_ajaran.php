
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Cek hak akses
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Cek tombol simpan
if (isset($_POST['simpan'])) {

    // Mengambil data dari form
    $nama = $_POST['nama'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    // Cek apakah tahun ajaran sudah terdaftar
    $cek = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE nama = '$nama'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
                alert('Tahun ajaran tersebut sudah terdaftar!');
                window.location='tambah_tahun_ajaran.php';
              </script>";
        exit;
    }

    // Menyimpan data tahun ajaran
    $query = mysqli_query($koneksi, "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')");

    // Cek hasil penyimpanan
    if ($query) {
        echo "<script>
                alert('Data tahun ajaran berhasil ditambahkan!');
                window.location='data_tahun_ajaran.php';
              </script>";
    } else {
        echo "<script>
                alert('Data tahun ajaran gagal ditambahkan!');
                window.location='tambah_tahun_ajaran.php';
              </script>";
    }

} else {
    header("Location: tambah_tahun_ajaran.php");
    exit;
}
?>
