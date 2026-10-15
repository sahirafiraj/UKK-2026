
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_POST['nama'];
$deskripsi = $_POST['deskripsi'];
$status_aktif = $_POST['status_aktif'];

$query = mysqli_query($koneksi, "INSERT INTO t_pelanggaran_kategori
(nama, deskripsi, status_aktif)
VALUES ('$nama', '$deskripsi', '$status_aktif')");

if ($query) {
    header("Location: data_kategori_pelanggaran.php");
    exit;
} else {
    echo "Data kategori pelanggaran gagal ditambahkan.";
}
?>