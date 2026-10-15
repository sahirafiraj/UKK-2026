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

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    if ($tanggal_mulai > $tanggal_selesai) {
        echo "Tanggal mulai tidak boleh melebihi tanggal selesai.";
        echo "<br><a href='tambah_tahun_ajaran.php'>Kembali</a>";
        exit;
    }

    $query = mysqli_query($koneksi, "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')");

    if ($query) {
        header("Location: data_tahun_ajaran.php");
        exit;
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
        echo "<br><a href='tambah_tahun_ajaran.php'>Kembali</a>";
    }
} else {
    header("Location: tambah_tahun_ajaran.php");
    exit;
}
?>