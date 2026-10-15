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

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    if ($tanggal_mulai > $tanggal_selesai) {
        echo "Tanggal mulai tidak boleh melebihi tanggal selesai.";
        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
        exit;
    }

    $query = mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET
        nama = '$nama',
        tanggal_mulai = '$tanggal_mulai',
        tanggal_selesai = '$tanggal_selesai',
        status_aktif = '$status_aktif'
        WHERE id = $id");

    if ($query) {
        header("Location: data_tahun_ajaran.php");
        exit;
    } else {
        echo "Gagal mengubah data: " . mysqli_error($koneksi);
        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
    }
} else {
    header("Location: data_tahun_ajaran.php");
    exit;
}
?>