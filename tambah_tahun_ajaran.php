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

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')");

    if ($query) {
        header("Location: data_tahun_ajaran.php");
        exit;
    } else {
        $pesan = "Gagal menyimpan data tahun ajaran.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tahun Ajaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
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
                    <a href="data_tahun_ajaran.php" class="nav-link active">
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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- JUDUL HALAMAN -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">Tambah Tahun Ajaran</h2>

                <p class="text-secondary mb-0">
                    Tambahkan data tahun ajaran baru ke dalam sistem.
                </p>
            </div>

            <!-- FORM TAMBAH -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Form Tambah Tahun Ajaran</h5>
                </div>

                <div class="card-body p-4">

                    <?php if (isset($pesan)) { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nama Tahun Ajaran</label>
                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   placeholder="2026/2027"
                                   required>
                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date"
                                       name="tanggal_mulai"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date"
                                       name="tanggal_selesai"
                                       class="form-control"
                                       required>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Aktif</label>

                                <select name="status_aktif" class="form-select" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="data_tahun_ajaran.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan Tahun Ajaran
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>