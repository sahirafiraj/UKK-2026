<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">
                Sistem Pelanggaran Siswa
            </h4>

            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <?php if ($role == 'admin') { ?>

                    <li class="nav-item mb-2">
                        <a href="kelas.php" class="nav-link text-white">
                            Kelola Kelas
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="siswa.php" class="nav-link text-white">
                            Kelola Siswa
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="guru.php" class="nav-link text-white">
                            Kelola Guru
                        </a>
                    </li>

                <?php } elseif ($role == 'guru') { ?>

                    <li class="nav-item mb-2">
                        <a href="catat_pelanggaran.php" class="nav-link text-white">
                            Catat Pelanggaran
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="riwayat.php" class="nav-link text-white">
                            Riwayat
                        </a>
                    </li>

                <?php } ?>

                <li class="nav-item mb-2">
                    <a href="about.php" class="nav-link active">
                        About Me
                    </a>
                </li>

                <hr class="text-secondary">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>

            </ul>

        </div>


        <!-- KONTEN ABOUT ME -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">About Me</h2>

            <div class="card shadow-sm">

                <div class="card-body">

                    <h3 class="mb-3">
                        Sistem Informasi Pelanggaran Siswa
                    </h3>

                    <p>
                        Halaman ini berisi informasi mengenai aplikasi
                        Sistem Informasi Pelanggaran Siswa.
                    </p>

                    <hr>

                    <h5>Informasi Pengguna</h5>

                    <table class="table table-borderless">

                        <tr>
                            <td width="150">Nama</td>
                            <td>: <?= htmlspecialchars($name); ?></td>
                        </tr>

                        <tr>
                            <td>Role</td>
                            <td>: <?= htmlspecialchars($role); ?></td>
                        </tr>

                    </table>

                    <hr>

                    <h5>Tentang Aplikasi</h5>

                    <p>
                        Sistem Informasi Pelanggaran Siswa merupakan aplikasi
                        berbasis web yang digunakan untuk membantu sekolah
                        dalam mengelola data siswa, guru, kelas, serta data
                        pelanggaran siswa secara lebih terorganisir.
                    </p>

                    <p>
                        Aplikasi ini dibuat sebagai bagian dari pembelajaran
                        Rekayasa Perangkat Lunak.
                    </p>

                </div>

            </div>

        </main>

    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>


</body>
</html>