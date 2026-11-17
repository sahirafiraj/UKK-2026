<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// CEK HAK AKSES
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// AMBIL DATA FORM
$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
$status_aktif = (int) $_POST['status_aktif'];

// VALIDASI
if (empty($nama) || empty($deskripsi)) {
    echo "Nama dan deskripsi kategori wajib diisi.";
    exit;
}

// TAMBAHKAN KATEGORI
$query = mysqli_query($koneksi, "
    INSERT INTO t_pelanggaran_kategori
    (nama, deskripsi, status_aktif)
    VALUES
    ('$nama', '$deskripsi', '$status_aktif')
");

// CEK HASIL
if ($query) {
    header("Location: data_kategori_pelanggaran.php");
    exit;
} else {
    echo "Data kategori pelanggaran gagal ditambahkan.<br>";
    echo "Error: " . mysqli_error($koneksi);
}
?>