
<?php
session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jk = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status = (int) $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "
        UPDATE t_siswa SET
            nis = '$nis',
            nisn = '$nisn',
            nama = '$nama',
            jenis_kelamin = '$jk',
            tanggal_lahir = '$tanggal',
            alamat = '$alamat',
            status_aktif = '$status'
        WHERE id = $id
    ");

    if ($query) {
        header("Location: data_siswa.php");
        exit;
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($koneksi);
    }

} else {
    echo "Form edit belum dikirim dengan benar.";
}
?>
