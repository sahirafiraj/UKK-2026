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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_wali_kelas.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query($koneksi, "DELETE FROM t_wali_kelas WHERE id = $id");

if ($query) {
    header("Location: data_wali_kelas.php");
    exit;
} else {
    echo "Gagal menghapus data wali kelas.";
    echo "<br><a href='data_wali_kelas.php'>Kembali</a>";
}
?>