<?php
session_start();

include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo 'Anda tidak memiliki akses';
    exit;
}

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];

    $query = mysqli_query($koneksi, " INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif) 
    VALUES ('$nama', '$tingkat', '$jurusan', 1)");

    if ($query) {
        header("Location: data_kelas.php");
        exit;
    } else {
        echo 'Data gagal ditambahkan';
    }
}
?>