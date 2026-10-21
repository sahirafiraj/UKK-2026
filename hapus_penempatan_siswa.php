
<?php
session_start();

include "config/koneksi.php";

// Cek login dan hak akses Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Ambil ID dari URL
if (isset($_GET['id']) && ctype_digit($_GET['id']) && (int) $_GET['id'] > 0) {

    $id = (int) $_GET['id'];

    // Hapus data penempatan siswa
    $query = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_kelas_siswa WHERE id = ?"
    );

    mysqli_stmt_bind_param($query, "i", $id);

    if (mysqli_stmt_execute($query)) {
        if (mysqli_stmt_affected_rows($query) > 0) {
            $_SESSION['pesan_sukses'] =
                "Data penempatan siswa berhasil dihapus.";
        } else {
            $_SESSION['pesan_error'] =
                "Data penempatan siswa tidak ditemukan.";
        }
    } else {
        $_SESSION['pesan_error'] =
            "Data gagal dihapus: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($query);

} else {
    $_SESSION['pesan_error'] = "ID penempatan siswa tidak valid.";
}

// Kembali ke halaman data
header("Location: data_penempatan_siswa.php");
exit;
?>
