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

// Data dashboard
$query_siswa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_siswa");
$total_siswa = mysqli_fetch_assoc($query_siswa)['total'];

$query_guru = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_guru");
$total_guru = mysqli_fetch_assoc($query_guru)['total'];

$query_pelanggaran = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_pelanggaran_siswa");
$total_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)['total'];

$query_kategori = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_pelanggaran_kategori");
$total_kategori = mysqli_fetch_assoc($query_kategori)['total'];

// Total poin, jika kolom poin tersedia pada tabel tersebut
$total_poin = 0;
$query_poin = mysqli_query($koneksi, "SELECT SUM(poin) AS total FROM t_pelanggaran_siswa");

if ($query_poin) {
    $data_poin = mysqli_fetch_assoc($query_poin);
    $total_poin = $data_poin['total'] ?? 0;
}

// Contoh sementara jumlah tindakan
$total_tindakan = 0;

// Riwayat menggunakan jumlah seluruh catatan pelanggaran
$total_riwayat = $total_pelanggaran;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #192952;">

            <h4 class="text-white mb-4">
                Sistem<br>Pelanggaran
            </h4>

            <ul class="nav nav-pills flex-column gap-1">

                <!-- MENU ADMIN -->
                <?php if ($role == 'admin') { ?>

                    <li class="nav-item">
                        <a href="dashboard.php"
                           class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
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

                    <!-- MENU GURU -->
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

                <!-- MENU BERSAMA -->
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
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">Dashboard</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>
            </div>

            <?php if ($role == 'admin') { ?>

                <!-- KARTU DASHBOARD ADMIN -->
                <div class="row g-4">

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Total Siswa</p>
                                <h2 class="fw-bold mb-3"><?= $total_siswa; ?></h2>
                                <a href="data_siswa.php" class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Total Guru</p>
                                <h2 class="fw-bold mb-3"><?= $total_guru; ?></h2>
                                <a href="data_guru.php" class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Data Pelanggaran</p>
                                <h2 class="fw-bold mb-3"><?= $total_pelanggaran; ?></h2>
                                <a href="laporan.php" class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Kategori Pelanggaran</p>
                                <h2 class="fw-bold mb-3"><?= $total_kategori; ?></h2>
                                <a href="data_kategori_pelanggaran.php"
                                   class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            <?php } else { ?>

                <!-- KARTU DASHBOARD GURU -->
                <div class="row g-4">

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Catat Pelanggaran</p>
                                <h2 class="fw-bold mb-3"><?= $total_pelanggaran; ?></h2>
                                <a href="catat_pelanggaran.php"
                                   class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Tindakan</p>
                                <h2 class="fw-bold mb-3"><?= $total_tindakan; ?></h2>
                                <a href="tindakan.php" class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Riwayat</p>
                                <h2 class="fw-bold mb-3"><?= $total_riwayat; ?></h2>
                                <a href="riwayat.php" class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-secondary mb-2">Rekap Poin</p>
                                <h2 class="fw-bold mb-3"><?= $total_poin; ?></h2>
                                <a href="rekap_poin.php" class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            <?php } ?>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>