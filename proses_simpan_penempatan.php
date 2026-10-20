
<?php
session_start();

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Periksa login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Periksa hak akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_penempatan_siswa.php");
    exit;
}

// Ambil data dari form
$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id = (int) ($_POST['kelas_id'] ?? 0);
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status_aktif = isset($_POST['status_aktif'])
    ? (int) $_POST['status_aktif']
    : -1;

// Validasi data wajib
if (
    $siswa_id <= 0 ||
    $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 ||
    !in_array($status_aktif, [0, 1], true) ||
    $tanggal_mulai === '' ||
    $tanggal_selesai === ''
) {
    $_SESSION['pesan_error'] = 'Semua data wajib diisi dengan benar.';
    header("Location: data_penempatan_siswa.php");
    exit;
}

// Validasi format tanggal
$mulai = DateTime::createFromFormat('!Y-m-d', $tanggal_mulai);
$selesai = DateTime::createFromFormat('!Y-m-d', $tanggal_selesai);

if (
    !$mulai ||
    !$selesai ||
    $mulai->format('Y-m-d') !== $tanggal_mulai ||
    $selesai->format('Y-m-d') !== $tanggal_selesai ||
    $tanggal_selesai < $tanggal_mulai
) {
    $_SESSION['pesan_error'] =
        'Tanggal tidak valid. Tanggal selesai harus setelah atau sama dengan tanggal mulai.';
    header("Location: data_penempatan_siswa.php");
    exit;
}

// Simpan data penempatan
$stmt = mysqli_prepare(
    $koneksi,
    "INSERT INTO t_kelas_siswa
    (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif)
    VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    $_SESSION['pesan_error'] = 'Gagal menyiapkan penyimpanan data.';
    header("Location: data_penempatan_siswa.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "iisssi",
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = 'Data penempatan siswa berhasil disimpan.';
} else {
    $_SESSION['pesan_error'] =
        'Gagal menyimpan data penempatan siswa: ' . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);

header("Location: data_penempatan_siswa.php");
exit;
?>