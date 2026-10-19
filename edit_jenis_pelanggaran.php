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

if (!isset($_GET['id'])) {
    header("Location: data_jenis_pelanggaran.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data jenis pelanggaran tidak ditemukan";
    exit;
}

$kategori = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jenis Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h5 class="text-white mb-1">Sistem Informasi</h5>
            <p class="text-white-50 small">Pelanggaran Siswa</p>
            <hr class="text-white">

            <a href="dashboard.php" class="d-block text-white text-decoration-none p-2">Dashboard</a>
            <a href="data_siswa.php" class="d-block text-white text-decoration-none p-2">Data Siswa</a>
            <a href="data_guru.php" class="d-block text-white text-decoration-none p-2">Data Guru</a>
            <a href="data_kelas.php" class="d-block text-white text-decoration-none p-2">Data Kelas</a>
            <a href="data_tahun_ajaran.php" class="d-block text-white text-decoration-none p-2">Tahun Ajaran</a>
            <a href="data_penempatan_siswa.php" class="d-block text-white text-decoration-none p-2">Penempatan Siswa</a>
            <a href="data_wali_kelas.php" class="d-block text-white text-decoration-none p-2">Data Wali Kelas</a>
            <a href="data_kategori_pelanggaran.php" class="d-block text-white text-decoration-none p-2">Kategori Pelanggaran</a>
            <a href="data_jenis_pelanggaran.php" class="d-block text-white text-decoration-none p-2">Jenis Pelanggaran</a>
            <a href="laporan.php" class="d-block text-white text-decoration-none p-2">Laporan</a>
            <a href="about_me.php" class="d-block text-white text-decoration-none p-2">About Me</a>
            <a href="logout.php" class="d-block text-white text-decoration-none p-2">Logout</a>
        </div>

        <!-- Konten -->
        <div class="col-md-9 col-lg-10 ms-auto p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3>Edit Jenis Pelanggaran</h3>
                    <p class="text-muted mb-0">Ubah data jenis pelanggaran siswa.</p>
                </div>

                <span class="text-secondary">
                    Admin: <?= htmlspecialchars($name); ?>
                </span>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Form Edit Jenis Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <form action="proses_edit_jenis_pelanggaran.php" method="POST">

                        <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']); ?>">

                        <div class="mb-3">
                            <label class="form-label">Kategori Pelanggaran</label>

                            <select name="pelanggaran_kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>

                                <?php while ($kat = mysqli_fetch_assoc($kategori)) { ?>
                                    <option value="<?= htmlspecialchars($kat['id']); ?>"
                                        <?= ($data['pelanggaran_kategori_id'] == $kat['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($kat['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kode Pelanggaran</label>
                            <input type="text" name="kode" class="form-control"
                                   value="<?= htmlspecialchars($data['kode']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggaran</label>
                            <input type="text" name="nama" class="form-control"
                                   value="<?= htmlspecialchars($data['nama']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Poin</label>
                            <input type="number" name="poin" class="form-control"
                                   value="<?= htmlspecialchars($data['poin']); ?>" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <select name="status_aktif" class="form-select" required>
                                <option value="1"
                                    <?= ((string)$data['status_aktif'] === '1') ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= ((string)$data['status_aktif'] === '0') ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="data_jenis_pelanggaran.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>