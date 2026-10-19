
<?php
// Memulai session
session_start();

// Menghubungkan ke database
include "config/koneksi.php";

// Memeriksa login dan hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Mengambil nama pengguna yang sedang login
$halaman = basename($_SERVER['PHP_SELF']);
$nama_login = $_SESSION['name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR ADMIN -->
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
                    <a href="laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link active">
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

            <!-- HEADER HALAMAN -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Tentang Saya</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>

            </div>

            <!-- KARTU PROFIL -->
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body text-center p-4 p-md-5">

                    <!-- FOTO PROFIL -->
                    <div class="mb-4">
                        <img src="img/foto-saya.jpg"
                             alt="Foto Profil"
                             class="rounded-circle border border-4 border-primary shadow"
                             width="160"
                             height="160"
                             style="object-fit: cover;">
                    </div>

                    <!-- NAMA DAN JURUSAN -->
                    <h2 class="fw-bold mb-2">Sahira Firaj</h2>

                    <span class="badge rounded-pill bg-primary px-3 py-2">
                        Rekayasa Perangkat Lunak
                    </span>

                    <!-- DESKRIPSI DIRI -->
                    <p class="text-secondary mt-4 mx-auto"
                       style="max-width: 700px; line-height: 1.9;">
                        Halo, saya Sahira Firaj, seorang pelajar kelas XII RPL 2
                        di SMK Muhammadiyah Tasikmalaya. Saya memiliki ketertarikan
                        pada teknologi, pemrograman, pengembangan website, dan
                        desain antarmuka. Saya terus belajar untuk meningkatkan
                        kemampuan serta membuat aplikasi yang bermanfaat,
                        responsif, dan mudah digunakan.
                    </p>

                    <a href="dashboard.php" class="btn btn-primary mt-3">
                        Kembali ke Dashboard
                    </a>

                </div>
            </div>

            <!-- INFORMASI DIRI -->
            <div class="row g-4 mb-4">

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-3">
                                Informasi Diri
                            </h5>

                            <hr>

                            <p class="mb-2">
                                <b>Nama</b><br>
                                Sahira Firaj
                            </p>

                            <p class="mb-2">
                                <b>Kelas</b><br>
                                XII RPL 2
                            </p>

                            <p class="mb-2">
                                <b>Sekolah</b><br>
                                SMK Muhammadiyah Tasikmalaya
                            </p>

                            <p class="mb-0">
                                <b>Jurusan</b><br>
                                Rekayasa Perangkat Lunak
                            </p>

                        </div>
                    </div>
                </div>

                <!-- KEAHLIAN -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-3">
                                Keahlian
                            </h5>

                            <hr>

                            <p class="text-secondary">
                                Teknologi yang sedang saya pelajari
                                dalam pengembangan website.
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary">HTML</span>
                                <span class="badge bg-primary">CSS</span>
                                <span class="badge bg-primary">PHP</span>
                                <span class="badge bg-primary">MySQL</span>
                                <span class="badge bg-primary">Bootstrap</span>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- PROJECT -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-3">
                                Project Saya
                            </h5>

                            <hr>

                            <h6 class="fw-bold">
                                Sistem Informasi Pelanggaran Siswa
                            </h6>

                            <p class="text-secondary">
                                Aplikasi berbasis website untuk mengelola
                                data siswa, guru, kelas, kategori pelanggaran,
                                jenis pelanggaran, dan laporan pelanggaran siswa.
                            </p>

                            <span class="badge bg-info text-dark mt-2">
                                PHP Native & MySQL
                            </span>

                        </div>
                    </div>
                </div>

            </div>

            <!-- PENUTUP -->
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body text-center p-4">

                    <h5 class="fw-bold">
                        Terus Belajar dan Berkembang
                    </h5>

                    <p class="text-secondary mb-0">
                        Terus mengembangkan kemampuan, menambah pengalaman,
                        dan menciptakan karya yang bermanfaat.
                    </p>

                </div>
            </div>

            <!-- FOOTER -->
            <div class="text-center text-secondary small py-2">
                &copy; 2026 Sistem Informasi Pelanggaran Siswa
            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>