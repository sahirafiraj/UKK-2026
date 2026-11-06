
<?php
session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';
$halaman = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Siswa - Sistem Pelanggaran</title>

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
                    <a href="dashboard.php" class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link active">
                        Data Siswa
                    </a>
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
                    <h2 class="fw-bold">Tambah Siswa</h2>
                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>
                </div>

                <div class="bg-white rounded shadow-sm mb-4">

                    <div class="border-bottom px-4 py-3">
                        <h5 class="fw-bold mb-0">Formulir Data Siswa</h5>
                    </div>

                    <div class="p-4">

                        <form action="proses_tambah_siswa.php" method="POST">

                            <div class="mb-3">
                                <label class="form-label">NIS</label>
                                <input
                                    type="text"
                                    name="nis"
                                    class="form-control"
                                    maxlength="10"
                                    pattern="[0-9]{10}"
                                    title="NIS harus terdiri dari tepat 10 angka."
                                    inputmode="numeric"
                                    placeholder="Masukkan NIS"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">NISN</label>
                                <input
                                    type="text"
                                    name="nisn"
                                    class="form-control"
                                    maxlength="10"
                                    pattern="[0-9]{10}"
                                    title="NISN harus terdiri dari tepat 10 angka."
                                    inputmode="numeric"
                                    placeholder="Masukkan NISN"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Siswa</label>
                                <input
                                    type="text"
                                    name="nama"
                                    class="form-control"
                                    placeholder="Masukkan nama lengkap siswa"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input
                                    type="date"
                                    name="tanggal_lahir"
                                    class="form-control"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea
                                    name="alamat"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Masukkan alamat siswa"
                                    required
                                ></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Status Siswa</label>
                                <select name="status_aktif" class="form-select" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" name="simpan" class="btn btn-primary">
                                    Simpan Data
                                </button>

                                <a href="data_siswa.php" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

                <p class="text-secondary text-center mt-4 small">
                    © 2026 Sistem Pelanggaran Siswa
                </p>

            </div>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
