
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Pengguna';
$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id'] ?? 0);
$halaman = basename($_SERVER['PHP_SELF']);

// CEK HAK AKSES
if ($role != 'admin' && $role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

// CEK STATUS WALI KELAS
$status_wali_kelas = false;

if ($role == 'guru') {

    $query_guru = mysqli_query($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = '$id_user'
        AND status_aktif = 1
        LIMIT 1
    ");

    if ($query_guru && mysqli_num_rows($query_guru) > 0) {

        $data_guru = mysqli_fetch_assoc($query_guru);
        $guru_id = $data_guru['id'];

        // CEK TAHUN AJARAN AKTIF
        $query_tahun_ajaran = mysqli_query($koneksi, "
            SELECT id
            FROM t_tahun_ajaran
            WHERE status_aktif = 1
            LIMIT 1
        ");

        if ($query_tahun_ajaran && mysqli_num_rows($query_tahun_ajaran) > 0) {

            $data_tahun_ajaran = mysqli_fetch_assoc($query_tahun_ajaran);
            $tahun_ajaran_id = $data_tahun_ajaran['id'];

            // CEK APAKAH GURU MERUPAKAN WALI KELAS
            $query_wali_kelas = mysqli_query($koneksi, "
                SELECT id
                FROM t_wali_kelas
                WHERE guru_id = '$guru_id'
                AND tahun_ajaran_id = '$tahun_ajaran_id'
                AND status_aktif = 1
                LIMIT 1
            ");

            if ($query_wali_kelas && mysqli_num_rows($query_wali_kelas) > 0) {
                $status_wali_kelas = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me - Sistem Pelanggaran</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS UTAMA -->
    <link rel="stylesheet" href="style/style.css">

    <style>
        .content-main {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .page-content {
            margin-top: 0 !important;
            padding-top: 24px !important;
        }

        @media (max-width: 767.98px) {
            .page-content {
                padding-top: 20px !important;
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

                    <!-- MENU ADMIN -->
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

                    <!-- LAPORAN ADMIN -->
                    <li class="nav-item">
                        <a href="laporan.php"
                           class="nav-link <?= $halaman == 'laporan.php' ? 'active' : 'text-white'; ?>">
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

                    <!-- LAPORAN KHUSUS WALI KELAS -->
                    <?php if ($status_wali_kelas) { ?>
                    <li class="nav-item">
                        <a href="laporan.php"
                           class="nav-link <?= $halaman == 'laporan.php' ? 'active' : 'text-white'; ?>">
                            Laporan
                        </a>
                    </li>
                    <?php } ?>

                <?php } ?>

                <!-- ABOUT ME -->
                <li class="nav-item">
                    <a href="about_me.php"
                       class="nav-link <?= $halaman == 'about_me.php' ? 'active' : 'text-white'; ?>">
                        About Me
                    </a>
                </li>

                <hr class="border-white">

                <!-- LOGOUT -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">Logout</a>
                </li>

            </ul>
        </div>

        <!-- KONTEN -->
        <main class="content-main">

            <div class="page-content">

                <!-- HEADER -->
                <div class="bg-white rounded shadow-sm p-4 mb-4">

                    <h2 class="fw-bold mb-2">Tentang Saya</h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>

                </div>

                <!-- PROFIL -->
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

                <!-- INFORMASI PERSONAL -->
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

                <!-- BIDANG YANG DIPELAJARI -->
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

                <!-- FOOTER -->
                <div class="text-center text-secondary py-3">
                    <small>© 2026 Sistem Pelanggaran Siswa</small>
                </div>

            </div>

        </main>

    </div>
</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
