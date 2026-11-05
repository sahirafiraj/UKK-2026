
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Pengguna';
$role = strtolower(trim($_SESSION['role'] ?? ''));
$halaman = basename($_SERVER['PHP_SELF']);

if ($role != 'admin' && $role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

// DATA DASHBOARD
$query_siswa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_siswa");
$total_siswa = mysqli_fetch_assoc($query_siswa)['total'];

$query_guru = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_guru");
$total_guru = mysqli_fetch_assoc($query_guru)['total'];

$query_pelanggaran = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_pelanggaran_siswa");
$total_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)['total'];

$query_kategori = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_pelanggaran_kategori");
$total_kategori = mysqli_fetch_assoc($query_kategori)['total'];

// TOTAL POIN
$total_poin = 0;
$query_poin = mysqli_query($koneksi, "SELECT SUM(poin) AS total FROM t_pelanggaran_siswa");

if ($query_poin) {
    $data_poin = mysqli_fetch_assoc($query_poin);
    $total_poin = $data_poin['total'] ?? 0;
}

// JUMLAH TINDAKAN DAN RIWAYAT
$total_tindakan = 0;
$total_riwayat = $total_pelanggaran;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">

    <style>
        /* KONTEN UTAMA */
        .content-main {
            width: calc(100% - 260px);
            margin-left: 260px;
            min-height: 100vh;
            padding: 100px 35px 40px;
        }

        /* PEMBUNGKUS DASHBOARD */
        .dashboard-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* HEADER */
        .dashboard-header {
            background: #ffffff;
            border-radius: 14px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        .dashboard-header h2 {
            color: #172f52;
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .dashboard-header p {
            color: #64748b;
            margin: 0;
        }

        /* KARTU DASHBOARD */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
            width: 100%;
        }

        .dashboard-card {
            background: #ffffff;
            border: 1px solid #e8edf4;
            border-radius: 14px;
            padding: 25px;
            min-width: 0;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.04);
            transition: 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 7px 20px rgba(23, 47, 82, 0.10);
        }

        .dashboard-card p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .dashboard-card h2 {
            color: #172f52;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .dashboard-card .btn {
            background: #244b7a;
            border: none;
            border-radius: 7px;
            padding: 7px 15px;
            font-size: 13px;
        }

        .dashboard-card .btn:hover {
            background: #172f52;
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .dashboard-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .content-main {
                width: 100%;
                margin-left: 0;
                padding: 30px 18px;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .dashboard-header {
                padding: 20px;
            }
        }
    </style>
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <?php if ($role == 'admin') { ?>

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active">
                            Dashboard
                        </a>
                    </li>

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

                    <li class="nav-item">
                        <a href="laporan.php" class="nav-link text-white">
                            Laporan
                        </a>
                    </li>

                <?php } else { ?>

                    <li class="nav-item">
                        <a href="dashboard.php"
                           class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                            Dashboard
                        </a>
                    </li>

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

        <!-- KONTEN UTAMA -->
        <main class="content-main">

            <div class="dashboard-content">

                <!-- HEADER -->
                <div class="dashboard-header">
                    <h2>Dashboard</h2>

                    <p>
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>
                </div>

                <?php if ($role == 'admin') { ?>

                    <!-- KARTU ADMIN -->
                    <div class="dashboard-cards">

                        <div class="dashboard-card">
                            <p>Total Siswa</p>
                            <h2><?= $total_siswa; ?></h2>
                            <a href="data_siswa.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
                        </div>

                        <div class="dashboard-card">
                            <p>Total Guru</p>
                            <h2><?= $total_guru; ?></h2>
                            <a href="data_guru.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
                        </div>

                        <div class="dashboard-card">
                            <p>Data Pelanggaran</p>
                            <h2><?= $total_pelanggaran; ?></h2>
                            <a href="laporan.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
                        </div>

                        <div class="dashboard-card">
                            <p>Kategori Pelanggaran</p>
                            <h2><?= $total_kategori; ?></h2>
                            <a href="data_kategori_pelanggaran.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
                        </div>

                    </div>

                <?php } else { ?>

                    <!-- KARTU GURU -->
                    <div class="dashboard-cards">

                        <div class="dashboard-card">
                            <p>Catat Pelanggaran</p>
                            <h2><?= $total_pelanggaran; ?></h2>
                            <a href="catat_pelanggaran.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
                        </div>

                        <div class="dashboard-card">
                            <p>Tindakan</p>
                            <h2><?= $total_tindakan; ?></h2>
                            <a href="tindakan.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
                        </div>

                        <div class="dashboard-card">
                            <p>Riwayat</p>
                            <h2><?= $total_riwayat; ?></h2>
                            <a href="riwayat.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
                        </div>

                        <div class="dashboard-card">
                            <p>Rekap Poin</p>
                            <h2><?= $total_poin; ?></h2>
                            <a href="rekap_poin.php" class="btn btn-primary btn-sm text-white">
                                Lihat Data
                            </a>
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
