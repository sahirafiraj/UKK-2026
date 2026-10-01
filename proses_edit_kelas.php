<?php
include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "
        UPDATE t_kelas SET
            nama='$nama',
            tingkat='$tingkat',
            jurusan='$jurusan',
            status_aktif='$status_aktif'
        WHERE id='$id'
    ");

    if ($query) {
        header("Location: data_kelas.php");
        exit;
    } else {
        echo "Data gagal diubah";
    }
}
?>