
<?php
session_start();

include "config/koneksi.php";

// Cek login dan hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Proses tambah siswa
if (isset($_POST['simpan'])) {

    // Mengambil data dari form
    $nis = mysqli_real_escape_string($koneksi, trim($_POST['nis']));
    $nisn = mysqli_real_escape_string($koneksi, trim($_POST['nisn']));
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $jk = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $status = (int) $_POST['status_aktif'];

    // Validasi NIS dan NISN maksimal 10 karakter
    if (strlen($nis) > 10 || strlen($nisn) > 10) {
        echo "<script>
                alert('NIS dan NISN maksimal 10 karakter!');
                window.location='tambah_siswa.php';
              </script>";
        exit;
    }

    // Validasi NISN hanya boleh berisi angka
    if (!ctype_digit($nisn)) {
        echo "<script>
                alert('NISN hanya boleh berisi angka!');
                window.location='tambah_siswa.php';
              </script>";
        exit;
    }

    // Simpan data ke database
    $query = mysqli_query($koneksi, "
        INSERT INTO t_siswa
        (
            nis,
            nisn,
            nama,
            jenis_kelamin,
            tanggal_lahir,
            alamat,
            status_aktif
        )
        VALUES
        (
            '$nis',
            '$nisn',
            '$nama',
            '$jk',
            '$tanggal',
            '$alamat',
            '$status'
        )
    ");

    // Cek hasil penyimpanan
    if ($query) {
        echo "<script>
                alert('Data siswa berhasil ditambahkan!');
                window.location='data_siswa.php';
              </script>";
        exit;
    } else {
        echo "Data siswa gagal ditambahkan: " . mysqli_error($koneksi);
    }

} else {
    header("Location: tambah_siswa.php");
    exit;
}
?>
