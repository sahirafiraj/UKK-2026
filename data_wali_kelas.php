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

$query = mysqli_query($koneksi, "
    SELECT
        w.*,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        g.nama AS nama_guru
    FROM t_wali_kelas w
    LEFT JOIN t_tahun_ajaran ta ON w.tahun_ajaran_id = ta.id
    LEFT JOIN t_kelas k ON w.kelas_id = k.id
    LEFT JOIN t_guru g ON w.guru_id = g.id
    ORDER BY w.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.4/css/dataTables.dataTables.min.css">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR ADMIN -->
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

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
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link active">Data Wali Kelas</a>
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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">Kelola Data Wali Kelas</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($name); ?></b>
                </p>
            </div>

            <!-- TABEL DATA WALI KELAS -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <h5 class="mb-0 fw-bold">Daftar Data Wali Kelas</h5>

                        <div class="d-flex gap-2">
                            <a href="dashboard.php" class="btn btn-secondary btn-sm">
                                Kembali ke Dashboard
                            </a>

                            <a href="tambah_wali_kelas.php" class="btn btn-primary btn-sm">
                                Tambah Wali Kelas
                            </a>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelWaliKelas" class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Kelas</th>
                                    <th>Guru</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;

                                if ($query && mysqli_num_rows($query) > 0) {
                                    while ($data = mysqli_fetch_assoc($query)) {
                                ?>

                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= htmlspecialchars($data['tahun_ajaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['tanggal_mulai'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['tanggal_selesai'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php if (($data['status_aktif'] ?? 0) == 1) { ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="edit_wali_kelas.php?id=<?= urlencode($data['id']); ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a href="hapus_wali_kelas.php?id=<?= urlencode($data['id']); ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus data wali kelas ini?')">
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <?php
                                    }
                                } else {
                                ?>

                                <tr>
                                    <td colspan="8" class="text-center">
                                        Belum ada data wali kelas.
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/3.1.4/js/dataTables.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new DataTable('#tabelWaliKelas', {
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        layout: {
            topStart: 'info',
            bottom: 'paging',
            bottomStart: null,
            bottomEnd: null
        },
        language: {
            search: 'Cari:',
            searchPlaceholder: 'Cari data wali kelas...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data wali kelas',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya'
            }
        },
        columnDefs: [{
            targets: 7,
            orderable: false,
            searchable: false
        }]
    });
});
</script>

</body>
</html>