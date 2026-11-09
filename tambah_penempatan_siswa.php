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

$name = $_SESSION['name'] ?? 'Admin';
$halaman = basename($_SERVER['PHP_SELF']);

$siswa = mysqli_query($koneksi, "
    SELECT id, nis, nama
    FROM t_siswa
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

$tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY id DESC
");

$kelas = mysqli_query($koneksi, "
    SELECT id, nama, tingkat, jurusan
    FROM t_kelas
    WHERE status_aktif = 1
    AND nama IN ('XII TKR 4', 'X TKR 4', 'XII RPL 2', 'XII RPL 1')
    ORDER BY nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
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
                    <a href="data_penempatan_siswa.php"
                       class="nav-link <?= ($halaman == 'data_penempatan_siswa.php' || $halaman == 'tambah_penempatan_siswa.php') ? 'active' : 'text-white'; ?>">
                        Penempatan Siswa
                    </a>
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

        <main class="content-main">
            <div class="page-content">

                <div class="bg-white rounded shadow-sm p-4 mb-4">

                    <h2 class="fw-bold">Tambah Penempatan Siswa</h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($name); ?></b>
                    </p>

                </div>

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white p-3">
                        <h5 class="mb-0 fw-bold">Form Tambah Penempatan Siswa</h5>
                    </div>

                    <div class="card-body p-4">

                        <form action="proses_tambah_penempatan_siswa.php" method="POST">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Siswa</label>

                                    <select name="siswa_id" class="form-select" required>
                                        <option value="">-- Pilih Siswa --</option>

                                        <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                            <option value="<?= (int) $s['id']; ?>">
                                                <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelas</label>

                                    <select name="kelas_id" class="form-select" required>
                                        <option value="">-- Pilih Kelas --</option>

                                        <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                            <option value="<?= (int) $k['id']; ?>">
                                                <?= htmlspecialchars($k['nama'] . ' - Tingkat ' . $k['tingkat'] . ' - ' . $k['jurusan']); ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tahun Ajaran</label>

                                    <select name="tahun_ajaran_id" class="form-select" required>
                                        <option value="">-- Pilih Tahun Ajaran --</option>

                                        <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                            <option value="<?= (int) $ta['id']; ?>">
                                                <?= htmlspecialchars($ta['nama']); ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Mulai</label>

                                    <input type="date"
                                           name="tanggal_mulai"
                                           class="form-control"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Selesai</label>

                                    <input type="date"
                                           name="tanggal_selesai"
                                           class="form-control"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status</label>

                                    <select name="status_aktif" class="form-select" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4">

                                    <button type="submit" class="btn btn-primary">
                                        Simpan Data
                                    </button>

                                    <a href="data_penempatan_siswa.php" class="btn btn-secondary">
                                        Kembali
                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>