
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// 1. CEK LOGIN DAN HAK AKSES
// ==========================================

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (strtolower(trim($_SESSION['role'] ?? '')) != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// ==========================================
// 2. CEK METHOD POST
// ==========================================

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: tambah_penempatan_siswa.php");
    exit;
}

// ==========================================
// 3. AMBIL DATA FORM
// ==========================================

$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id = (int) ($_POST['kelas_id'] ?? 0);
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status_aktif = (int) ($_POST['status_aktif'] ?? 0);

// ==========================================
// 4. VALIDASI DATA
// ==========================================

if (
    $siswa_id <= 0 ||
    $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 ||
    empty($tanggal_mulai) ||
    empty($tanggal_selesai) ||
    !in_array($status_aktif, [0, 1])
) {
    echo "<script>
        alert('Penempatan gagal! Semua data wajib diisi dengan lengkap.');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

if ($tanggal_selesai < $tanggal_mulai) {
    echo "<script>
        alert('Penempatan gagal! Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 5. CEK DATA SISWA
// ==========================================

$cek_siswa = mysqli_query($koneksi, "
    SELECT id FROM t_siswa
    WHERE id = '$siswa_id'
    AND status_aktif = 1
");

if (!$cek_siswa || mysqli_num_rows($cek_siswa) == 0) {
    echo "<script>
        alert('Penempatan gagal! Data siswa tidak ditemukan atau sudah tidak aktif.');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 6. CEK TAHUN AJARAN
// ==========================================

$cek_tahun = mysqli_query($koneksi, "
    SELECT id FROM t_tahun_ajaran
    WHERE id = '$tahun_ajaran_id'
    AND status_aktif = 1
");

if (!$cek_tahun || mysqli_num_rows($cek_tahun) == 0) {
    echo "<script>
        alert('Penempatan gagal! Tahun ajaran tidak ditemukan atau sudah tidak aktif.');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 7. CEK KELAS
// ==========================================

$cek_kelas = mysqli_query($koneksi, "
    SELECT id FROM t_kelas
    WHERE id = '$kelas_id'
    AND status_aktif = 1
");

if (!$cek_kelas || mysqli_num_rows($cek_kelas) == 0) {
    echo "<script>
        alert('Penempatan gagal! Kelas tidak ditemukan atau sudah tidak aktif.');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 8. CEK PENEMPATAN GANDA
// ==========================================

$cek_penempatan = mysqli_query($koneksi, "
    SELECT id
    FROM t_kelas_siswa
    WHERE siswa_id = '$siswa_id'
    AND tahun_ajaran_id = '$tahun_ajaran_id'
");

if (!$cek_penempatan) {
    die("Gagal memeriksa penempatan: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($cek_penempatan) > 0) {
    echo "<script>
        alert('Penempatan gagal! Siswa sudah terdaftar pada tahun ajaran ini. Silakan periksa kembali data penempatan siswa.');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// ==========================================
// 9. SIMPAN DATA PENEMPATAN
// ==========================================

$query = mysqli_query($koneksi, "
    INSERT INTO t_kelas_siswa (
        siswa_id,
        tahun_ajaran_id,
        kelas_id,
        tanggal_mulai,
        tanggal_selesai,
        status_aktif
    ) VALUES (
        '$siswa_id',
        '$tahun_ajaran_id',
        '$kelas_id',
        '$tanggal_mulai',
        '$tanggal_selesai',
        '$status_aktif'
    )
");

// ==========================================
// 10. NOTIFIKASI HASIL
// ==========================================

if ($query) {
    echo "<script>
        alert('Berhasil! Data penempatan siswa telah ditambahkan.');
        window.location='data_penempatan_siswa.php';
    </script>";
} else {
    echo "<script>
        alert('Penempatan gagal! Terjadi kesalahan saat menyimpan data.');
        window.location='tambah_penempatan_siswa.php';
    </script>";
}

exit;
?>
