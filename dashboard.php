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

if (!in_array($role, ['admin', 'guru'])) {
    echo "Anda tidak memiliki akses";
    exit;
}

$name = $_SESSION['name'] ?? 'Admin';

$query_tahun = mysqli_query($koneksi, "
    SELECT id
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
");

$data_tahun = mysqli_fetch_assoc($query_tahun);
$id_tahun = $data_tahun['id'] ?? 0;

$id_guru = 0;
$status_wali_kelas = false;
$kelas_wali = [];

if ($role == 'guru') {

    $query_guru = mysqli_query($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = '$id_user'
        LIMIT 1
    ");

    $data_guru = mysqli_fetch_assoc($query_guru);
    $id_guru = $data_guru['id'] ?? 0;

    if ($id_guru > 0 && $id_tahun > 0) {

        $query_wali = mysqli_query($koneksi, "
            SELECT DISTINCT k.id, k.nama AS nama_kelas
            FROM t_wali_kelas wk
            JOIN t_kelas k ON k.id = wk.kelas_id
            WHERE wk.guru_id = '$id_guru'
            AND wk.tahun_ajaran_id = '$id_tahun'
            AND wk.status_aktif = 1
        ");

        while ($data_wali = mysqli_fetch_assoc($query_wali)) {
            $kelas_wali[] = $data_wali;
        }

        if (count($kelas_wali) > 0) {
            $status_wali_kelas = true;
        }
    }
}
/* DASHBOARD ADMIN */

$query_siswa = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_siswa
    WHERE status_aktif = 1
");

$total_siswa = mysqli_fetch_assoc($query_siswa)['total'] ?? 0;

$query_guru_total = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_guru
");

$total_guru = mysqli_fetch_assoc($query_guru_total)['total'] ?? 0;

$query_pelanggaran = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_pelanggaran_siswa
");

$total_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)['total'] ?? 0;



$query_kategori = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_pelanggaran_kategori
");

$total_kategori = mysqli_fetch_assoc($query_kategori)['total'] ?? 0;

/* DASHBOARD GURU DAN WALI KELAS */

$filter_pelanggaran = "";

if ($role == 'guru' && $id_guru > 0) {

    if ($status_wali_kelas) {
        $filter_pelanggaran = "
            WHERE kelas_id IN (
                SELECT kelas_id
                FROM t_wali_kelas
                WHERE guru_id = '$id_guru'
                AND tahun_ajaran_id = '$id_tahun'
                AND status_aktif = 1
            )
        ";
    } else {
        $filter_pelanggaran = "
            WHERE guru_id = '$id_guru'
        ";
    }

} else {
    $filter_pelanggaran = "WHERE 1 = 0";
}

$query_total_guru = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_pelanggaran_siswa
    $filter_pelanggaran
");

$total_pelanggaran_guru = mysqli_fetch_assoc($query_total_guru)['total'] ?? 0;

$query_belum = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_pelanggaran_siswa
    $filter_pelanggaran
    AND status = 'Belum'
");

$total_belum = mysqli_fetch_assoc($query_belum)['total'] ?? 0;

$query_selesai = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_pelanggaran_siswa
    $filter_pelanggaran
    AND status <> 'Belum'
");

$total_selesai = mysqli_fetch_assoc($query_selesai)['total'] ?? 0;

$query_poin = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(poin), 0) AS total
    FROM t_pelanggaran_siswa
    $filter_pelanggaran
");

$total_poin = mysqli_fetch_assoc($query_poin)['total'] ?? 0;

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

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- SIDEBAR -->
            <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

                <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

                <ul class="nav nav-pills flex-column gap-1">

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active">
                            Dashboard
                        </a>
                    </li>

                    <?php if ($role == 'admin') { ?>

                        <li class="nav-item">
                            <a href="data_siswa.php" class="nav-link text-white">
                                Data Siswa
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="data_guru.php" class="nav-link text-white">
                                Data Guru
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="data_kelas.php" class="nav-link text-white">
                                Data Kelas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="data_tahun_ajaran.php" class="nav-link text-white">
                                Tahun Ajaran
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="data_penempatan_siswa.php" class="nav-link text-white">
                                Penempatan Siswa
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="data_wali_kelas.php" class="nav-link text-white">
                                Data Wali Kelas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                                Kategori Pelanggaran
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="data_jenis_pelanggaran.php" class="nav-link text-white">
                                Jenis Pelanggaran
                            </a>
                        </li>

                    <?php } else { ?>

                        <li class="nav-item">
                            <a href="catat_pelanggaran.php" class="nav-link text-white">
                                Catat Pelanggaran
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="tindakan.php" class="nav-link text-white">
                                Tindakan
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="riwayat.php" class="nav-link text-white">
                                Riwayat
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="rekap_poin.php" class="nav-link text-white">
                                Rekap Poin
                            </a>
                        </li>

                    <?php } ?>

                    <li class="nav-item">
                        <a href="laporan.php" class="nav-link text-white">
                            Laporan
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="about_me.php" class="nav-link text-white">
                            About Me
                        </a>
                    </li>

                    <hr class="border-white">

                    <li class="nav-item">
                        <a href="logout.php" class="nav-link text-white">
                            Logout
                        </a>
                    </li>

                </ul>
            </div>

            <!-- KONTEN DASHBOARD -->
            <main class="content-main">
            <div class="page-content">

                <!-- HEADER -->
                <div class="bg-white rounded shadow-sm p-4 mb-4">

                    <h2 class="fw-bold">
                        Dashboard
                    </h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>

                </div>

                    <!-- CARD ADMIN -->
                    <?php if ($role == 'admin') { ?>

                        <div class="dashboard-cards">

                            <div class="dashboard-card">
                                <p>Total Siswa</p>
                                <h2><?= $total_siswa; ?></h2>
                                <a href="data_siswa.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                            <div class="dashboard-card">
                                <p>Total Guru</p>
                                <h2><?= $total_guru; ?></h2>
                                <a href="data_guru.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                            <div class="dashboard-card">
                                <p>Data Jenis Pelanggaran</p>
                                <h2><?= $total_pelanggaran; ?></h2>
                                <a href="data_jenis_pelanggaran.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                            <div class="dashboard-card">
                                <p>Kategori Pelanggaran</p>
                                <h2><?= $total_kategori; ?></h2>
                                <a href="data_kategori_pelanggaran.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                        </div>

                    <?php } else { ?>

                        <!-- CARD GURU DAN WALI KELAS -->
                        <div class="dashboard-cards">

                            <div class="dashboard-card">
                                <p>Total Pelanggaran</p>
                                <h2><?= $total_pelanggaran_guru; ?></h2>
                                <a href="riwayat.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                            <div class="dashboard-card">
                                <p>Belum Ditindaklanjuti</p>
                                <h2><?= $total_belum; ?></h2>
                                <a href="tindakan.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                            <div class="dashboard-card">
                                <p>Selesai Ditindaklanjuti</p>
                                <h2><?= $total_selesai; ?></h2>
                                <a href="tindakan.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                            <div class="dashboard-card">
                                <p>Total Poin Pelanggaran</p>
                                <h2><?= $total_poin; ?></h2>
                                <a href="rekap_poin.php" class="btn btn-primary">Lihat Data</a>
                            </div>

                        </div>

                    <?php } ?>

                </div>

            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>