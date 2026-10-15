
<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    mysqli_begin_transaction($koneksi);

    // Hapus data pelanggaran siswa
    $query1 = mysqli_query($koneksi, "
        DELETE FROM t_pelanggaran_siswa
        WHERE siswa_id = $id
    ");

    // Hapus penempatan siswa di kelas
    $query2 = mysqli_query($koneksi, "
        DELETE FROM t_kelas_siswa
        WHERE siswa_id = $id
    ");

    // Hapus data siswa
    $query3 = mysqli_query($koneksi, "
        DELETE FROM t_siswa
        WHERE id = $id
    ");

    if ($query1 && $query2 && $query3) {
        mysqli_commit($koneksi);

        header("Location: data_siswa.php");
        exit;
    } else {
        mysqli_rollback($koneksi);

        echo "Gagal menghapus data: " . mysqli_error($koneksi);
        exit;
    }

} else {
    echo "ID siswa tidak ditemukan!";
}
?>
