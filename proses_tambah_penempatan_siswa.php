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

// CEK METHOD POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: tambah_penempatan_siswa.php");
    exit;
}

// AMBIL DATA FORM
$siswa_id = (int) $_POST['siswa_id'];
$kelas_id = (int) $_POST['kelas_id'];
$tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
$tanggal_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
$tanggal_selesai = mysqli_real_escape_string($koneksi, $_POST['tanggal_selesai']);
$status_aktif = (int) $_POST['status_aktif'];

// SIMPAN DATA PENEMPATAN SISWA
$query = mysqli_query($koneksi, "
    INSERT INTO t_kelas_siswa
    (
        siswa_id,
        kelas_id,
        tahun_ajaran_id,
        tanggal_mulai,
        tanggal_selesai,
        status_aktif
    )
    VALUES
    (
        '$siswa_id',
        '$kelas_id',
        '$tahun_ajaran_id',
        '$tanggal_mulai',
        '$tanggal_selesai',
        '$status_aktif'
    )
");

// CEK HASIL
if ($query) {
    header("Location: data_penempatan_siswa.php");
    exit;
} else {
    die("Gagal menambahkan penempatan siswa: " . mysqli_error($koneksi));
}
?>