
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

$id = $_GET['id'];

mysqli_begin_transaction($koneksi);

$hapus_siswa = mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran_siswa
    WHERE pelanggaran_id IN (
        SELECT id FROM t_pelanggaran
        WHERE pelanggaran_kategori_id = '$id'
    )
");

$hapus_pelanggaran = mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran
    WHERE pelanggaran_kategori_id = '$id'
");

$hapus_kategori = mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran_kategori
    WHERE id = '$id'
");

if ($hapus_siswa && $hapus_pelanggaran && $hapus_kategori) {
    mysqli_commit($koneksi);
    header("Location: data_kategori_pelanggaran.php");
    exit;
} else {
    mysqli_rollback($koneksi);
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>