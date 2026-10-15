
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

$id = $_GET['id'];

$hapus_pelanggaran = mysqli_query($koneksi, "DELETE FROM t_pelanggaran
WHERE pelanggaran_kategori_id = '$id'");

if ($hapus_pelanggaran) {

    $hapus_kategori = mysqli_query($koneksi, "DELETE FROM t_pelanggaran_kategori
    WHERE id = '$id'");

    if ($hapus_kategori) {
        header("Location: data_kategori_pelanggaran.php?pesan=hapus_berhasil");
        exit;
    } else {
        header("Location: data_kategori_pelanggaran.php?pesan=hapus_gagal");
        exit;
    }

} else {
    header("Location: data_kategori_pelanggaran.php?pesan=hapus_gagal");
    exit;
}
?>
