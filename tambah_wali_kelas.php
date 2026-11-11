<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';
$halaman = basename($_SERVER['PHP_SELF']);

// Data tahun ajaran aktif
$query_tahun = mysqli_query($koneksi, "
    SELECT * FROM t_tahun_ajaran
    WHERE status_aktif = 1
");

// Data kelas aktif
$query_kelas = mysqli_query($koneksi, "
    SELECT * FROM t_kelas
    WHERE status_aktif = 1
");

// Data guru aktif
$query_guru = mysqli_query($koneksi, "
    SELECT * FROM t_guru
    WHERE status_aktif = 1
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

    <!-- SIDEBAR -->
    <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

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
                <a href="data_wali_kelas.php" class="nav-link active">
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

    <!-- KONTEN -->
    <main class="content-main">
        <div class="page-content">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-2">Tambah Wali Kelas</h2>
                <p class="text-secondary mb-0">
                    Selamat datang, <b><?= htmlspecialchars($nama_login); ?></b>
                </p>
            </div>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 fw-bold">Form Tambah Wali Kelas</h5>
                </div>

                <div class="card-body p-4">

                    <form action="proses_tambah_wali_kelas.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($tahun = mysqli_fetch_assoc($query_tahun)) { ?>
                                    <option value="<?= $tahun['id']; ?>">
                                        <?= htmlspecialchars($tahun['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>

                                <?php while ($kelas = mysqli_fetch_assoc($query_kelas)) { ?>
                                    <option value="<?= $kelas['id']; ?>">
                                        <?= htmlspecialchars($kelas['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Guru</label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>

                                <?php while ($guru = mysqli_fetch_assoc($query_guru)) { ?>
                                    <option value="<?= $guru['id']; ?>">
                                        <?= htmlspecialchars($guru['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <hr>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" name="simpan" class="btn btn-primary">
                                Simpan
                            </button>

                            <a href="data_wali_kelas.php" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>