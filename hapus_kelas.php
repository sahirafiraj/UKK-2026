
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
    header("Location: data_kelas.php");
    exit;
}

$id = (int) $_GET['id'];

mysqli_begin_transaction($koneksi);

// 1. Hapus data pelanggaran siswa yang terkait kelas
$hapus_pelanggaran = mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran_siswa
    WHERE kelas_id = $id
");

if ($hapus_pelanggaran) {

    // 2. Hapus data penempatan kelas siswa
    $hapus_siswa = mysqli_query($koneksi, "
        DELETE FROM t_kelas_siswa
        WHERE kelas_id = $id
    ");

    if ($hapus_siswa) {

        // 3. Hapus data wali kelas
        $hapus_wali = mysqli_query($koneksi, "
            DELETE FROM t_wali_kelas
            WHERE kelas_id = $id
        ");

        if ($hapus_wali) {

            // 4. Hapus kelas
            $hapus_kelas = mysqli_query($koneksi, "
                DELETE FROM t_kelas
                WHERE id = $id
            ");

            if ($hapus_kelas) {
                mysqli_commit($koneksi);
                header("Location: data_kelas.php");
                exit;
            }
        }
    }
}

// Batalkan semua perubahan jika gagal
mysqli_rollback($koneksi);

echo "Kelas gagal dihapus karena masih terhubung dengan tabel lain.";
echo "<br><a href='data_kelas.php'>Kembali</a>";
?>
