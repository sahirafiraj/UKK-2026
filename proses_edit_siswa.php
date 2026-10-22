
<?php

session_start();
include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['update'])) {

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jk = $_POST['jenis_kelamin'] ?? '';
    $tanggal = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif']
        : -1;

    // Validasi ID
    if ($id <= 0) {
        echo "ID siswa tidak valid.";
        exit;
    }

    // Validasi panjang NIS dan NISN
    if (strlen($nis) > 10 || strlen($nisn) > 10) {
        $_SESSION['pesan_error'] = "NIS dan NISN maksimal 10 karakter.";
        header("Location: edit_siswa.php?id=" . $id);
        exit;
    }

    // Validasi data wajib
    if ($nis == '' || $nisn == '' || $nama == '' ||
        $jk == '' || $tanggal == '' || $alamat == '' ||
        !in_array($status, [0, 1], true)) {

        $_SESSION['pesan_error'] = "Periksa kembali semua data yang wajib diisi.";
        header("Location: edit_siswa.php?id=" . $id);
        exit;
    }

    // Mengamankan data sebelum query SQL
    $nis = mysqli_real_escape_string($koneksi, $nis);
    $nisn = mysqli_real_escape_string($koneksi, $nisn);
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $jk = mysqli_real_escape_string($koneksi, $jk);
    $tanggal = mysqli_real_escape_string($koneksi, $tanggal);
    $alamat = mysqli_real_escape_string($koneksi, $alamat);

    // Memperbarui data siswa
    $query = mysqli_query($koneksi, "
        UPDATE t_siswa SET
            nis = '$nis',
            nisn = '$nisn',
            nama = '$nama',
            jenis_kelamin = '$jk',
            tanggal_lahir = '$tanggal',
            alamat = '$alamat',
            status_aktif = $status
        WHERE id = $id
    ");

    if ($query) {
        header("Location: data_siswa.php");
        exit;
    } else {
        $_SESSION['pesan_error'] = "Gagal memperbarui data siswa. Silakan coba lagi.";
        header("Location: edit_siswa.php?id=" . $id);
        exit;
    }

} else {
    header("Location: data_siswa.php");
    exit;
}

?>
