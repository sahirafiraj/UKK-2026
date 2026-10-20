
<?php
session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';

$query_siswa = mysqli_query($koneksi, "SELECT * FROM t_siswa");
$total_siswa = mysqli_num_rows($query_siswa);

$query_guru = mysqli_query($koneksi, "SELECT * FROM t_guru");
$total_guru = mysqli_num_rows($query_guru);

$query_pelanggaran = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa");
$total_pelanggaran = mysqli_num_rows($query_pelanggaran);

$query_jenis = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
$total_jenis = mysqli_num_rows($query_jenis);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">Data Siswa</a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link text-white">Data Guru</a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">Data Kelas</a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">Tahun Ajaran</a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">Data Wali Kelas</a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">Kategori Pelanggaran</a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link text-white">Jenis Pelanggaran</a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php" class="nav-link text-white">Laporan</a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">Logout</a>
                </li>

            </ul>
        </div>

        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-2">Dashboard</h2>
                <p class="text-secondary mb-0">
                    Selamat datang, <b><?= htmlspecialchars($nama_login); ?></b>
                </p>
            </div>

            <div class="row g-4">

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-secondary mb-2">Total Siswa</p>
                            <h2 class="fw-bold text-dark mb-3"><?= $total_siswa; ?></h2>
                            <a href="data_siswa.php" class="btn btn-primary btn-sm">Lihat Data</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-secondary mb-2">Total Guru</p>
                            <h2 class="fw-bold text-dark mb-3"><?= $total_guru; ?></h2>
                            <a href="data_guru.php" class="btn btn-primary btn-sm">Lihat Data</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-secondary mb-2">Data Pelanggaran</p>
                            <h2 class="fw-bold text-dark mb-3"><?= $total_pelanggaran; ?></h2>
                            <a href="laporan.php" class="btn btn-primary btn-sm">Lihat Data</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-secondary mb-2">Kategori Pelanggaran</p>
                            <h2 class="fw-bold text-dark mb-3"><?= $total_jenis; ?></h2>
                            <a href="data_kategori_pelanggaran.php" class="btn btn-primary btn-sm">Lihat Data</a>
                        </div>
                    </div>
                </div>

            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>