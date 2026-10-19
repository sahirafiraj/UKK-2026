
<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['simpan'])) {
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = (int) $_POST['kategori'];
    $poin = (int) $_POST['poin'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $aktif = (int) $_POST['aktif'];

    $query = mysqli_query($koneksi, "
        INSERT INTO t_pelanggaran
        (kode, nama, pelanggaran_kategori_id, poin, deskripsi, aktif)
        VALUES
        ('$kode', '$nama', $kategori, $poin, '$deskripsi', $aktif)
    ");

    if ($query) {
        header("Location: data_jenis_pelanggaran.php");
        exit;
    } else {
        die("Gagal menambah data: " . mysqli_error($koneksi));
    }
}

header("Location: tambah_jenis_pelanggaran.php");
exit;
?>
