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

    // Memulai transaksi agar status aktif tetap konsisten
    mysqli_begin_transaction($koneksi);

    // Jika data yang diedit diaktifkan, nonaktifkan semua tahun ajaran lain
    if ($status_aktif == 1) {
        $nonaktifkan = mysqli_query(
            $koneksi,
            "UPDATE t_tahun_ajaran SET status_aktif = 0 WHERE id != $id"
        );
    } else {
        $nonaktifkan = true;
    }

    // Memperbarui data tahun ajaran yang dipilih
    if ($nonaktifkan) {
        $query = mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET
            nama = '$nama',
            tanggal_mulai = '$tanggal_mulai',
            tanggal_selesai = '$tanggal_selesai',
            status_aktif = '$status_aktif'
            WHERE id = $id");
    } else {
        $query = false;
    }

    if ($query) {
        mysqli_commit($koneksi);
        header("Location: data_tahun_ajaran.php");
        exit;
    } else {
        mysqli_rollback($koneksi);
        echo "Gagal mengubah data: " . mysqli_error($koneksi);
        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
    }
} else {
    header("Location: data_tahun_ajaran.php");
    exit;
}
?>