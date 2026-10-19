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

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: data_jenis_pelanggaran.php");
    exit;
}

if (
    !isset($_POST['id']) ||
    !isset($_POST['pelanggaran_kategori_id']) ||
    !isset($_POST['kode']) ||
    !isset($_POST['nama']) ||
    !isset($_POST['poin']) ||
    !isset($_POST['status_aktif'])
) {
    echo "Data yang dikirim belum lengkap.";
    exit;
}

$id = (int) $_POST['id'];
$kategori = (int) $_POST['pelanggaran_kategori_id'];
$kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$poin = (int) $_POST['poin'];
$deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?? '');
$status_aktif = (int) $_POST['status_aktif'];

/* Pastikan status hanya 0 atau 1 */
if ($status_aktif != 0 && $status_aktif != 1) {
    echo "Status tidak valid.";
    exit;
}

/* Update data */
$query = mysqli_query($koneksi, "UPDATE t_pelanggaran SET
    pelanggaran_kategori_id = '$kategori',
    kode = '$kode',
    nama = '$nama',
    poin = '$poin',
    deskripsi = '$deskripsi',
    status_aktif = '$status_aktif'
    WHERE id = '$id'
");

if ($query) {
    header("Location: data_jenis_pelanggaran.php?pesan=berhasil");
    exit;
} else {
    echo "Gagal mengubah data: " . mysqli_error($koneksi);
}
?>