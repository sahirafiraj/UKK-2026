<?php 
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$name = $_SESSION['name'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid">
    <div class="row">

        <!-- = SIDEBAR ADMIN = -->
        <?php if ($role == 'admin') { ?>

        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">
                Sistem Pelanggaran
            </h4>

            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="dashboard.php"
                       class="nav-link active">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_siswa.php"
                       class="nav-link text-white">
                        Kelola Data Siswa
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_guru.php"
                       class="nav-link text-white">
                        Kelola Data Guru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_kelas.php"
                       class="nav-link text-white">
                        Kelola Data Kelas
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_tahun_ajaran.php"
                       class="nav-link text-white">
                        Kelola Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_penempatan_siswa.php"
                       class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_wali_kelas.php"
                       class="nav-link text-white">
                        Kelola Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_kategori_pelanggaran.php"
                       class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_jenis_pelanggaran.php"
                       class="nav-link text-white">
                        Kelola Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="laporan.php"
                       class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                
<li class="nav-item mb-2">
    <a href="about_me.php"
       class="nav-link text-white">
        About Me
    </a>
</li>

               <hr class="text-secondary">

<li class="nav-item">
    <a href="logout.php"
       class="nav-link text-danger">
        Logout
    </a>
</li>

            </ul>

        </div>


        <!-- ================= SIDEBAR GURU ================= -->
        <?php } elseif ($role == 'guru') { ?>

        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">
                Sistem Pelanggaran
            </h4>

            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="dashboard.php"
                       class="nav-link active">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_pelanggaran.php"
                       class="nav-link text-white">
                        Catatan Pelanggaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_tindakan.php"
                       class="nav-link text-white">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_riwayat.php"
                       class="nav-link text-white">
                        Riwayat
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="data_rekap_poin.php"
                       class="nav-link text-white">
                        Rekap Poin
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="about_me.php"
                       class="nav-link text-white">
                        About Me
                    </a>
                </li>

               <hr class="text-secondary">

<li class="nav-item">
    <a href="logout.php"
       class="nav-link text-danger">
        Logout
    </a>
</li>

            </ul>

        </div>

        <?php } ?>


        <!-- ================= KONTEN ================= -->

        <main class="col-md-9 col-lg-10 p-4">

            <h2>
                Dashboard <?= ucfirst($role); ?>
            </h2>

            <p>
                Selamat datang,
                <b><?= $name; ?></b>
            </p>


            <!-- ================= CARD ADMIN ================= -->

            <?php if ($role == 'admin') { ?>

            <div class="row">

                <!-- SISWA -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Siswa
                            </h5>

                            <p class="card-text">
                                Kelola data siswa.
                            </p>

                            <a href="data_siswa.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>


                <!-- GURU -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Guru
                            </h5>

                            <p class="card-text">
                                Kelola data guru.
                            </p>

                            <a href="data_guru.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>


                <!-- KELAS -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Kelas
                            </h5>

                            <p class="card-text">
                                Kelola data kelas.
                            </p>

                            <a href="data_kelas.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>

                
                <!-- TAHUN AJARAN -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Tahun Ajaran
                            </h5>

                            <p class="card-text">
                                Kelola data tahun ajaran.
                            </p>

                            <a href="data_tahun_ajaran.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>

                <!-- PENEMPATAN SISWA -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Penempatan Siswa
                            </h5>

                            <p class="card-text">
                                Kelola data penempatan siswa.
                            </p>

                            <a href="data_penempatan_siswa.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>

                <!-- WALI KELAS -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Wali Kelas
                            </h5>

                            <p class="card-text">
                                Kelola data wali kelas.
                            </p>

                            <a href="data_wali_kelas.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>

                <!-- KATEGORI PELANGGARAN -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Kategori Pelanggaran
                            </h5>

                            <p class="card-text">
                                Kelola data kategori pelanggaran.
                            </p>

                            <a href="data_kategori_pelanggaran.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>

                <!-- JENIS PELANGGARAN -->
                <div class="col-md-4 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Jenis Pelanggaran
                            </h5>

                            <p class="card-text">
                                Kelola data jenis pelanggaran.
                            </p>

                            <a href="data_jenis_pelanggaran.php"
                               class="btn btn-primary">
                                Lihat Data
                            </a>

                        </div>

                    </div>

                </div>

                
            </div>

            <!-- ================= CARD GURU ================= -->

            <?php } elseif ($role == 'guru') { ?>

            <div class="row">

                <!-- CATATAN PELANGGARAN -->
                <div class="col-md-6 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Catatan Pelanggaran
                            </h5>

                            <p class="card-text">
                                Mencatat data pelanggaran siswa.
                            </p>

                            <a href="data_pelanggaran.php"
                               class="btn btn-primary">
                                Buka
                            </a>

                        </div>

                    </div>

                </div>


                <!-- TINDAKAN -->
                <div class="col-md-6 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Tindakan
                            </h5>

                            <p class="card-text">
                                Mengelola tindakan terhadap pelanggaran.
                            </p>

                            <a href="data_tindakan.php"
                               class="btn btn-primary">
                                Buka
                            </a>

                        </div>

                    </div>

                </div>


                <!-- RIWAYAT -->
                <div class="col-md-6 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Riwayat
                            </h5>

                            <p class="card-text">
                                Melihat riwayat pelanggaran siswa.
                            </p>

                            <a href="data_riwayat.php"
                               class="btn btn-primary">
                                Buka
                            </a>

                        </div>

                    </div>

                </div>


                <!-- REKAP POIN -->
                <div class="col-md-6 mb-3">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Rekap Poin
                            </h5>

                            <p class="card-text">
                                Melihat rekap poin pelanggaran siswa.
                            </p>

                            <a href="data_rekap_poin.php"
                               class="btn btn-primary">
                                Buka
                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <?php } ?>

        </main>

    </div>

</div>
 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>

</body>
</html>
