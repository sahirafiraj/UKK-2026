
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (strtolower(trim($_SESSION['role'] ?? '')) != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: tambah_penempatan_siswa.php");
    exit;
}

$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id = (int) ($_POST['kelas_id'] ?? 0);
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status_aktif = (int) ($_POST['status_aktif'] ?? 0);

if (
    $siswa_id <= 0 ||
    $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 ||
    empty($tanggal_mulai) ||
    empty($tanggal_selesai) ||
    !in_array($status_aktif, [0, 1])
) {
    echo "<script>
        alert('Semua data wajib diisi!');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

if ($tanggal_selesai < $tanggal_mulai) {
    echo "<script>
        alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// CEK SISWA
$cek_siswa = mysqli_query($koneksi, "
    SELECT id FROM t_siswa
    WHERE id = '$siswa_id'
    AND status_aktif = 1
");

if (!$cek_siswa || mysqli_num_rows($cek_siswa) == 0) {
    echo "<script>
        alert('Data siswa tidak ditemukan atau tidak aktif!');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// CEK TAHUN AJARAN
$cek_tahun = mysqli_query($koneksi, "
    SELECT id FROM t_tahun_ajaran
    WHERE id = '$tahun_ajaran_id'
    AND status_aktif = 1
");

if (!$cek_tahun || mysqli_num_rows($cek_tahun) == 0) {
    echo "<script>
        alert('Tahun ajaran tidak ditemukan atau tidak aktif!');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// CEK KELAS
$cek_kelas = mysqli_query($koneksi, "
    SELECT id FROM t_kelas
    WHERE id = '$kelas_id'
    AND status_aktif = 1
");

if (!$cek_kelas || mysqli_num_rows($cek_kelas) == 0) {
    echo "<script>
        alert('Kelas tidak ditemukan atau tidak aktif!');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// CEK PENEMPATAN GANDA
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
        alert('Siswa sudah ditempatkan pada tahun ajaran tersebut!');
        window.location='tambah_penempatan_siswa.php';
    </script>";
    exit;
}

// SIMPAN DATA
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

if ($query) {
    echo "<script>
        alert('Data penempatan siswa berhasil ditambahkan!');
        window.location='data_penempatan_siswa.php';
    </script>";
} else {
    echo "<script>
        alert('Data gagal ditambahkan: " . addslashes(mysqli_error($koneksi)) . "');
        window.location='tambah_penempatan_siswa.php';
    </script>";
}

exit;
