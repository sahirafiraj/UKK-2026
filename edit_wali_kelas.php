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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_wali_kelas.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query($koneksi, "SELECT * FROM t_wali_kelas WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data wali kelas tidak ditemukan.";
    exit;
}

$tahun = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");
$kelas = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY id DESC");
$guru = mysqli_query($koneksi, "SELECT * FROM t_guru ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wali Kelas</title>
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
            <li class="nav-item"><a href="data_wali_kelas.php" class="nav-link active">Data Wali Kelas</a></li>
            <li class="nav-item"><a href="data_kategori_pelanggaran.php" class="nav-link text-white">Kategori Pelanggaran</a></li>
            <li class="nav-item"><a href="data_jenis_pelanggaran.php" class="nav-link text-white">Jenis Pelanggaran</a></li>
            <li class="nav-item"><a href="laporan.php" class="nav-link text-white">Laporan</a></li>
            <li class="nav-item"><a href="about_me.php" class="nav-link text-white">About Me</a></li>
            <hr class="border-white">
            <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
        </ul>
    </div>

    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h2 class="fw-bold">Edit Wali Kelas</h2>
            <p class="text-secondary mb-0">Ubah data penugasan wali kelas.</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Form Edit Wali Kelas</h5>
            </div>

            <div class="card-body p-4">
                <form action="proses_edit_wali_kelas.php?id=<?= $id; ?>" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-select" required>
                            <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                <option value="<?= $ta['id']; ?>"
                                    <?= $data['tahun_ajaran_id'] == $ta['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($ta['nama']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $k['id']; ?>"
                                        <?= $data['kelas_id'] == $k['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($k['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-select" required>
                                <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                    <option value="<?= $g['id']; ?>"
                                        <?= $data['guru_id'] == $g['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($g['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_mulai']); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_selesai']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                                <option value="0" <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="data_wali_kelas.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>

    </main>
</div>
</div>
</body>
</html>