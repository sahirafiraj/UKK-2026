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
    header("Location: data_tahun_ajaran.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query($koneksi, "DELETE FROM t_tahun_ajaran WHERE id = $id");

if ($query) {
    header("Location: data_tahun_ajaran.php");
    exit;
} else {
    echo "Gagal menghapus data. Tahun ajaran mungkin masih digunakan oleh data lain.";
    echo "<br><a href='data_tahun_ajaran.php'>Kembali</a>";
}
?>