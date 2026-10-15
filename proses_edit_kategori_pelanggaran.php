
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

$id = $_POST['id'];
$nama = $_POST['nama'];
$deskripsi = $_POST['deskripsi'];
$status_aktif = $_POST['status_aktif'];

$query = mysqli_query($koneksi, "UPDATE t_pelanggaran_kategori SET
    nama='$nama',
    deskripsi='$deskripsi',
    status_aktif='$status_aktif',
    updated_at=CURRENT_TIMESTAMP
    WHERE id='$id'");

if ($query) {
    header("Location: data_kategori_pelanggaran.php");
    exit;
} else {
    echo "Data kategori pelanggaran gagal diubah.";
}
?>