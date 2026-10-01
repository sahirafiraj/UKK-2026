<?php 
include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo 'Anda tidak memiliki akses';
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($koneksi, "DELETE FROM t_kelas WHERE id='$id'");

if ($query) {
    header("Location: data_kelas.php");
    exit;
} else {
    echo "Data gagal dihapus";
}
?>