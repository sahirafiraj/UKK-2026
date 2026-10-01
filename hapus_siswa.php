<?php

session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$id = $_GET['id'];

mysqli_query($koneksi, "
    DELETE FROM t_siswa
    WHERE id='$id'
");

header("Location: data_siswa.php");
exit;

?>