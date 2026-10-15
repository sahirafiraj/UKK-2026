
<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$name = $_SESSION['name'];
$halaman = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR ADMIN TETAP -->
         <?php if ($role == 'admin') { ?>
             <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link active">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php"
                       class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php"
                       class="nav-link text-white">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php"
                       class="nav-link text-white">
                        Data Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php"
                       class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php"
                       class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php"
                       class="nav-link text-white">
                        Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php"
                       class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php"
                       class="nav-link text-white">
                        Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php"
                       class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php"
                       class="nav-link text-white">
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

        <!-- SIDEBAR GURU -->
        <?php } elseif ($role == 'guru') { ?>
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link active">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php"
                       class="nav-link text-white">
                        Catatan Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php"
                       class="nav-link text-white">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php"
                       class="nav-link text-white">
                        Riwayat
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php"
                       class="nav-link text-white">
                        Rekap Poin
                    </a>
                </li>


                <li class="nav-item">
                    <a href="data_guru.php"
                       class="nav-link text-white">
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

        <?php } else { ?>

        <div class="col-12 p-4">
            <div class="alert alert-danger">
                Role pengguna tidak dikenali.
            </div>
        </div>

        <?php exit; } ?>

        <!-- KONTEN DASHBOARD -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">
                    Dashboard <?= ucfirst($role); ?>
                </h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($name); ?></b>
                </p>
            </div>

            <!-- CARD ADMIN -->
            <?php if ($role == 'admin') { ?>

            <?php
            $menu_admin = [
                ['Data Siswa', 'Kelola data siswa.', 'data_siswa.php'],
                ['Data Guru', 'Kelola data guru.', 'data_guru.php'],
                ['Data Kelas', 'Kelola data kelas.', 'data_kelas.php'],
                ['Data Tahun Ajaran', 'Kelola data tahun ajaran.', 'data_tahun_ajaran.php'],
                ['Penempatan Siswa', 'Kelola penempatan siswa.', 'data_penempatan_siswa.php'],
                ['Data Wali Kelas', 'Kelola data wali kelas.', 'data_wali_kelas.php'],
                ['Kategori Pelanggaran', 'Kelola kategori pelanggaran.', 'data_kategori_pelanggaran.php'],
                ['Jenis Pelanggaran', 'Kelola jenis pelanggaran.', 'data_jenis_pelanggaran.php'],
                ['Laporan', 'Lihat laporan sistem.', 'laporan.php']
            ];
            ?>

            <div class="row">

                <?php foreach ($menu_admin as $menu) { ?>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">

                            <h5 class="card-title fw-bold">
                                <?= htmlspecialchars($menu[0]); ?>
                            </h5>

                            <p class="card-text text-secondary">
                                <?= htmlspecialchars($menu[1]); ?>
                            </p>

                            <a href="<?= htmlspecialchars($menu[2]); ?>"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>
                    </div>
                </div>

                <?php } ?>

            </div>

            <!-- CARD GURU -->
            <?php } elseif ($role == 'guru') { ?>

            <?php
            $menu_guru = [
                ['Catatan Pelanggaran', 'Mencatat data pelanggaran siswa.', 'data_pelanggaran.php'],
                ['Tindakan', 'Mengelola tindakan pelanggaran.', 'data_tindakan.php'],
                ['Riwayat', 'Melihat riwayat pelanggaran siswa.', 'data_riwayat.php'],
                ['Rekap Poin', 'Melihat rekap poin pelanggaran siswa.', 'data_rekap_poin.php']
            ];
            ?>

            <div class="row">

                <?php foreach ($menu_guru as $menu) { ?>

                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">

                            <h5 class="card-title fw-bold">
                                <?= htmlspecialchars($menu[0]); ?>
                            </h5>

                            <p class="card-text text-secondary">
                                <?= htmlspecialchars($menu[1]); ?>
                            </p>

                            <a href="<?= htmlspecialchars($menu[2]); ?>"
                               class="btn btn-primary">
                                Buka
                            </a>

                        </div>
                    </div>
                </div>

                <?php } ?>

            </div>

            <?php } ?>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>

</body>
</html>
