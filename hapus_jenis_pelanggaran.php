
<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: data_jenis_pelanggaran.php");
    exit;
}

$id = (int) $_GET['id'];

// Menghapus riwayat pelanggaran siswa yang memakai jenis ini
mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran_siswa
    WHERE pelanggaran_id = '$id'
");

// Menghapus jenis pelanggaran
$query = mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran
    WHERE id = '$id'
");

if ($query) {
    header("Location: data_jenis_pelanggaran.php");
    exit;
} else {
    echo "Data gagal dihapus: " . mysqli_error($koneksi);
}
?>
