
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

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori Pelanggaran</title>
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
            <h2 class="fw-bold">Data Kategori Pelanggaran</h2>
            <p class="text-secondary mb-0">Kelola kategori pelanggaran siswa.</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Daftar Kategori Pelanggaran</h5>

                <a href="tambah_kategori_pelanggaran.php" class="btn btn-primary">
                    + Tambah Kategori
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th width="160">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;

                            while ($d = mysqli_fetch_assoc($data)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($d['nama']); ?></td>
                                <td><?= htmlspecialchars($d['deskripsi']); ?></td>
                                <td>
                                    <?php if ($d['status_aktif'] == 1) { ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="edit_kategori_pelanggaran.php?id=<?= $d['id']; ?>"
                                       class="btn btn-warning btn-sm">Edit</a>

                                    <a href="hapus_kategori_pelanggaran.php?id=<?= $d['id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>

                            <?php if (mysqli_num_rows($data) == 0) { ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary">
                                    Belum ada data kategori pelanggaran.
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>
</div>
</body>
</html>
