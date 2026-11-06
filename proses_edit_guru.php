```php
<?php
include "config/koneksi.php";
include "includes/cek_session.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status = (int) $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "
        UPDATE t_guru SET
            nip='$nip',
            nama='$nama',
            email='$email',
            status_aktif='$status'
        WHERE id='$id'
    ");

    if ($query) {
        header("Location: data_guru.php");
        exit;
    } else {
        echo "Gagal mengubah data guru: " . mysqli_error($koneksi);
    }

} else {
    header("Location: data_guru.php");
    exit;
}