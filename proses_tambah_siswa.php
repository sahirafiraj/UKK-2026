<?php

session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['simpan'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jk = $_POST['jenis_kelamin'];
    $tanggal = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];

    mysqli_query($koneksi, "
        INSERT INTO t_siswa
        (
            nis,
            nisn,
            nama,
            jenis_kelamin,
            tanggal_lahir,
            alamat,
            status_aktif
        )
        VALUES
        (
            '$nis',
            '$nisn',
            '$nama',
            '$jk',
            '$tanggal',
            '$alamat',
            1
        )
    ");

    header("Location: data_siswa.php");
    exit;
}

?>