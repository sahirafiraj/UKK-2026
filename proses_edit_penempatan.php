
<?php
session_start();
include "config/koneksi.php";

// ==========================================
// 1. CEK LOGIN DAN HAK AKSES
// ==========================================

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "<script>
        alert('Anda tidak memiliki akses!');
        window.location='login.php';
    </script>";
    exit;
}

// ==========================================
// 2. CEK FORM
// ==========================================

if (!isset($_POST['update'])) {
    echo "<script>
        alert('GAGAL! Form edit tidak terkirim.');
        window.location='data_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 3. AMBIL DATA FORM
// ==========================================

$id = $_POST['id'] ?? '';
$siswa_id = $_POST['siswa_id'] ?? '';
$tahun_ajaran_id = $_POST['tahun_ajaran_id'] ?? '';
$kelas_id = $_POST['kelas_id'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = !empty($_POST['tanggal_selesai'])
    ? $_POST['tanggal_selesai']
    : null;
$status_aktif = $_POST['status_aktif'] ?? '';

// ==========================================
// 4. VALIDASI ID DAN STATUS
// ==========================================

if (
    !ctype_digit((string) $id) || (int) $id < 1 ||
    !ctype_digit((string) $siswa_id) || (int) $siswa_id < 1 ||
    !ctype_digit((string) $tahun_ajaran_id) || (int) $tahun_ajaran_id < 1 ||
    !ctype_digit((string) $kelas_id) || (int) $kelas_id < 1 ||
    !in_array((string) $status_aktif, ['0', '1'])
) {
    echo "<script>
        alert('GAGAL! Data penempatan tidak valid. Silakan periksa kembali form.');
        window.location='data_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 5. VALIDASI TANGGAL MULAI
// ==========================================

$tanggal_mulai_valid = DateTime::createFromFormat(
    '!Y-m-d',
    $tanggal_mulai
);

if (
    !$tanggal_mulai_valid ||
    $tanggal_mulai_valid->format('Y-m-d') !== $tanggal_mulai
) {
    echo "<script>
        alert('GAGAL! Tanggal mulai tidak valid.');
        window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
    </script>";
    exit;
}

// ==========================================
// 6. VALIDASI TANGGAL SELESAI
// ==========================================

if ($tanggal_selesai !== null) {

    $tanggal_selesai_valid = DateTime::createFromFormat(
        '!Y-m-d',
        $tanggal_selesai
    );

    if (
        !$tanggal_selesai_valid ||
        $tanggal_selesai_valid->format('Y-m-d') !== $tanggal_selesai ||
        $tanggal_selesai < $tanggal_mulai
    ) {
        echo "<script>
            alert('GAGAL! Tanggal selesai tidak valid atau lebih awal dari tanggal mulai.');
            window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
        </script>";
        exit;
    }
}

// ==========================================
// 7. CEK DATA PENEMPATAN
// ==========================================

$cek = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_kelas_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);

$data_ada = mysqli_stmt_num_rows($cek) > 0;

mysqli_stmt_close($cek);

if (!$data_ada) {
    echo "<script>
        alert('GAGAL! Data penempatan siswa tidak ditemukan.');
        window.location='data_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 8. CEK SISWA
// ==========================================

$cek_siswa = mysqli_query($koneksi, "
    SELECT id FROM t_siswa
    WHERE id = '$siswa_id'
    AND status_aktif = 1
");

if (!$cek_siswa || mysqli_num_rows($cek_siswa) == 0) {
    echo "<script>
        alert('GAGAL! Data siswa tidak ditemukan atau sudah tidak aktif.');
        window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
    </script>";
    exit;
}

// ==========================================
// 9. CEK TAHUN AJARAN
// ==========================================

$cek_tahun = mysqli_query($koneksi, "
    SELECT id FROM t_tahun_ajaran
    WHERE id = '$tahun_ajaran_id'
    AND status_aktif = 1
");

if (!$cek_tahun || mysqli_num_rows($cek_tahun) == 0) {
    echo "<script>
        alert('GAGAL! Tahun ajaran tidak ditemukan atau sudah tidak aktif.');
        window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
    </script>";
    exit;
}

// ==========================================
// 10. CEK KELAS
// ==========================================

$cek_kelas = mysqli_query($koneksi, "
    SELECT id FROM t_kelas
    WHERE id = '$kelas_id'
    AND status_aktif = 1
");

if (!$cek_kelas || mysqli_num_rows($cek_kelas) == 0) {
    echo "<script>
        alert('GAGAL! Kelas tidak ditemukan atau sudah tidak aktif.');
        window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
    </script>";
    exit;
}

// ==========================================
// 11. CEK PENEMPATAN GANDA
// ==========================================

$cek_penempatan = mysqli_query($koneksi, "
    SELECT id
    FROM t_kelas_siswa
    WHERE siswa_id = '$siswa_id'
    AND tahun_ajaran_id = '$tahun_ajaran_id'
    AND id != '$id'
");

if (!$cek_penempatan) {
    echo "<script>
        alert('GAGAL! Pemeriksaan penempatan siswa bermasalah.');
        window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
    </script>";
    exit;
}

if (mysqli_num_rows($cek_penempatan) > 0) {
    echo "<script>
        alert('GAGAL MENGEDIT PENEMPATAN! Siswa sudah terdaftar pada tahun ajaran tersebut. Siswa tidak dapat ditempatkan dua kali dalam tahun ajaran yang sama.');
        window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
    </script>";
    exit;
}

// ==========================================
// 12. UPDATE DATA PENEMPATAN
// ==========================================

$query = mysqli_prepare(
    $koneksi,
    "UPDATE t_kelas_siswa
     SET siswa_id = ?,
         tahun_ajaran_id = ?,
         kelas_id = ?,
         tanggal_mulai = ?,
         tanggal_selesai = ?,
         status_aktif = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $query,
    "iiissii",
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif,
    $id
);

// ==========================================
// 13. NOTIFIKASI HASIL EDIT
// ==========================================

if (mysqli_stmt_execute($query)) {

    mysqli_stmt_close($query);

    echo "<script>
        alert('BERHASIL! Data penempatan siswa berhasil diperbarui.');
        window.location='data_penempatan_siswa.php';
    </script>";
    exit;

} else {

    $error = mysqli_error($koneksi);

    mysqli_stmt_close($query);

    echo "<script>
        alert('GAGAL! Data penempatan siswa tidak berhasil diperbarui.');
        window.location='edit_penempatan_siswa.php?id=" . (int) $id . "';
    </script>";
    exit;
}
?>
