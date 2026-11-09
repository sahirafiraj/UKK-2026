
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
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_guru']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status = ($_POST['status'] == 'aktif') ? 1 : 0;

    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Cek data guru
    $query_guru = mysqli_query($koneksi, "
        SELECT * FROM t_guru
        WHERE id = '$id'
    ");

    if (mysqli_num_rows($query_guru) == 0) {
        echo "Data guru tidak ditemukan";
        exit;
    }

    $guru = mysqli_fetch_assoc($query_guru);
    $user_id = $guru['user_id'];

    // Jika password diisi
    if ($password != '') {

        // Cek konfirmasi password
        if ($password != $konfirmasi_password) {
            echo "<script>
                alert('Konfirmasi password tidak sama!');
                window.history.back();
            </script>";
            exit;
        }

        // Hash password baru
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Update password di t_users
        $password_hash = mysqli_real_escape_string($koneksi, $password_hash);

        $update_password = mysqli_query($koneksi, "
            UPDATE t_users SET
                password = '$password_hash'
            WHERE id = '$user_id'
        ");

        if (!$update_password) {
            echo "Gagal mengubah password: " . mysqli_error($koneksi);
            exit;
        }

    } else {

        // Jika password kosong tetapi konfirmasi diisi
        if ($konfirmasi_password != '') {
            echo "<script>
                alert('Password baru harus diisi!');
                window.history.back();
            </script>";
            exit;
        }

    }

    // Update data guru
    $query = mysqli_query($koneksi, "
        UPDATE t_guru SET
            nip = '$nip',
            nama = '$nama',
            email = '$email',
            status_aktif = '$status'
        WHERE id = '$id'
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
?>