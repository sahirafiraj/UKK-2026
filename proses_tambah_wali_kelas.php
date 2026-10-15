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
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $guru_id = (int) $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    if ($tanggal_mulai > $tanggal_selesai) {
        echo "Tanggal mulai tidak boleh melebihi tanggal selesai.";
        echo "<br><a href='tambah_wali_kelas.php'>Kembali</a>";
        exit;
    }

    $query = mysqli_query($koneksi, "INSERT INTO t_wali_kelas
        (tahun_ajaran_id, kelas_id, guru_id, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ($tahun_ajaran_id, $kelas_id, $guru_id, '$tanggal_mulai', '$tanggal_selesai', $status_aktif)");

    if ($query) {
        header("Location: data_wali_kelas.php");
        exit;
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
        echo "<br><a href='tambah_wali_kelas.php'>Kembali</a>";
    }
} else {
    header("Location: tambah_wali_kelas.php");
    exit;
}
?>