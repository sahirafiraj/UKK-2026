
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Pengguna';
$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = $_SESSION['id'] ?? 0;

if (!in_array($role, ['admin', 'guru', 'walikelas'])) {
    echo "Anda tidak memiliki akses";
    exit;
}

// TAHUN AJARAN AKTIF
$query_tahun = mysqli_query($koneksi, "
    SELECT id FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
");

$data_tahun = mysqli_fetch_assoc($query_tahun);
$id_tahun = $data_tahun['id'] ?? 0;

// ID GURU LOGIN
$id_guru = 0;

if ($role == 'walikelas') {
    $query_guru = mysqli_query($koneksi, "
        SELECT id FROM t_guru
        WHERE user_id = '$id_user'
        LIMIT 1
    ");

    $data_guru = mysqli_fetch_assoc($query_guru);
    $id_guru = $data_guru['id'] ?? 0;
}

// JUMLAH SISWA
if ($role == 'walikelas') {
    $query_siswa = mysqli_query($koneksi, "
        SELECT COUNT(DISTINCT s.id) AS total
        FROM t_siswa s
        JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
        JOIN t_wali_kelas wk ON wk.kelas_id = ks.kelas_id
        WHERE ks.tahun_ajaran_id = '$id_tahun'
        AND wk.tahun_ajaran_id = '$id_tahun'
        AND wk.guru_id = '$id_guru'
        AND wk.status_aktif = 1
        AND s.status_aktif = 1
    ");
} else {
    $query_siswa = mysqli_query($koneksi, "
        SELECT COUNT(*) AS total
        FROM t_siswa
        WHERE status_aktif = 1
    ");
}

$data_siswa = mysqli_fetch_assoc($query_siswa);
$total_siswa = $data_siswa['total'] ?? 0;

// JUMLAH PELANGGARAN
if ($role == 'walikelas') {
    $query_pelanggaran = mysqli_query($koneksi, "
        SELECT COUNT(ps.id) AS total
        FROM t_pelanggaran_siswa ps
        JOIN t_siswa s ON s.id = ps.siswa_id
        JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
        JOIN t_wali_kelas wk ON wk.kelas_id = ks.kelas_id
        WHERE ks.tahun_ajaran_id = '$id_tahun'
        AND wk.tahun_ajaran_id = '$id_tahun'
        AND wk.guru_id = '$id_guru'
        AND wk.status_aktif = 1
    ");

    $query_poin = mysqli_query($koneksi, "
        SELECT SUM(ps.poin) AS total
        FROM t_pelanggaran_siswa ps
        JOIN t_siswa s ON s.id = ps.siswa_id
        JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
        JOIN t_wali_kelas wk ON wk.kelas_id = ks.kelas_id
        WHERE ks.tahun_ajaran_id = '$id_tahun'
        AND wk.tahun_ajaran_id = '$id_tahun'
        AND wk.guru_id = '$id_guru'
        AND wk.status_aktif = 1
    ");
} else {
    $query_pelanggaran = mysqli_query($koneksi, "
        SELECT COUNT(*) AS total FROM t_pelanggaran_siswa
    ");

    $query_poin = mysqli_query($koneksi, "
        SELECT SUM(poin) AS total FROM t_pelanggaran_siswa
    ");
}

$data_pelanggaran = mysqli_fetch_assoc($query_pelanggaran);
$total_pelanggaran = $data_pelanggaran['total'] ?? 0;

$data_poin = mysqli_fetch_assoc($query_poin);
$total_poin = $data_poin['total'] ?? 0;

$query_guru = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM t_guru
");
$data_guru = mysqli_fetch_assoc($query_guru);
$total_guru = $data_guru['total'] ?? 0;

$query_kategori = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM t_pelanggaran_kategori
");
$data_kategori = mysqli_fetch_assoc($query_kategori);
$total_kategori = $data_kategori['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

    <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

    <ul class="nav nav-pills flex-column gap-1">

        <?php if ($role == 'admin') { ?>

            <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
            <li><a href="data_siswa.php" class="nav-link text-white">Data Siswa</a></li>
            <li><a href="data_guru.php" class="nav-link text-white">Data Guru</a></li>
            <li><a href="data_kelas.php" class="nav-link text-white">Data Kelas</a></li>
            <li><a href="data_tahun_ajaran.php" class="nav-link text-white">Tahun Ajaran</a></li>
            <li><a href="data_penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a></li>
            <li><a href="data_wali_kelas.php" class="nav-link text-white">Data Wali Kelas</a></li>
            <li><a href="data_kategori_pelanggaran.php" class="nav-link text-white">Kategori Pelanggaran</a></li>
            <li><a href="data_jenis_pelanggaran.php" class="nav-link text-white">Jenis Pelanggaran</a></li>
            <li><a href="laporan.php" class="nav-link text-white">Laporan</a></li>

        <?php } elseif ($role == 'walikelas') { ?>

            <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
            <li><a href="data_siswa.php" class="nav-link text-white">Data Siswa</a></li>
            <li><a href="rekap_pelanggaran.php" class="nav-link text-white">Rekap Pelanggaran</a></li>
            <li><a href="cetak_rekap.php" class="nav-link text-white">Cetak Rekap</a></li>

        <?php } else { ?>

            <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
            <li><a href="catat_pelanggaran.php" class="nav-link text-white">Catat Pelanggaran</a></li>
            <li><a href="tindakan.php" class="nav-link text-white">Tindakan</a></li>
            <li><a href="riwayat.php" class="nav-link text-white">Riwayat</a></li>
            <li><a href="rekap_poin.php" class="nav-link text-white">Rekap Poin</a></li>

        <?php } ?>

        <?php if ($role != 'walikelas') { ?>
            <li><a href="about_me.php" class="nav-link text-white">About Me</a></li>
        <?php } ?>

        <hr class="border-white">

        <li><a href="logout.php" class="nav-link text-white">Logout</a></li>

    </ul>
</div>

<!-- KONTEN -->
<main class="content-main">
<div class="dashboard-content">

    <div class="dashboard-header">
        <h2>Dashboard</h2>
        <p>Selamat datang, <b><?= htmlspecialchars($nama_login); ?></b></p>
    </div>

    <div class="dashboard-cards">

        <?php if ($role == 'admin') { ?>

            <div class="dashboard-card">
                <p>Total Siswa</p>
                <h2><?= $total_siswa; ?></h2>
                <a href="data_siswa.php" class="btn btn-primary btn-sm">Lihat Data</a>
            </div>

            <div class="dashboard-card">
                <p>Total Guru</p>
                <h2><?= $total_guru; ?></h2>
                <a href="data_guru.php" class="btn btn-primary btn-sm">Lihat Data</a>
            </div>

            <div class="dashboard-card">
                <p>Data Pelanggaran</p>
                <h2><?= $total_pelanggaran; ?></h2>
                <a href="laporan.php" class="btn btn-primary btn-sm">Lihat Data</a>
            </div>

            <div class="dashboard-card">
                <p>Kategori Pelanggaran</p>
                <h2><?= $total_kategori; ?></h2>
                <a href="data_kategori_pelanggaran.php" class="btn btn-primary btn-sm">Lihat Data</a>
            </div>

        <?php } elseif ($role == 'walikelas') { ?>

            <div class="dashboard-card">
                <p>Siswa Kelas Wali</p>
                <h2><?= $total_siswa; ?></h2>
                <a href="data_siswa.php" class="btn btn-primary btn-sm">Lihat Siswa</a>
            </div>

            <div class="dashboard-card">
                <p>Rekap Pelanggaran</p>
                <h2><?= $total_pelanggaran; ?></h2>
                <a href="rekap_pelanggaran.php" class="btn btn-primary btn-sm">Lihat Rekap</a>
            </div>

            <div class="dashboard-card">
                <p>Total Poin</p>
                <h2><?= $total_poin; ?></h2>
                <a href="cetak_rekap.php" class="btn btn-primary btn-sm">Cetak Rekap</a>
            </div>

        <?php } else { ?>

            <div class="dashboard-card">
                <p>Catat Pelanggaran</p>
                <h2><?= $total_pelanggaran; ?></h2>
                <a href="catat_pelanggaran.php" class="btn btn-primary btn-sm">Lihat Data</a>
            </div>

            <div class="dashboard-card">
                <p>Rekap Poin</p>
                <h2><?= $total_poin; ?></h2>
                <a href="rekap_poin.php" class="btn btn-primary btn-sm">Lihat Data</a>
            </div>

        <?php } ?>

    </div>

</div>
</main>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>