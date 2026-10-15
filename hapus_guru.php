<?php

include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$id = $_GET['id'];

mysqli_query(
    $koneksi,
    "DELETE FROM t_guru WHERE id='$id'"
);

header("Location: data_guru.php");
exit;