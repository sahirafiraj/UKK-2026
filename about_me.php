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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .skill-badge {
            background-color: #EEF2FF;
            color: #3155A6;
            border: 1px solid #DCE4FF;
            transition: all 0.2s ease;
        }

        .skill-badge:hover {
            background-color: #DCE6FF;
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <?php if ($role == 'admin') { ?>

                    <li class="nav-item">
                        <a href="dashboard.php"
                           class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                            Dashboard
                        </a>
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

                <?php } else { ?>

                    <li class="nav-item">
                        <a href="dashboard.php"
                           class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="catat_pelanggaran.php" class="nav-link text-white">Catat Pelanggaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="tindakan.php" class="nav-link text-white">Tindakan</a>
                    </li>

                    <li class="nav-item">
                        <a href="riwayat.php" class="nav-link text-white">Riwayat</a>
                    </li>

                    <li class="nav-item">
                        <a href="rekap_poin.php" class="nav-link text-white">Rekap Poin</a>
                    </li>

                <?php } ?>

                <li class="nav-item">
                    <a href="about_me.php"
                       class="nav-link <?= $halaman == 'about_me.php' ? 'active' : 'text-white'; ?>">
                        About Me
                    </a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">Logout</a>
                </li>

            </ul>
        </div>

        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-2">Tentang Saya</h2>
                <p class="text-secondary mb-0">
                    Selamat datang, <b><?= htmlspecialchars($nama_login); ?></b>
                </p>
            </div>

            <div class="card border-0 shadow-sm mb-4 text-center rounded-4">
                <div class="card-body p-4 p-md-5">

                    <img src="img/foto_saya.jpg"
                         alt="Foto Profil"
                         class="rounded-circle border border-3 border-light shadow-sm mb-3"
                         width="120"
                         height="120"
                         style="object-fit: cover;">

                    <h3 class="fw-bold mb-2">Sahira Firaj</h3>

                    <p class="text-secondary mb-3">
                        Siswa Rekayasa Perangkat Lunak
                    </p>

                    <span class="badge rounded-pill px-3 py-2 text-white mb-3"
                          style="background-color: #172554;">
                        SMK Muhammadiyah Kota Tasikmalaya
                    </span>

                    <p class="text-secondary mx-auto mb-0"
                       style="max-width: 650px; line-height: 1.8;">
                        Saya adalah siswa jurusan Rekayasa Perangkat Lunak
                        yang sedang mempelajari pembuatan website dan
                        pengelolaan database. Saya ingin terus menambah
                        pengetahuan dan pengalaman di bidang teknologi,
                        serta meningkatkan kemampuan melalui tugas dan
                        proyek sekolah.
                    </p>

                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4 rounded-4"
                 style="background-color: #DCE6F7;">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-4" style="color: #172554;">
                        Informasi Personal
                    </h4>

                    <div class="row g-4">

                        <div class="col-md-6">
                            <small style="color: #475569;">Nama Lengkap</small>
                            <h6 class="fw-bold mt-1 mb-0" style="color: #172554;">
                                Sahira Firaj
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small style="color: #475569;">Kelas</small>
                            <h6 class="fw-bold mt-1 mb-0" style="color: #172554;">
                                XII RPL 2
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small style="color: #475569;">Jurusan</small>
                            <h6 class="fw-bold mt-1 mb-0" style="color: #172554;">
                                Rekayasa Perangkat Lunak
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small style="color: #475569;">Tahun Ajaran</small>
                            <h6 class="fw-bold mt-1 mb-0" style="color: #172554;">
                                2026–2027
                            </h6>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card border rounded-4 shadow-sm mb-4"
                 style="background-color: #FFFFFF; border-color: #DCE4FF !important;">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3" style="color: #172554;">
                        Bidang yang Dipelajari
                    </h5>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="skill-badge px-3 py-2 rounded-3">HTML</span>
                        <span class="skill-badge px-3 py-2 rounded-3">CSS</span>
                        <span class="skill-badge px-3 py-2 rounded-3">PHP</span>
                        <span class="skill-badge px-3 py-2 rounded-3">Bootstrap</span>
                        <span class="skill-badge px-3 py-2 rounded-3">MySQL</span>
                        <span class="skill-badge px-3 py-2 rounded-3">Web Development</span>
                    </div>

                    <hr style="border-color: #E5E7EB;">

                    <p class="text-secondary mb-0 mt-3" style="line-height: 1.8;">
                        Berfokus pada pengembangan website dengan struktur yang rapi,
                        desain yang konsisten, dan pengalaman pengguna yang baik.
                    </p>

                </div>
            </div>

            <div class="text-center text-secondary py-3">
                <small>© 2026 Sistem Pelanggaran Siswa</small>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>