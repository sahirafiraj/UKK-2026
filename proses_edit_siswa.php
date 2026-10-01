<?php

session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jk = $_POST['jenis_kelamin'];
    $tanggal = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status = $_POST['status_aktif'];

    mysqli_query($koneksi, "
        UPDATE t_siswa SET
            nis='$nis',
            nisn='$nisn',
            nama='$nama',
            jenis_kelamin='$jk',
            tanggal_lahir='$tanggal',
            alamat='$alamat',
            status_aktif='$status'
        WHERE id='$id'
    ");

    header("Location: data_siswa.php");
    exit;
}

?>