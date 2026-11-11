
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// CEK HAK AKSES
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// CEK ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_tahun_ajaran.php");
    exit;
}

$id = (int) $_GET['id'];

// CEK DATA TAHUN AJARAN
$query = mysqli_query($koneksi, "
    SELECT * FROM t_tahun_ajaran
    WHERE id = $id
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: data_tahun_ajaran.php");
    exit;
}

// PROSES UPDATE
if (isset($_POST['update'])) {

    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    // VALIDASI
    if (
        empty($nama) ||
        empty($tanggal_mulai) ||
        empty($tanggal_selesai)
    ) {
        echo "<script>
            alert('Semua data wajib diisi!');
            window.location='edit_tahun_ajaran.php?id=$id';
        </script>";
        exit;
    }

    if ($tanggal_selesai < $tanggal_mulai) {
        echo "<script>
            alert('Tanggal selesai tidak boleh sebelum tanggal mulai!');
            window.location='edit_tahun_ajaran.php?id=$id';
        </script>";
        exit;
    }

    // CEK NAMA TAHUN AJARAN DUPLIKAT
    $cek = mysqli_query($koneksi, "
        SELECT id FROM t_tahun_ajaran
        WHERE nama = '$nama'
        AND id != $id
    ");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('Tahun ajaran sudah tersedia!');
            window.location='edit_tahun_ajaran.php?id=$id';
        </script>";
        exit;
    }

    mysqli_begin_transaction($koneksi);

    // JIKA AKTIF, NONAKTIFKAN YANG LAIN
    if ($status_aktif == 1) {
        mysqli_query($koneksi, "
            UPDATE t_tahun_ajaran
            SET status_aktif = 0
            WHERE id != $id
        ");
    }

    // UPDATE DATA
    $update = mysqli_query($koneksi, "
        UPDATE t_tahun_ajaran SET
            nama = '$nama',
            tanggal_mulai = '$tanggal_mulai',
            tanggal_selesai = '$tanggal_selesai',
            status_aktif = $status_aktif,
            updated_at = NOW()
        WHERE id = $id
    ");

    if ($update) {
        mysqli_commit($koneksi);

        echo "<script>
            alert('Data tahun ajaran berhasil diperbarui!');
            window.location='data_tahun_ajaran.php';
        </script>";
        exit;
    } else {
        mysqli_rollback($koneksi);

        echo "<script>
            alert('Gagal memperbarui data tahun ajaran!');
            window.location='edit_tahun_ajaran.php?id=$id';
        </script>";
        exit;
    }
}
?>
