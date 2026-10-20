
<?php
include 'config/koneksi.php';

$nip = $_POST['nip'];
$nama = $_POST['nama_guru'];
$email = $_POST['email'];
$password = $_POST['password'];
$konfirmasi_password = $_POST['konfirmasi_password'];
$status = $_POST['status'];

if ($password != $konfirmasi_password) {
    echo "<script>alert('Konfirmasi password tidak cocok'); window.history.back();</script>";
} else {
    $name = $nama;
    $role = 'guru';

    $query_user = mysqli_query($koneksi, "INSERT INTO t_users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");

    if ($query_user) {
        $user_id = mysqli_insert_id($koneksi);

        if ($status == 'aktif') {
            $status_aktif = 1;
        } else {
            $status_aktif = 0;
        }

        $query_guru = mysqli_query($koneksi, "INSERT INTO t_guru (nip, nama, email, status_aktif, user_id) VALUES ('$nip', '$nama', '$email', '$status_aktif', '$user_id')");

        if ($query_guru) {
            echo "<script>alert('Data guru berhasil ditambahkan'); window.location='data_guru.php';</script>";
        } else {
            echo "Gagal menambahkan data guru";
        }
    } else {
        echo "Gagal membuat akun pengguna";
    }
}
?>
