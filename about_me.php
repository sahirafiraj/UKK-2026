<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$name = $_SESSION['name'];
$halaman = basename($_SERVER['PHP_SELF']);

$nama = "Sahira Firaj";
$jurusan = "Rekayasa Perangkat Lunak";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$email = "emailkamu@example.com";

$deskripsi = "Saya adalah siswa Rekayasa Perangkat Lunak yang
sedang mempelajari pengembangan aplikasi web. Saya tertarik
dengan pemrograman, desain antarmuka, dan pengelolaan database.";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f6fc;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #172554;
        }

        /* SIDEBAR */
        .sidebar {
            background: linear-gradient(180deg, #172554, #102047);
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 25px 18px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar h4 {
            color: white;
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 32px;
            padding-left: 8px;
        }

        .sidebar .nav-link {
            color: #e2e8f0;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 5px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .sidebar .nav-link.active {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }

        .sidebar hr {
            border-color: rgba(255, 255, 255, 0.35);
            margin: 20px 8px;
        }

        /* AREA KONTEN */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            height: 72px;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e5eaf3;
        }

        .user-name {
            font-weight: 600;
            color: #172554;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 750;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: #64748b;
            margin-bottom: 25px;
        }

        /* KARTU PROFIL */
        .profile-card {
            background: white;
            border: 1px solid #e5eaf3;
            border-radius: 22px;
            box-shadow: 0 12px 35px rgba(23, 37, 84, 0.06);
            padding: 42px 35px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: "";
            position: absolute;
            width: 230px;
            height: 230px;
            background: #eaf2ff;
            border-radius: 50%;
            top: -150px;
            left: -100px;
        }

        .profile-card::after {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            background: #edf4ff;
            border-radius: 50%;
            bottom: -145px;
            right: -90px;
        }

        .profile-content {
            position: relative;
            z-index: 1;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            object-position: center 30%;
            border-radius: 50%;
            padding: 5px;
            border: 4px solid #3b82f6;
            background: white;
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.15);
            margin-bottom: 20px;
        }

        .profile-name {
            font-size: 32px;
            font-weight: 750;
            margin-bottom: 10px;
            color: #172554;
        }

        .role-badge {
            display: inline-block;
            background: #eaf2ff;
            color: #2563eb;
            border-radius: 9px;
            padding: 9px 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .school-name {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 22px;
        }

        .accent-line {
            width: 42px;
            height: 3px;
            background: #2563eb;
            border-radius: 5px;
            margin: 0 auto 22px;
        }

        .profile-description {
            max-width: 650px;
            margin: 0 auto 30px;
            color: #64748b;
            line-height: 1.8;
            font-size: 15px;
        }

        /* INFORMASI PROFIL */
        .info-box {
            height: 100%;
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: left;
            padding: 18px;
            background: #f5f8ff;
            border: 1px solid #dbeafe;
            border-radius: 13px;
        }

        .info-letter {
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            background: #dbeafe;
            color: #2563eb;
            font-size: 17px;
            font-weight: 700;
        }

        .info-label {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .info-value {
            color: #172554;
            font-size: 13px;
            font-weight: 650;
            overflow-wrap: anywhere;
        }

        /* MEDIA SOSIAL */
        .social-title {
            font-size: 17px;
            font-weight: 700;
            margin: 30px 0 15px;
        }

        .social-link {
            display: inline-block;
            min-width: 115px;
            text-decoration: none;
            color: #2563eb;
            border: 1px solid #93b4ff;
            border-radius: 10px;
            padding: 10px 18px;
            margin: 4px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }

        .social-link:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        /* TOMBOL */
        .button-primary,
        .button-outline {
            display: inline-block;
            text-decoration: none;
            border-radius: 11px;
            padding: 13px 27px;
            margin: 7px 5px 0;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .button-primary {
            background: #2563eb;
            color: white;
            border: 1px solid #2563eb;
        }

        .button-primary:hover {
            background: #1d4ed8;
            color: white;
            transform: translateY(-2px);
        }

        .button-outline {
            background: white;
            color: #2563eb;
            border: 1px solid #93b4ff;
        }

        .button-outline:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        /* RESPONSIVE */
        @media (max-width: 991px) {
            .sidebar {
                width: 220px;
            }

            .main-content {
                margin-left: 220px;
            }

            .content {
                padding: 22px;
            }

            .profile-card {
                padding: 35px 22px;
            }
        }

        @media (max-width: 767px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                max-height: none;
            }

            .sidebar h4 {
                margin-bottom: 18px;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                height: 62px;
                padding: 0 18px;
            }

            .content {
                padding: 18px 12px;
            }

            .page-title {
                font-size: 24px;
            }

            .profile-card {
                padding: 30px 16px;
                border-radius: 16px;
            }

            .profile-photo {
                width: 125px;
                height: 125px;
            }

            .profile-name {
                font-size: 27px;
            }

            .profile-description {
                font-size: 14px;
            }

            .info-box {
                padding: 14px;
            }

            .social-link {
                min-width: 100px;
            }

            .button-primary,
            .button-outline {
                width: 100%;
                margin: 6px 0;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid p-0">
    <div class="row g-0">

        <!-- SIDEBAR ADMIN -->
        <div class="sidebar">
            <h4>Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php"
                       class="nav-link <?= $halaman == 'data_siswa.php' ? 'active' : ''; ?>">
                        Kelola Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php"
                       class="nav-link <?= $halaman == 'data_guru.php' ? 'active' : ''; ?>">
                        Kelola Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php"
                       class="nav-link <?= $halaman == 'data_kelas.php' ? 'active' : ''; ?>">
                        Kelola Data Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php"
                       class="nav-link <?= $halaman == 'data_tahun_ajaran.php' ? 'active' : ''; ?>">
                        Kelola Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php"
                       class="nav-link <?= $halaman == 'data_penempatan_siswa.php' ? 'active' : ''; ?>">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php"
                       class="nav-link <?= $halaman == 'data_wali_kelas.php' ? 'active' : ''; ?>">
                        Kelola Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php"
                       class="nav-link <?= $halaman == 'data_kategori_pelanggaran.php' ? 'active' : ''; ?>">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php"
                       class="nav-link <?= $halaman == 'data_jenis_pelanggaran.php' ? 'active' : ''; ?>">
                        Kelola Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php"
                       class="nav-link <?= $halaman == 'laporan.php' ? 'active' : ''; ?>">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php"
                       class="nav-link <?= $halaman == 'about_me.php' ? 'active' : ''; ?>">
                        About Me
                    </a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>

            </ul>
        </div>

        <!-- KONTEN UTAMA -->
        <main class="main-content">

            <div class="topbar">
                <span class="fw-semibold">About Me</span>
                <span class="user-name">
                    <?= htmlspecialchars($name); ?>
                </span>
            </div>

            <div class="content">

                <h1 class="page-title">About Me</h1>
                <p class="page-subtitle">
                    Kenalan lebih dekat dengan saya
                </p>

                <div class="profile-card">
                    <div class="profile-content">

                        <!-- FOTO PROFIL -->
                        <img
                            src="foto_profil.jpg"
                            alt="Foto Profil <?= htmlspecialchars($nama); ?>"
                            class="profile-photo"
                        >

                        <h2 class="profile-name">
                            <?= htmlspecialchars($nama); ?>
                        </h2>

                        <div class="role-badge">
                            <?= htmlspecialchars($jurusan); ?> (RPL)
                        </div>

                        <p class="school-name">
                            <?= htmlspecialchars($sekolah); ?>
                        </p>

                        <div class="accent-line"></div>

                        <p class="profile-description">
                            <?= nl2br(htmlspecialchars($deskripsi)); ?>
                        </p>

                        <!-- INFORMASI PROFIL -->
                        <div class="row g-3 text-start mx-auto"
                             style="max-width: 760px;">

                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="info-letter">R</div>
                                    <div>
                                        <div class="info-label">Jurusan</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($jurusan); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="info-letter">S</div>
                                    <div>
                                        <div class="info-label">Sekolah</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($sekolah); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="info-box">
                                    <div class="info-letter">@</div>
                                    <div>
                                        <div class="info-label">Email</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($email); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- MEDIA SOSIAL -->
                        <h3 class="social-title">Media Sosial</h3>

                        <div class="mb-4">
                            <a href="https://instagram.com/"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-link">
                                Instagram
                            </a>

                            <a href="https://github.com/"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-link">
                                GitHub
                            </a>

                            <a href="https://facebook.com/"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-link">
                                Facebook
                            </a>
                        </div>

                        <!-- TOMBOL AKSI -->
                        <div>
                            <a href="mailto:<?= htmlspecialchars($email); ?>"
                               class="button-primary">
                                Hubungi Saya
                            </a>

                            <a href="dashboard.php"
                               class="button-outline">
                                Kembali ke Dashboard
                            </a>
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