<?php 
include 'config/koneksi.php';

$nama = 'User 01';
$email = 'user01@sekolah.test';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';

$sql = "INSERT INTO t_users (name, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";

if (mysqli_query($koneksi, $sql)) {
    echo 'User admin berhasil dibuat. <br>';
} else {
    echo 'Gagal membuat user: ' .
    mysqli_error($koneksi);
}

$nama = 'User 02';
$email = 'user02@sekolah.test';
$password = password_hash('guru123', PASSWORD_DEFAULT);
$role = 'guru';

$sql = "INSERT INTO t_users (name, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";

if (mysqli_query($koneksi, $sql)) {
    echo 'User guru berhasil dibuat.';
} else {
    echo 'Gagal membuat user: ' .
    mysqli_error($koneksi);
}