<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['update'])) {

    $id = $_GET['id'];

    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $guru_id = $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    // Cek apakah kelas sudah mempunyai wali kelas lain
    if ($status_aktif == 1) {

        $cek = mysqli_query($koneksi, "
            SELECT * FROM t_wali_kelas
            WHERE tahun_ajaran_id = '$tahun_ajaran_id'
            AND kelas_id = '$kelas_id'
            AND status_aktif = 1
            AND id != '$id'
        ");

        if (mysqli_num_rows($cek) > 0) {
            echo "<script>
                alert('Kelas tersebut sudah memiliki wali kelas!');
                window.location='edit_wali_kelas.php?id=$id';
            </script>";
            exit;
        }
    }

    // Update data
    $query = mysqli_query($koneksi, "
        UPDATE t_wali_kelas SET
            tahun_ajaran_id = '$tahun_ajaran_id',
            kelas_id = '$kelas_id',
            guru_id = '$guru_id',
            tanggal_mulai = '$tanggal_mulai',
            tanggal_selesai = " . ($tanggal_selesai == '' ? "NULL" : "'$tanggal_selesai'") . ",
            status_aktif = '$status_aktif'
        WHERE id = '$id'
    ");

    if ($query) {
        echo "<script>
            alert('Data wali kelas berhasil diperbarui!');
            window.location='data_wali_kelas.php';
        </script>";
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>