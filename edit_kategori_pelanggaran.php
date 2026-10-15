
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

$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE id='$id'");
$d = mysqli_fetch_assoc($data);

if (!$d) {
    die("Data kategori tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container-fluid">
<div class="row">

    <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
         style="background-color: #172554;">

        <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

        <ul class="nav nav-pills flex-column gap-1">
            <li class="nav-item"><a href="dashboard.php" class="nav-link text-white">Dashboard</a></li>
            <li class="nav-item"><a href="data_siswa.php" class="nav-link text-white">Data Siswa</a></li>
            <li class="nav-item"><a href="data_guru.php" class="nav-link text-white">Data Guru</a></li>
            <li class="nav-item"><a href="data_kelas.php" class="nav-link text-white">Data Kelas</a></li>
            <li class="nav-item"><a href="data_tahun_ajaran.php" class="nav-link text-white">Tahun Ajaran</a></li>
            <li class="nav-item"><a href="data_penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a></li>
            <li class="nav-item"><a href="data_wali_kelas.php" class="nav-link text-white">Data Wali Kelas</a></li>
            <li class="nav-item"><a href="data_kategori_pelanggaran.php" class="nav-link active">Kategori Pelanggaran</a></li>
            <li class="nav-item"><a href="data_jenis_pelanggaran.php" class="nav-link text-white">Jenis Pelanggaran</a></li>
            <li class="nav-item"><a href="laporan.php" class="nav-link text-white">Laporan</a></li>
            <li class="nav-item"><a href="about_me.php" class="nav-link text-white">About Me</a></li>
            <hr class="border-white">
            <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
        </ul>
    </div>

    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h2 class="fw-bold">Edit Kategori Pelanggaran</h2>
            <p class="text-secondary mb-0">Ubah informasi kategori pelanggaran.</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Form Edit Kategori Pelanggaran</h5>
            </div>

            <div class="card-body p-4">
                <form action="proses_edit_kategori_pelanggaran.php" method="POST">

                    <input type="hidden" name="id" value="<?= $d['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= htmlspecialchars($d['nama']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($d['deskripsi']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Aktif</label>
                        <select name="status_aktif" class="form-select" required>
                            <option value="1" <?= $d['status_aktif'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= $d['status_aktif'] == 0 ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="data_kategori_pelanggaran.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="ubah" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>
</div>
</div>
</body>
</html>