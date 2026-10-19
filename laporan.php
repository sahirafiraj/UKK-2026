
<?php
session_start();
include "config/koneksi.php";

// Cek login dan hak akses
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';

// Mengambil data laporan
$query = mysqli_query($koneksi, "
    SELECT *
    FROM t_pelanggaran_siswa
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
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
                    <a href="laporan.php" class="nav-link active">
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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Laporan Pelanggaran Siswa</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>

            </div>

            <!-- DAFTAR LAPORAN -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0 fw-bold">Daftar Laporan Pelanggaran</h5>

                    <div>
                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali ke Dashboard
                        </a>

                        <button onclick="window.print()" class="btn btn-primary">
                            Cetak Laporan
                        </button>
                    </div>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>ID Siswa</th>
                                    <th>ID Pelanggaran</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                if ($query && mysqli_num_rows($query) > 0) {

                                    while ($data = mysqli_fetch_assoc($query)) {
                                ?>

                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= htmlspecialchars($data['siswa_id'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['pelanggaran_id'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                    </td>
                                </tr>

                                <?php
                                    }

                                } else {
                                ?>

                                <tr>
                                    <td colspan="5" class="text-center">
                                        Belum ada data laporan pelanggaran.
                                    </td>
                                </tr>

                                <?php
                                }
                                ?>

                            </tbody>

                        </table>

                    </div>

                    <p class="mb-0">
                        Jumlah laporan:
                        <b><?= $query ? mysqli_num_rows($query) : 0; ?></b>
                    </p>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>